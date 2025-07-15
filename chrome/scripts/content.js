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
        chrome.storage.sync.get("pressid_credentials").then(function(res, err){
          document.getElementById("did").value = res.pressid_credentials.did;
          chrome.storage.local.set({
            dialog: "settings",
            didValue: res.pressid_credentials.did,
            keyValue: res.pressid_credentials.privateKey
          });
          chrome.runtime.sendMessage({ command: "SHOW_POPUP_DIALOG" });
        });
        return true; // ⬅️ Critical: keeps message channel open
  } else if (event.data.command === "SHOW_SIGNING_DIALOG") {
    chrome.storage.local.set({ dialog: "signing", message: event.data.message });
    await chrome.runtime.sendMessage({command: "SHOW_POPUP_DIALOG" });
  } 

});

chrome.runtime.onMessage.addListener((message, sender, sendResponse) => {
  if (message.type === "SIGNATURE_BROADCAST") {
    window.postMessage({
      type: "SIGNATURE_READY",
      signature: message.signature
    }, "*");

    sendResponse({ received: true });
  }
});
