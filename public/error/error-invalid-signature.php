<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Invalid Signature</title>
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body class="w3-pale-red">

  <div class="w3-container w3-card w3-margin w3-padding-large w3-white">
    <h2 class="w3-text-red">❌ Invalid Signature</h2>
    <p>The signature is not valid. The signed message could not be verified against the DID owner's public key.</p>
    <p><b>DID:</b> <?php echo $_SESSION['did']; ?></p>
    <p><b>Reason:</b> <?php echo $_SESSION['error']; ?></p>
  </div>

</body>
</html>