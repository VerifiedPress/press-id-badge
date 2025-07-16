<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Verification Error</title>
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body class="w3-pale-yellow">

  <div class="w3-container w3-card w3-margin w3-padding-large w3-white">
    <h2 class="w3-text-orange">⚠️ Verification Error</h2>
    <p>An error occurred during verification due to an OpenSSL issue.</p>
    <p><b>DID:</b> <?php echo $_SESSION['did']; ?></p>
    <p><b>Error:</b> <?php echo $_SESSION['error']; ?></p>
    <p><b>Suggestion:</b> Check the JWK structure, exponent, and PEM formatting.</p>
  </div>

</body>
</html>