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
