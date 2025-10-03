# 🔐 JWT Tool — VerifiedPress / Press ID Badge

This module provides a secure, audit-grade implementation of JSON Web Token (JWT) creation and verification using asymmetric RSA key pairs. Designed for coalition-grade identity workflows, it enables badge issuance, token introspection, and public-key verifiability across decentralized trust networks.

## 📦 Repository
**GitHub:** [VerifiedPress/press-id-badge/tools/jwt](https://github.com/VerifiedPress/press-id-badge/tools/jwt)

---

## ✨ Features

- ✅ RSA-based JWT signing (`RS256`)
- 🔍 Public key verification for decentralized trust
- ⏳ Expiration and introspection support
- 🧩 Modular integration with Press ID Badge workflows
- 📜 Audit-grade logging for token lifecycle events


## 🧠 Use Cases

- Press badge issuance with verifiable expiration
- Coalition onboarding with public-key trust
- Modular token introspection for civic documentation
- Secure API access for surplus-backed workflows

---

## 🛡️ Security Notes

- Always store private keys securely (e.g., environment variables, vaults)
- Publish public keys via JWKS or coalition registry for verification
- Use short-lived tokens for sensitive operations

---

## 🤝 Coalition Integration

This tool is part of the [Press ID Badge](https://github.com/VerifiedPress/press-id-badge) suite under the VerifiedPress initiative. It supports modular overlays for surplus-backed solvency, civic documentation, and generational resilience.

## 🔐 JWT Verification for Login Identity (Public Key Only)
Since this token includes an expiration, and the host website should check the expiration, you can save this token on your mobile device and use
See the [README.md in the public folder](../../public/README.md)

### 🧭 Core Principle
JWTs are **signed by a trusted issuer** (e.g., your backend or badge authority) using a **private key**, and **verified by any client** (mobile, web, kiosk) using the **public key**. Mobile devices **never sign** — they only **receive and verify**.

---

## 📲 Mobile Login Flow (Public Key Verification Only)

### 1. **User Authenticates via Trusted Channel**
- Mobile app sends credentials (e.g., email + OTP, biometric, badge scan) to your backend.
- Backend authenticates and issues a JWT signed with its **private RSA key**.

### 2. **JWT Delivered to Mobile Client**
- The signed JWT is returned to the mobile app.
- It includes claims like:
  ```json
  {
    "did": "did:web:localhost:8082",
    "role": "admin",
    "exp": 1696358400
  }
  ```

### 3. **Mobile Verifies JWT Using Public Key**
- The app uses the **public key** (bundled or fetched from a JWKS endpoint) to verify:
  - Signature validity (`RS256`)
  - Expiration (`exp`)
  - Audience (`aud`) or issuer (`iss`) if used

```js
const jwt = require('jsonwebtoken');
const publicKey = getPublicKey(); // from JWKS or bundled PEM

try {
  const decoded = jwt.verify(token, publicKey, { algorithms: ['RS256'] });
  console.log('Valid badge:', decoded.badge);
} catch (err) {
  if (err.name === 'TokenExpiredError') {
    alert('Session expired. Please log in again.');
  } else {
    alert('Invalid badge or tampered token.');
  }
}
```

---

## 🛡️ Coalition-Grade Enhancements

- **JWKS Endpoint**: Host your public key(s) at `https://verifiedpress.com/.well-known/jwks.json` for dynamic key rotation and mobile trust.
- **Short-Lived Tokens**: Use `exp` to enforce session boundaries (e.g., 15 min).
- **Token Introspection**: Optionally validate token status server-side for revoked or suspended badges.
- **Offline Verification**: Embed public key in app for offline badge validation (e.g., at events or checkpoints).

---

## 🧩 Use Cases

- ✅ Press badge verification at civic events
- 📸 Autonomous drone access control
- 🧾 Coalition member check-in via QR code
- 🛂 Surplus-backed identity workflows with audit-grade optics


## 🧾 Decoded JWT Payload Explained
This decoded JWT payload represents a minimal, audit-grade identity assertion. Here's a breakdown of each field and its procedural significance:

```json
{
  did: "did:web:localhost:8082",
  role: "admin",
  iat: 1759481162,
  exp: 1759484762
}
```

### 🔹 `did: "did:web:localhost:8082"`
- **Decentralized Identifier (DID)** using the `did:web` method.
- Resolves to `http://localhost:8082/.well-known/did.json`, which should contain the public key and metadata for verification.
- In production, this would point to a GitHub Pages domain or coalition registry (e.g., `did:web:username.github.io`).
- This field anchors the identity claim to a verifiable source controlled by the user.

### 🔹 `role: "admin"`
- Defines the **authorization scope** of the token holder.
- Can be used by the verification host (`login.php`) to gate access to admin-only features, press badge issuance, or surplus-backed dashboards.
- Coalition-grade systems may map this to procedural authority (e.g., badge issuer, intake coordinator).

### 🔹 `iat: 1759481162`
- **Issued At** timestamp in Unix time (seconds since epoch).
- Represents when the JWT was created — useful for audit logs and replay protection.
- In human-readable format:  
  🕒 `new Date(1759481162 * 1000)` → `Tue, Oct 3, 2025, 04:46:02 AM EDT`

### 🔹 `exp: 1759484762`
- **Expiration** timestamp — the token becomes invalid after this time.
- Enforces session boundaries and limits exposure if the token is leaked.
- In human-readable format:  
  🕒 `new Date(1759484762 * 1000)` → `Tue, Oct 3, 2025, 05:46:02 AM EDT`

---

## 🧠 Strategic Implications

- This payload is **self-contained**: it doesn’t require a centralized login database.
- Verification is **stateless**: any host with the public key can validate the token.
- The user retains full control over identity, role, and recovery — especially when paired with HSM signing and DID publishing.
