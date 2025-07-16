# Press ID Badge Browser Extension
The overview for a browser extension. Read the [whitepaper](./whitepaper.pdf).

![sett1ngs](./images/screenshot-1.png) and ![signing](./images/screenshot-4.png)

Your Decentralized Identification (DiD) credentials are created using the verifiedpress.org command-line interface utility.

**Browser Extension Compatibility Overview**

| Browser     | Extension Architecture       | Chrome Extension Compatibility | Notes |
|-------------|------------------------------|-------------------------------|-------|
| [**Chrome**](src/chrome/README.md)   | WebExtension (Chromium-native) | ✅ Native                     | Full support for `manifest.json`, `chrome.*` APIs |
| [**Edge**](src/edge/README.md)     | Chromium-based Extension       | ✅ Native                     | Uses same engine as Chrome; extensions install directly |
| [**Opera**](src/opera/README.md)    | Chromium-based Extension       | ✅ Native                     | Extensions from Chrome Web Store work seamlessly |
| [**Brave**](src/brave/README.md)    | Chromium-based Extension       | ✅ Native                     | Extension APIs and behavior match Chrome closely |
| [**Vivaldi**](src/vivaldi/README.md)  | Chromium-based Extension       | ✅ Native                     | Custom UI shell, but supports all Chrome extensions |
| [**Firefox**](src/firefox/README.md)  | WebExtension (Gecko engine)    | ⚠️ Mostly Compatible          | Uses `browser.*` namespace; good Chrome API coverage but not perfect |
| [**Safari**](src/safari/README.md)   | Safari App Extension           | ⚠️ Partial Compatibility      | Requires Xcode packaging; some `chrome.*` APIs via polyfill or rewrite |


## 📰 Overview: Press ID Badge Browser Extension
The **Press ID Badge** browser extension—designed to integrate seamlessly with credential verification platform of VerifiedPress.org.

### **Purpose**
The **Press ID Badge** extension empowers journalists, editors, and contributors by securely displaying and verifying their decentralized identity (DID) and press credentials directly within public-facing web pages. This enables audience trust, attribution clarity, and interoperability with credential-aware publishing platforms.

---

### **Key Features**
- ✅ **Credential Injection**: Auto-populate verified user data (e.g., name, DID, outlet) into read-only fields on forms, like `userid`, using `content.js`
- 🛡️ **Tamper-Proof Attribution**: Credentials are cryptographically signed and surfaced in `index.php` via extension routing
- 🔁 **Modular Messaging**: Coordinated communication between `content.js`, `background.js`, and `popup.js` using structured `sendMessage()` and `postMessage()` flows
- 🔍 **Audit Trail Ready**: Enables credential tracking for content submissions, edits, and claim verification
- 🖥️ **Popup Verification UI**: Allows users to inspect, update, or revoke credentials securely without interfering with site DOM

---

### **Architecture Highlights**
| Component       | Role                                                   |
|----------------|--------------------------------------------------------|
| `index.php`     | Public-facing page receiving badge data via `postMessage` |
| `content.js`    | DOM injector and relay for verified credentials        |
| `background.js` | Message router, credential fetcher, and permission gatekeeper |
| `popup.js`      | UI interface for credential inspection and update      |

- **Messaging Flow**:  
  `index.php` ←→ `content.js` ←→ `background.js` ←→ `popup.js`  
  → `window.postMessage` used for external page ↔ extension script  
  → `chrome.runtime.sendMessage` coordinates internal component flow

---

### **Use Cases**
- 🖋️ **Verified Journalism**: Automatically inject badge-linked DID for authorship verification  
- 🧠 **AI Content Attribution**: Track which entity reviewed/generated content before publication  
- 🧾 **Decentralized Press Registry**: Sync with VerifiedPress.org for credential proofs and DID registration

### 🧩 Browser-Specific Architecture Adjustments

#### ⚙️ Chromium-Based Browsers (Chrome, Edge, Brave, Vivaldi, Opera)
- Full support for `manifest.json` and `chrome.*` APIs
- Seamless `chrome.runtime.sendMessage()` routing across `background.js`, `popup.js`, and `content.js`
- Can inject `content.js` into public pages like `index.php` for credential population
- Supports modular messaging flow and storage sync

#### 🦊 Firefox (Gecko Engine)
- Uses `browser.*` namespace but supports most Chrome-style APIs via WebExtension model
- Requires slight syntax adjustments (`chrome` → `browser`) and promise-based messaging
- CSP directives may be stricter—inline scripts often blocked without `sha256` hashes
- `postMessage` still viable across public→`content.js`, but relay logic needs testing

#### 🍏 Safari (WebKit / App Extension)
- Requires packaging via Xcode as an App Extension
- Does **not** support standard `manifest.json` directly
- Messaging works via background page → popup, but `content.js` access to page DOM requires special configuration
- `window.postMessage` may require bridging logic or native support toggles
- Limited Chrome API support—polyfills needed for structured messaging

### 🔄 Architectural Best Practices by Platform
- **Abstract messaging layer** with fallback for `browser.runtime.*` vs `chrome.runtime.*`
- **Context detection** to handle storage or event listeners differently (especially popup lifecycle)
- **Permission planning** for CSP compliance and tab/page access
- **Packaging flows** aligned with each browser’s dev tools (e.g., Xcode for Safari, zip for Firefox AMO, CRX for Chrome)



## 🔒 Security Considerations

Since you’re laser-focused on CSP and secure workflows, remember:
- Always **validate incoming messages** to prevent abuse or spoofing.
- Avoid passing sensitive data unless using secure channels.
- Consider **Manifest V3 service worker context** if transitioning from traditional background pages.

---

## Training
[Build a Chrome Extension – Course for Beginners](https://www.youtube.com/watch?v=0n809nd4Zu4)


# 🛡️ EOL Doctrine: *Perpetual Relevance in Useful Software*

> **End of Life does not exist for software that remains useful.**

I reject the notion that software must expire on a schedule divorced from its utility. When a system continues to serve a purpose—securely, functionally, and ethically—it deserves active preservation rather than forced obsolescence.

- 🧬 **Continuity by Purpose:** Utility should dictate longevity—not vendor timelines or hype cycles.
- 🛠️ **Architect for Immortality:** Modular design, transparent dependencies, and community ownership enable sustainable use beyond corporate roadmap limits.
- 🌐 **Decentralized Preservation:** When empowered by open standards and distributed governance, software lives as long as its users will it to.
- 🔁 **Evolution over Erosion:** Extensions, polyfills, and community forks are signs of vitality—not decay.

**Patrick O. Ingle**  
July 4, 2025
