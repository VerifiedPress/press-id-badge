<?php
session_start();

// Set maximum reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);


if (isset($_POST['authenticate_publickey'])) {
    require 'authenticate_key.php';
} elseif ($_POST['authenticate_jwks']) {
    require 'authenticate_jwks.php';
} elseif ($_POST['authenticate_jwt']) {
    require 'authenticate_jwt.php';
} else {

}

?>