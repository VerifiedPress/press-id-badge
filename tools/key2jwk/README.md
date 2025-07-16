# 🔐 Public Key to JWK Conversion Tool

This tool converts an RSA public key (PEM format) into a compliant JSON Web Key (JWK), suitable for DID configuration and signature verification workflows.

Designed for use in decentralized identity systems, the JWK output can be directly embedded into a `.well-known/did.json` file for DID resolution and credential signing.

---

## 🧩 Features

- Parses RSA public key from `.pem` format
- Extracts Modulus and Exponent using OpenSSL
- Outputs JWK in `RS256` format
- Supports integration with `.well-known/did.json`
- CLI-friendly and CSP-compliant for browser extension toolchains

---

## 🚀 Quick Start

1. **Install dependencies**  
   *(No packages required unless you're wiring in validation/formatting)*

2. **Place your public key**  
   Put your PEM-formatted public key at:  
   `./public/public_key.pem`

3. **Run the converter**

   ```sh
   npm install
   npm start
   ```

   This runs `index.js`, generates a JWK, and prints the JSON output.

---

## 📂 Sample JWK Output

```json
{
  "kty": "RSA",
  "alg": "RS256",
  "use": "sig",
  "n": "...",  // base64url-encoded modulus
  "e": "AQAB"  // base64url-encoded exponent (65537)
}
```

---

## 📎 Integration with `.well-known/did.json`

You can now embed the JWK into your DID document like this:

```json
{
  "@context": "https://www.w3.org/ns/did/v1",
  "id": "did:web:yourdomain.org",
  "verificationMethod": [
    {
      "id": "did:web:yourdomain.org#key-0",
      "type": "JsonWebKey2020",
      "controller": "did:web:yourdomain.org",
      "publicKeyJwk": {
        "kty": "RSA",
        "e": "AQAB",
        "n": "..."
      }
    }
  ]
}
```

Save this as:
```sh
public/.well-known/did.json
```

Your DID is now verifiable using the published RSA key.

---
## The DID (Decentralized Identifier) as the kid (Key ID) in a JWK

Using a DID (Decentralized Identifier) as the kid (Key ID) in a JWK is a clever way to tightly couple the key with its identity context—especially in decentralized systems. Here's how it works and what to consider:

### 🧩 Why Use a DID as kid?
- Globally Unique: DIDs are designed to be unique across systems, making them ideal identifiers.
- Self-describing: A DID can point to a DID Document that contains verification methods, including the JWK itself.
- Interoperability: Many decentralized identity frameworks (like DID-JWK or DID-Key) support embedding JWKs directly in DID Documents.

---

### ✅ When to Use a Custom DID (e.g. `did:web`, `did:key`, `did:ion`, etc.)

If the user already has a DID (say, `did:web:example.com` or `did:ion:xyz...`) and wants to use a JWK for signing content, you can:

- Embed the JWK in the DID Document under `verificationMethod`
- Reference it using a fragment like `did:web:example.com#key-1`
- Set the `kid` in the JWK to match that fragment (`kid: "did:web:example.com#key-1"`)

This approach allows the JWK to be **anchored to the user's existing DID**, maintaining continuity across identity systems.

---

### 🔄 How It Differs from `did:jwk`

- `did:jwk` is a **self-contained DID** derived directly from the JWK itself.
- It’s deterministic and doesn’t require external resolution infrastructure.
- But it **doesn’t support key rotation** or multiple keys—it’s a one-key, one-DID setup.

Using a custom DID gives you **more flexibility**, especially if you're managing multiple keys, rotating them, or integrating with federated trust systems like VerifiedPress.

---

### 🧪 Example

```json
{
  "kty": "RSA",
  "alg": "RS256",
  "use": "sig",
  "kid": "did:web:verifiedpress.org#signing-key-2025",
  "n": "...",
  "e": "AQAB"
}
```

This JWK is tied to a key in the DID Document at `did:web:verifiedpress.org`, and the verifier can resolve that DID to fetch the public key.

---


## ⚠️ Security Notice

> The included keys and output are for **development and evaluation purposes only**.  
> Do **not** use them in production, public, or credential-critical environments.  
> Always generate secure keys using approved cryptographic standards and keep private keys encrypted and isolated.

---

## 🤝 Contributing

Pull requests and suggestions are welcome! To extend the tool with support for private key to JWK conversion or JWT signing, feel free to open a feature request.

---

## 🧪 License

Apache-2.0 for code.  
Cryptographic keys provided herein are **not valid for production** — use at your own risk.
