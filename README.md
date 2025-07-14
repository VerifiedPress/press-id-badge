# PressID Badge Browser Plugin (aka extension)
The overview for a browser extension

![popup.html](./images/popup.png)


**Browser Extension Compatibility Overview**

| Browser     | Extension Architecture       | Chrome Extension Compatibility | Notes |
|-------------|------------------------------|-------------------------------|-------|
| [**Chrome**](chrome/README.md)   | WebExtension (Chromium-native) | ✅ Native                     | Full support for `manifest.json`, `chrome.*` APIs |
| **Edge**     | Chromium-based Extension       | ✅ Native                     | Uses same engine as Chrome; extensions install directly |
| **Opera**    | Chromium-based Extension       | ✅ Native                     | Extensions from Chrome Web Store work seamlessly |
| **Brave**    | Chromium-based Extension       | ✅ Native                     | Extension APIs and behavior match Chrome closely |
| **Vivaldi**  | Chromium-based Extension       | ✅ Native                     | Custom UI shell, but supports all Chrome extensions |
| **Firefox**  | WebExtension (Gecko engine)    | ⚠️ Mostly Compatible          | Uses `browser.*` namespace; good Chrome API coverage but not perfect |
| **Safari**   | Safari App Extension           | ⚠️ Partial Compatibility      | Requires Xcode packaging; some `chrome.*` APIs via polyfill or rewrite |

## 🔒 Security Considerations

Since you’re laser-focused on CSP and secure workflows, remember:
- Always **validate incoming messages** to prevent abuse or spoofing.
- Avoid passing sensitive data unless using secure channels.
- Consider **Manifest V3 service worker context** if transitioning from traditional background pages.

---

## Training
[Build a Chrome Extension – Course for Beginners](https://www.youtube.com/watch?v=0n809nd4Zu4)

