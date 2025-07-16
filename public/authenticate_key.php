<?php
require 'authenticate_functions.php';



// Extract POST values
$did = $_POST['did'] ?? '';
$proof = $_POST['proof'] ?? '';
$message = $_POST['message'] ?? '';

if (!$did || !$proof) {
    require('error/error-missing-parameters.php');
    exit();
}

// Get public key
$user = getPublicKeyByDID($did);
if (!$user) {
    exit("Unknown DID.");
}


$publicKeyPem = file_get_contents($user['key']);

$verified = verifySignature($publicKeyPem, $message, $proof);


$_SESSION['did'] = $did;

if ($verified) {
    unset($_SESSION['error']);
    $_SESSION['name'] = $user['name'];
    $_SESSION['method'] = 'Verification by Public Key (public_key.pem)';
    require("error/error-success.php");
} elseif ($verified === 0) {
    $_SESSION['error'] = openssl_error_string();
    require("error/error-invalid-signature.php");
} else {
    $_SESSION['error'] = openssl_error_string();
    require("error/error-verification-error.php");
}
?>