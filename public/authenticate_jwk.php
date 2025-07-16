<?php
require 'authenticate_functions.php';

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

$_SESSION['did'] = $did;

try {
    $jwk = json_decode(file_get_contents($user['jwk']), true)['verificationMethod'][0]['publicKeyJwk'];
    $isValid = verifySignatureByJWK($jwk, $message, $proof);

    if ($isValid) {
        unset($_SESSION['error']);
        $_SESSION['method'] = 'Verification by JSON Web Key (JWK)';
        $_SESSION['name'] = $user['name'];
        require("error/error-success.php");
    }
} catch(Exception $ex) {
    $_SESSION['error'] = $ex->getMessage();
    require("error/error-invalid-signature.php");
}
?>