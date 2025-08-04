/**
 * File: content.js
 * 
 * Extensions use content scripts to read and modify the content of the page. 
 * When a user visits a Chrome API reference page, the extension's content script will update the page 
 * with the tip of the day. It sends a message to request the tip of the day from the service worker.
 * 
 * Start by declaring the content script in the manifest and add the match pattern corresponding to the 
 * Chrome API reference documentation.
 */

window.addEventListener("message", async function (event) {
  if (event.source !== window) return;
  if (!event.data || typeof event.data !== "object") return; 

  if (event.data.command === "GET_USER_PARAMS") {
        browser.storage.sync.get("pressid_credentials").then(function(res, err){
          if (res.pressid_credentials) {
            if (document.getElementById("did")) {
              document.getElementById("did").value = res.pressid_credentials.did;
            }
            browser.storage.local.set({
              dialog: "settings",
              didValue: res.pressid_credentials.did,
              keyValue: res.pressid_credentials.privateKey
            });
          } else {
            browser.storage.local.set({
              dialog: "settings",
              didValue: "",
              keyValue: ""
            });
          }
            browser.runtime.sendMessage({ command: "SHOW_POPUP_DIALOG" });
        });
        return true; // ⬅️ Critical: keeps message channel open
  } else if (event.data.command === "SHOW_SIGNING_DIALOG") {
      browser.storage.local.set({ dialog: "signing", message: event.data.message });
    await browser.runtime.sendMessage({command: "SHOW_POPUP_DIALOG" });
  }

});

browser.runtime.onMessage.addListener((message, sender, sendResponse) => {
  if (message.type === "SIGNATURE_BROADCAST") {
    window.postMessage({
      type: "SIGNATURE_READY",
      signature: message.signature
    }, "*");

    sendResponse({ received: true });
  } else if (message.type = "UPDATE_PUBLIC_DID") {
      if (document.getElementById("did")) {
          document.getElementById("did").value = message.didVal;
      }
      sendResponse({ received: true });
  } else if (message.type === "initiateUpload") {
      const input = document.createElement("input");
      input.type = "file";
      input.accept = ".pem";
      input.style.display = "none";

      input.addEventListener("change", () => {
        const file = input.files?.[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = () => {
          const key = typeof reader.result === "string" ? reader.result : "";
          const target = document.getElementById("privateKeyPEM");
          if (target) {
            target.value = key;
          } else {
            console.warn("⚠️ #privateKeyPEM not found.");
          }
        };
        reader.readAsText(file);
      });

      document.body.appendChild(input);
      input.click();
      document.body.removeChild(input);
  }
});
