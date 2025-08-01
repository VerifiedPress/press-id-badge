window.onload = async function() {
  const body = document.body;
  const html = document.documentElement;

  // Get the maximum dimensions needed
  const width = Math.max(
    body.scrollWidth,
    body.offsetWidth,
    html.clientWidth,
    html.scrollWidth,
    html.offsetWidth
  );

  const height = Math.max(
    body.scrollHeight,
    body.offsetHeight,
    html.clientHeight,
    html.scrollHeight,
    html.offsetHeight
  );
  // Resize the window to fit content + padding
  window.resizeTo(width + 32, height + 32); // Add buffer for borders

  chrome.storage.local.get(["dialog","didValue", "message"], (result) => {
    if (result.didValue) {
      document.getElementById("did").value = result.didValue;
      document.getElementById("didText").value = result.didValue;
    }
    if (result.message) {
      document.getElementById("messageText").value = result.message;
    }
    if (result.dialog === "settings") {
      document.getElementById("signing").style.display = "none";
      document.getElementById("settings").style.display = "block";
    } else if (result.dialog === "signing") {
      document.getElementById("settings").style.display = "none";
      document.getElementById("signing").style.display = "block";
    } else {
      document.getElementById("settings").style.display = "block";
      document.getElementById("signing").style.display = "none";
    }
  });

  document.getElementById("privateKeyFile")?.addEventListener("change", (event) => {
    const file = event.target.files?.[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = async () => {
      const key = typeof reader.result === "string" ? reader.result : "";
      try {
        document.getElementById("privateKeyPEM").value = key;
        return false;
      } catch (err) {
        console.error("❌ Failed to import private key:", err);
      }
    };
    reader.readAsText(file);  // Make sure this is .readAsText()
  });
}

document.addEventListener("DOMContentLoaded", () => {
  document.getElementById("save")?.addEventListener("click", async () => {
    const didVal = document.getElementById("did")?.value || "";

    chrome.storage.local.set({
        didValue: didVal
    });

    chrome.runtime.sendMessage(
      {
          command: "SAVE_USER_PARAMS",
          did: didVal
      },
      (res) => {
        if (chrome.runtime.lastError) {
          //console.log(chrome.runtime.lastError);
        }
        
        // Broadcast DID update to active tab
        chrome.tabs.query({ active: true, currentWindow: true }, (tabs) => {
          if (!tabs.length) {
            console.warn("No active tab available for UPDATE_PUBLIC_DID.");
            return;
          }

          chrome.tabs.sendMessage(tabs[0].id, {
            type: "UPDATE_PUBLIC_DID",
            didVal
          }, (response) => {
            if (chrome.runtime.lastError) {
              //console.error("Content script message error:", chrome.runtime.lastError.message);
            } else {
              console.log("Content script acknowledged DID update:", response);
            }
          });
        
          window.close(); // ✅ Close after confirmed message resolution
          return true; // ⬅️ Critical: keeps message channel open
        });
      }
    );
  })

  document.getElementById("signButton")?.addEventListener("click", async () => {
    const key = document.getElementById("privateKeyPEM")?.value;
    if (key.length === 0) {
      alert("Missing private key?");
      return false;
    }
    const message = document.getElementById("messageText")?.value;
    await chrome.runtime.sendMessage({ command: "SIGN_MESSAGE", key: key, message: message });

    chrome.storage.local.set({ dialog: "settings" });
    // ✅ Close the popup
    window.close();
  });
});