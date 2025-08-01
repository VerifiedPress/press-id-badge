/**
 * File: background.js (aka service worker)
 * 
 * Service workers don't have direct access to the window object and therefore cannot use window.localStorage 
 * to store values. Also, service workers are short-lived execution environments; 
 * they get terminated repeatedly throughout a user's browser session, 
 * which makes them incompatible with global variables. 
 * 
 * Instead, use chrome.storage.local which stores data on the local machine.
 * 
 * All event listeners need to be statically registered in the global scope of the service worker. 
 * In other words, event listeners shouldn't be nested in async functions. 
 * This way Chrome can ensure that all event handlers are restored in case of a service worker reboot.
 */

async function importPublicKey(jwk) {
  return await crypto.subtle.importKey(
    "jwk",
    jwk,
    {
      name: "RSASSA-PKCS1-v1_5",
      hash: { name: "SHA-256" }
    },
    false,
    ["verify"]
  );
}

async function verifySignatureJwK(publicKeyJwk, message, signature) {
  const pubKey = await importPublicKey(publicKeyJwk);
  return await verifySignature(pubKey, message, signature);
}

async function verifySignature(publicKey, message, signatureBase64url) {
  const messageBytes = new TextEncoder().encode(message);
  const signatureBytes = Uint8Array.from(
    atob(signatureBase64url.replace(/-/g, "+").replace(/_/g, "/")),
    c => c.charCodeAt(0)
  );

  const isValid = await crypto.subtle.verify(
    { name: "RSASSA-PKCS1-v1_5" },
    publicKey,
    signatureBytes,
    messageBytes
  );

  console.log("Is signature valid?", isValid);
  return isValid;
}


function str2ab(str) {
  const binary = atob(str);
  const bytes = new Uint8Array(binary.length);
  for (let i = 0; i < binary.length; i++) {
    bytes[i] = binary.charCodeAt(i);
  }
  return bytes.buffer;
}


async function importRsaPrivateKey(pem) {
  const cleanedPem = pem
    .replace(/-----BEGIN PRIVATE KEY-----/, "")
    .replace(/-----END PRIVATE KEY-----/, "")
    .replace(/\r?\n|\r/g, "")
    .trim();

  const binaryDer = str2ab(cleanedPem);

  return crypto.subtle.importKey(
    "pkcs8",
    binaryDer,
    {
      name: "RSASSA-PKCS1-v1_5",
      hash: { name: "SHA-256" }
    },
    false,
    ["sign"]
  );
}

async function signMessage(privateKeyPem, message) {
  const key = await importRsaPrivateKey(privateKeyPem);
  const encodedMsg = new TextEncoder().encode(message);

  const signature = await crypto.subtle.sign(
    {
      name: "RSASSA-PKCS1-v1_5",
      hash: { name: "SHA-256" }
    },
    key,
    encodedMsg
  );

  const hexSignature = Array.from(new Uint8Array(signature))
    .map(b => b.toString(16).padStart(2, "0"))
    .join("");

  return hexSignature;
}

async function performCredentialSigning(key, message) {
  return new Promise(async (resolve,reject) => {
    try {
      const signature = await signMessage(key, message);
      resolve({
        signed: true,
        timestamp: Date.now(),
        signature
      });
    } catch (err) {
      reject({
        signed: false,
        timestamp: Date.now(),
        error: err.message
      });
    }
  })
}

chrome.runtime.onMessage.addListener(async(msg, sender, sendResponse) => {
  const command = msg.command || msg.action || msg.type || "";

  switch(command) {
    case "SAVE_USER_PARAMS": 
      {
        const { did } = msg;
        
        // Save credentials to sync storage
        chrome.storage.sync.set({
          pressid_credentials: { did }
        }).then(() => {
          sendResponse({ saved: true });
        }).catch((err) => {
          console.error("Storage save failed:", err);
          sendResponse({ saved: false, error: err.message });
        });

        return true; // 🛡️ Keeps message channel open
      }
    case "GET_USER_PARAMS":
      {
        chrome.storage.sync.get("pressid_credentials").then(function(res, err){
          if (res.runtime.lastError) {
            console.error(err.message);
          } else {
            sendResponse(res);
          }
        });
        return true; // ✅ This keeps the port alive
      }
    case "SHOW_POPUP_DIALOG":
      {
        const popupUrl = browser.runtime.getURL("popup/popup.html");
        await browser.windows.create({
          url: popupUrl,
          type: "popup",
          width: 310, // Initial guess
          height: 400
        });
        return true; // ✅ This keeps the port alive
      }
    case "SIGN_MESSAGE":
      {
        (async () => {
          const { key, message } = msg;

          if (key && message) {
            try {
              const signature = await performCredentialSigning(key, message);
              sendResponse({ signature });

              // 🔄 Broadcast to active tab
              chrome.tabs.query({ active: true, currentWindow: true }, (tabs) => {
                if (tabs.length === 0) {
                  console.warn("No active tab found for signature broadcast.");
                  return;
                }

                chrome.tabs.sendMessage(
                  tabs[0].id,
                  {
                    type: "SIGNATURE_BROADCAST",
                    signature,
                    metadata: {
                      timestamp: Date.now(),
                      origin: chrome.runtime?.id || "unknown-extension"
                    }
                  },
                  (response) => {
                    if (chrome.runtime.lastError) {
                      console.warn("Broadcast failed:", chrome.runtime.lastError.message);
                    } else {
                      console.log("📨 Broadcast response:", response);
                    }
                  }
                );
              });
            } catch (err) {
              sendResponse({ error: err.message });
            }
          } else {
            sendResponse({ error: `Missing key=${key} and/or message=${message}` });
          }
        })();

        return true; // 🛡️ Keeps the message channel open
      }
    case "VERIFY_SIGNATURE":
      {
        const { jwk, message, signature } = msg;
        if (jwk && message && signature) {
          const verified = verifySignatureJwK(jwk, message, signature);
          sendResponse({ verified: verified });
        }
        return true; // ✅ This keeps the port alive
      }
    default:
      console.log(`unknown command: ${command}`);
  }
});

chrome.runtime.onInstalled.addListener((event) => {
});

