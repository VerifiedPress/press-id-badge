const { writeFileSync } = require("fs");
const { spawnSync } = require('child_process');

function getModulusHex(public_key_filename) {
    const result = spawnSync('openssl', [
    'rsa',
    '-in',
    `${public_key_filename}`,
    '-pubin',
    '-text',
    '-noout'
    ]);

    if (result.error) {
        console.error("Error running OpenSSL:", result.error);
        process.exit(1);
    }

    const output = result.stdout.toString();

    // 🧬 Extract the entire Modulus block (multi-line up to Exponent:)
    const modulusBlockMatch = output.match(/Modulus:\s*((?:\s+[0-9A-Fa-f:]+\n?)+)/);
    const exponentMatch = output.match(/Exponent:\s+(\d+)\s+\(0x([0-9a-f]+)\)/i);

    if (!modulusBlockMatch || !exponentMatch) {
        console.error("Failed to extract key parameters.");
        process.exit(1);
    }

    // 🧹 Clean and flatten modulus block
    const modulusHex = modulusBlockMatch[1].replace(/[\s:\n]/g, "");
    const exponentHex = exponentMatch[2];

    if (!modulusHex || !exponentHex) {
        console.error("Failed to extract key parameters.");
        process.exit(1);
    }


    // Clean and flatten hex string
    return {modulusHex, exponentHex};
}

function base64url(buffer) {
  return buffer.toString("base64")
    .replace(/\+/g, "-").replace(/\//g, "_").replace(/=+$/, "");
}

function hexToBuffer(hex) {
  return Buffer.from(hex.replace(/\s+/g, ""), "hex");
}

// Paste your extracted hex values here
let did = "";
const {modulusHex, exponentHex} = getModulusHex("../../public/public_key.pem"); // ← from OpenSSL
(async()=>{
  const json = await fetch("http://localhost:8082/.well-known/did.json").then(res=>res.json());
  did = json.id;

  const n = base64url(hexToBuffer(modulusHex));
  const e = base64url(hexToBuffer(exponentHex));

  const jwk = {
    kty: "RSA",
    e,
    n,
    alg: "RS256",
    use: "sig",
    kid: did
  };

  writeFileSync("../../public/public_key_jwk.json", JSON.stringify(jwk, null, 2));

  console.log('JSON Web Key is written to ../../public/public_key_jwk.json');  
})();

