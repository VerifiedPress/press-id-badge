<?php
session_start();

if (isset($_POST['authenticate_privatekey'])) {
    require 'authenticate_key.php';
} elseif ($_POST['authenticate_jwt']) {
    require 'authenticate_jwk.php';
} else {

}

?>