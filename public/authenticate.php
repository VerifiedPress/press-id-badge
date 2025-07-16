<?php
session_start();

// Helper function to load public key for a given DID
function getPublicKeyByDID($did) {
    // Example: Load from file, database, or registry
    $registry = [
        'did:web:localhost:8082' => [
            'name' => 'Test and Evaluation',
            'email' => 'someone@localhost',
            'role' => 'author',
            'key' => 'https://github.com/VerifiedPress/press-id-badge/raw/refs/heads/main/public/public_key.pem',
            'url' => 'http://localhost:8082/.well-known/did.json'
        ]
    ];
    return $registry[$did] ?? null;
}

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


// Extract POST values
$did = $_POST['did'] ?? '';
$proof = $_POST['proof'] ?? '';
$message = "login-request"; // known static message

if (!$did || !$proof) {
    require('error/error-missing-parameters.php');
    exit();
}

// Get public key
$user = getPublicKeyByDID($did);
if (!$user) {
    exit("Unknown DID.");
}


$message = $_POST['message'];
$publicKeyPem = file_get_contents($user['key']);

$verified = verifySignature($publicKeyPem, $message, $proof);


$_SESSION['did'] = $did;

if ($verified) {
    unset($_SESSION['error']);
    $_SESSION['name'] = $user['name'];
    require("error/error-success.php");
} elseif ($verified === 0) {
    $_SESSION['error'] = openssl_error_string();
    require("error/error-invalid-signature.php");
} else {
    $_SESSION['error'] = openssl_error_string();
    require("error/error-verification-error.php");
}
?>