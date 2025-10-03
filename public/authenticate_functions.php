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
        ],
        'did:press:presspage.news:publisher' => [
            'name' => 'PRESSPAGE ENTERTAINMENT INC',
            'email' => 'presspage.entertainment@gmail.com',
            'role' => 'publisher',
            'key' => 'https://presspage.news/keys/public.pem',
            'jwk' => 'https://presspage.news/.well-known/did.json'
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
function verifySignatureByJWK(array $jwk, string $message, string $signatureHex) : array {
    $verifier = new JwkVerifier($jwk, $message, $signatureHex);
    $isValid = $verifier->verify();

    return [
        'valid' => $isValid,
        'error' => $verifier->getLastError()
    ];
}

function verifyJWT($jwt, $remotePemUrl) {
    // Load public key from remote URL
    $publicKey = file_get_contents($remotePemUrl);
    if (!$publicKey) {
        return [
            'valid' => 0,
            'url' => $remotePemUrl,
            'error' => "Unable to fetch public key from: "
        ];
    }

    // Split JWT into parts
    $parts = explode('.', $jwt);
    if (count($parts) !== 3) {
        return [
            'valid' => 0,
            'error' => "Invalid JWT format."
        ];
    }

    list($headerB64, $payloadB64, $signatureB64) = $parts;

    // Decode payload
    $payloadJson = base64_decode($payloadB64);
    $payload = json_decode($payloadJson, true);
    if (!$payload) {
        return [
            'valid' => 0,
            'error' => "Invalid JWT payload."
        ];
    }

    // Check expiration
    if (isset($payload['exp']) && time() > $payload['exp']) {
        return [
            'valid' => 0,
            "payload" => $payload,
            'error' => "JWT has expired."
        ];
    }

    // Reconstruct signed data
    $signedData = $headerB64 . '.' . $payloadB64;
    $signature = base64_decode(strtr($signatureB64, '-_', '+/'));

    // Verify signature
    $verified = openssl_verify($signedData, $signature, $publicKey, OPENSSL_ALGO_SHA256);

    if ($verified === 1) {
        return [
            'valid' => $verified,
            'payload' => $payload, // Valid and unexpired
        ];
    } elseif ($verified === 0) {
        return [
            'valid' => $verified,
            'error' => "Invalid signature."
        ];
    } else {
        throw new Exception("Verification error: " . openssl_error_string());
    }
}
?>