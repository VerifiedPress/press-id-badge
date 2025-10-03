# 🧰 VerifiedPress Tools

This directory hosts command-line utilities and developer scripts used to support the VerifiedPress identity workflow, credential signing, and sandbox DID deployments.

Each tool is designed to be modular, auditable, and compliant with Web Standards for Verifiable Credentials and JSON Web Keys.

---

## 🔑 Tool: `key2jwk`

### Purpose

Converts an RSA public key (PEM format) into a standards-compliant JSON Web Key (JWK) for use in DID documents or credential verification.

### Location

```sh
tools/jwt
tools/key2jwk/
```

### Highlights

- Parses public key using OpenSSL + Node.js
- Extracts Modulus and Exponent for RSA-2048
- Outputs JWK in base64url format
- Ready for insertion into `.well-known/did.json`

### Usage

```bash
cd tools/key2jwk
npm install
npm start
```

Output is printed to console and may optionally be written to `public_key_jwk.json`.

---

## 🧭 Future Additions

Coming soon to `/tools/`:

| Tool         | Description                                          |
|--------------|------------------------------------------------------|
| `signvc`     | Sign a Verifiable Credential using private key PEM   |
| `jwtbuilder` | Generate signed JWTs for decentralized login flows   |
| `rotatekeys` | Ephemeral key generator for sandbox identity testing |
| `verifyproof`| Validate JWTs or VCs against published JWKs          |

---

## ⚠️ Security Notice

> Keys and scripts in this directory are for **local development and evaluation only**.  
> Do **not** use test keys in production environments or public trust workflows.  
> Always generate fresh key pairs and store private keys securely.

---

## 🤝 Contribution Guidelines

Feel free to extend any tool or open a proposal to add new ones.  
All contributions should follow a modular and auditable pattern.

---

## 📄 License

Apache-2.0 License — see individual scripts for source comments and disclaimers.
