<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Missing Parameters</title>
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  <meta charset="UTF-8">
</head>
<body class="w3-container w3-light-grey">

  <div class="w3-panel w3-red w3-card-4 w3-margin-top">
    <h3 class="w3-center">⚠️ Missing Parameters</h3>
    <p class="w3-large">
      The following required fields were not provided:
    </p>
    <ul class="w3-ul w3-border w3-white w3-padding">
      <li><strong>message</strong>: Payload text required for signature</li>
      <li><strong>publicKey</strong>: PEM-encoded RSA public key</li>
      <li><strong>signature</strong>: Verification signature in hex or base64 format</li>
    </ul>
    <p class="w3-text-grey">
      Please make sure all parameters are properly passed and encoded. UTF-8 is recommended for message strings.
    </p>
  </div>

  <div class="w3-panel w3-yellow w3-margin-top">
    <h4>💡 Tips:</h4>
    <ul>
      <li>Use heredoc format for embedded PEM keys in PHP.</li>
      <li>Normalize newline characters when importing keys from JS.</li>
      <li>Hex or base64 signature encoding must match across platforms.</li>
    </ul>
  </div>

</body>
</html>