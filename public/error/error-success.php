<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Verification Successful</title>
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body class="w3-light-green">

  <div class="w3-container w3-card w3-margin w3-padding-large w3-white">
    <h2 class="w3-text-green">✅ Verification Successful</h2>
    <p>The signed message (or JWT) was successfully verified using the public key associated with the DID owner.</p>
    <p><b>Method:</b> <?php echo $_SESSION['method']; ?></p>
    <p><b>DID:</b> <?php echo $_SESSION['did']; ?></p>
    <p><b>Signer:</b> <?php echo $_SESSION['name']; ?></p>
    <p><b>Timestamp:</b> <?php echo (new DateTime('now', new DateTimeZone('America/New_York')))->format('Y-m-d H:i:s T'); ?></p>
  </div>

</body>
</html>