window.onload = async function() {
    chrome.storage.local.get(["dialog","didValue", "keyValue", "message"], (result) => {
      if (result.didValue) {
        document.getElementById("did").value = result.didValue;
        document.getElementById("didText").value = result.didValue;
      }
      if (result.keyValue) {
        document.getElementById("privateKey").value = result.keyValue;
        document.getElementById("privateKeyPEM").value = result.keyValue;
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
        document.getElementById("settings").style.display = "none";
      }
    });
}

document.getElementById("save").addEventListener("click", async () => {
    const didVal = document.getElementById("did")?.value || "";
    const keyVal = document.getElementById("privateKey")?.value || "";

    chrome.storage.local.set({
        didValue: didVal,
        keyValue: keyVal
    });

    chrome.runtime.sendMessage(
        {
            command: "SAVE_USER_PARAMS",
            did: didVal,
            privateKey: keyVal
        },
        (res) => {
            if (chrome.runtime.lastError) {
                console.log(chrome.runtime.lastError);
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
                  console.error("Content script message error:", chrome.runtime.lastError.message);
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


document.getElementById("signButton").addEventListener("click", async () => {
    const key = document.getElementById("privateKeyPEM").value;
    const message = document.getElementById("messageText").value;
    await chrome.runtime.sendMessage({ command: "SIGN_MESSAGE", key: key, message: message });
    chrome.storage.local.set({ dialog: "settings" });
    // ✅ Close the popup
    window.close();
});