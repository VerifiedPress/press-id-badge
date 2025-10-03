<?php
require 'authenticate_functions.php';

$did = $_POST['did'] ?? '';
$jwt = $_POST['proof_jwt'] ?? '';

if (!$did || !$jwt) {
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
    $remotePemUrl = json_decode(file_get_contents($user['jwk']), true)['verificationMethod'][0]['key'];
    $result = verifyJWT($jwt, $remotePemUrl);

    if ($result['valid']) {
        unset($_SESSION['error']);
        $_SESSION['method'] = 'Verification by JSON Web Token (JWT)';
        $_SESSION['name'] = $user['name'];
        require("error/error-success.php");
    } else {
        $_SESSION['error'] = $result['error'];
        switch ($result['error']) {
            case 'Unable to fetch public key from: ':
                $_SESSION['remote_url'] = $result['url'];
                require("error/error-publickey-notfound.php");
                break;
            case 'Invalid JWT format.':
                require("error/error-token-format.php");
                break;
            case 'Invalid JWT payload.':
                require("error/error-token-payload.php");
                break;
            case 'JWT has expired.':
                $_SESSION['payload'] = $result['payload'];
                require("error/error-token-expired.php");
                break;
            case 'Invalid signature.':
                $_SESSION['payload'] = $result['payload'];
                require("error/error-invalid-signature.php");
                break;
        }
    }
} catch(Exception $ex) {
    $_SESSION['error'] = $ex;
    require("error/error-verification-error.php");
}
?>