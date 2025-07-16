<?php
require 'JwkVerifier.php';

// Helper function to load public key for a given DID
function getPublicKeyByDID($did) {
    // The registry is maintain on the authentication service, no password just links to user-controlled credentials
    // Typically this will be in a datastore, and not hardcoded in an array.
    $registry = [
        'did:web:localhost:8082' => [
            'name' => 'Test and Evaluation',
            'email' => 'someone@localhost',
            'role' => 'author',
            'key' => 'https://github.com/VerifiedPress/press-id-badge/raw/refs/heads/main/public/public_key.pem',
            'jwk' => 'https://github.com/VerifiedPress/press-id-badge/raw/refs/heads/main/public/.well-known/did.json'
        ]
    ];
    return $registry[$did] ?? null;
}

/**
 * Verifies the signature and message using the public key and openssl_* functions
 */
function verifySignature(string $publicKeyPem, string $message, string $signatureHex): bool {
    // Decode the signature from hex to raw binary
    $binarySignature = hex2bin($signatureHex);
    if ($binarySignature === false) {
        throw new Exception("Invalid hex signature format");
    }

    // Normalize message encoding to UTF-8
    $message = mb_convert_encoding($message, 'UTF-8');

    // Load public key
    $publicKey = openssl_pkey_get_public($publicKeyPem);
    if ($publicKey === false) {
        throw new Exception("Failed to parse public key");
    }

    // Verify signature using SHA256 and PKCS1 padding
    $result = openssl_verify($message, $binarySignature, $publicKey, OPENSSL_ALGO_SHA256);

    // Return true only if verification succeeds
    return $result === 1;
}

/**
 * Verify the signature and message using a custom JWK verifier class, without using openssl_* functions
 */
function verifySignatureByJWK(array $jwk, string $message, string $signatureHex) : bool {
    $verifier = new JwkVerifier($jwk, $message, $signatureHex);
    $isValid = $verifier->verify();

    return $isValid;
}
?>