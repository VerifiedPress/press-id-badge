#!/usr/bin/env node

const { writeFileSync, readFileSync } = require("fs");
const { spawnSync } = require("child_process");
const path = require("path");

const fs = require("fs");

function extractModulusExponent(pemPath) {
  const result = spawnSync("openssl", [
    "rsa", "-in", pemPath, "-pubin", "-text", "-noout"
  ]);

  if (result.error || result.status !== 0) {
    throw new Error("OpenSSL error: " + result.stderr.toString());
  }

  const output = result.stdout.toString();
  const modulusMatch = output.match(/Modulus:\s*((?:\s+[0-9A-Fa-f:]+\n?)+)/);
  const exponentMatch = output.match(/Exponent:\s+\d+\s+\(0x([0-9a-f]+)\)/i);

  const modulusHex = modulusMatch[1].replace(/[\s:\n]/g, "");
  const exponentHex = exponentMatch[1].padStart(6, "0");

  return {
    n: base64url(Buffer.from(modulusHex, "hex")),
    e: base64url(Buffer.from(exponentHex, "hex"))
  };
}

function base64url(buffer) {
  return buffer.toString("base64").replace(/\+/g, "-").replace(/\//g, "_").replace(/=+$/, "");
}

function getModulusHex(pemPath) {
  const result = spawnSync("openssl", [
    "rsa",
    "-in", pemPath,
    "-pubin",
    "-text",
    "-noout"
  ]);

  if (result.error || result.status !== 0) {
    throw new Error("OpenSSL error: " + (result.error?.message || result.stderr.toString()));
  }

  const output = result.stdout.toString();
  const modulusMatch = output.match(/Modulus:\s*((?:\s+[0-9A-Fa-f:]+\n?)+)/);
  const exponentMatch = output.match(/Exponent:\s+(\d+)\s+\(0x([0-9a-f]+)\)/i);

  if (!modulusMatch || !exponentMatch) {
    throw new Error("Failed to extract modulus or exponent");
  }

  const modulusHex = modulusMatch[1].replace(/[\s:\n]/g, "");
  const exponentHex = exponentMatch[2].padStart(6, "0"); // ensure 3-byte exponent
  return { modulusHex, exponentHex };
}

function hexToBuffer(hex) {
  return Buffer.from(hex.replace(/\s+/g, ""), "hex");
}

function safeHexToBuffer(hex) {
  if (typeof hex !== "string") {
    throw new Error("Hex input must be a string");
  }

  const cleaned = hex.replace(/\s+/g, "");
  if (!/^[0-9a-fA-F]+$/.test(cleaned)) {
    console.error("Invalid hex input:", hex); // 🔍 log original input
    throw new Error("Invalid hex input");
  }

  return Buffer.from(cleaned, "hex");
}

async function fetchDid(didUrl) {
  try {
    const res = await fetch(didUrl);
    const json = await res.json();
    return json.id || "";
  } catch (err) {
    console.warn("Could not fetch DID from", didUrl, err);
    return "";
  }
}

(async () => {
  const args = process.argv.slice(2);
  if (args.length < 1) {
    console.error("Usage: node modulus2jwk.js <pemPath> [didUrl] [outputPath]");
    process.exit(1);
  }

  const [pemPath, didUrl, outputPath] = args;
  const resolvedPem = path.resolve(pemPath);
  const { modulusHex, exponentHex } = getModulusHex(resolvedPem);

  const n = base64url(hexToBuffer(modulusHex));
  const e = base64url(hexToBuffer(exponentHex));
  const did = didUrl ? await fetchDid(didUrl) : "";

  console.log(did)

  const jwk = {
    kty: "RSA",
    n,
    e,
    alg: "RS256",
    use: "sig",
    kid: did
  };

  const dest = outputPath || path.resolve(path.dirname(pemPath), "public_key_jwk.json");
  writeFileSync(dest, JSON.stringify(jwk, null, 2));
  console.log(`✅ JWK written to ${dest}`);
})();