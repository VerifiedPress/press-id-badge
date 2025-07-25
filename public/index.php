<!DOCTYPE html>
<html>
<head>
  <title>Press ID Badge Sandbox</title>
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body class="w3-light-grey">

  <div class="w3-container w3-padding-64">
    <div class="w3-card-4 w3-white w3-padding">

        <div class="w3-container w3-blue">
            <h2>Press ID Badge Sandbox</h2>
        </div>
        <div id="pluginStatus" class="w3-panel w3-yellow w3-padding w3-margin-top" style="display: none;">
            <h3>🔧 VerifiedPress Plugin Required</h3>
            <p>To log in securely using your DID, please install the VerifiedPress ID Badge browser extension:</p>
            <a class="w3-button w3-green w3-block" href="https://github.com/VerifiedPress/press-id-badge" target="_blank">Install Plugin</a>
        </div>

        <fieldset>
            <form method="post" action="authenticate.php">
                <p>
                    <label for="did" class="w3-text-blue"><b>Your DID</b></label>
                    <input class="w3-input w3-border w3-light-grey" id="did" name="did" type="text" placeholder="did:web:yourdomain.com" readonly required>
                </p>

                <p>
                    <label for="message" class="w3-text-blue"><b>Message</b></label>
                    <input class="w3-input w3-border" id="message" name="message" type="text" value="" placeholder="Enter a message to be signed." required>
                </p>

                <p>
                    <label for="proof" class="w3-text-blue"><b>Signature Proof</b></label>
                    <textarea class="w3-input w3-border w3-light-grey" rows="5" id="proof" name="proof" placeholder="Signature of signing message placed here..." readonly required></textarea>
                </p>

                <button id="signButton" type="button" class="w3-button w3-blue w3-margin-top w3-block">Sign</button>
                <br/>
                <input type="submit" name="authenticate_publickey" class="w3-button w3-block w3-green" value="Validate using Public Key" />
                <br/>
                <input type="submit" name="authenticate_jwt" class="w3-button w3-block w3-purple" title="This is the preferred method for verifying credentials!" value="Validate using JSON Web Key (JWK)" />
            </form>
            <fieldset>
                <div class="w3-container">
                <div class="w3-panel w3-leftbar w3-light-grey">
                    <p class="w3-xlarge">
                    A boomer wakes up to a ransacked home.<br>
                    TV's still there. Jewelry untouched. Laptop humming quietly.<br>
                    But their sacred password notebook? Gone.<br><br>

                    Left in its place? A sticky note in ballpoint ink:<br>
                    <strong>"Install the Press ID Badge extension, Brenda."</strong><br><br>

                    No stolen goods—just stolen assumptions about personal cybersecurity.<br>
                    The burglar didn’t take valuables. They took initiative.<br><br>

                    Boomer defense system:<br>
                    1. Passwords in plain sight.<br>
                    2. Webcam covered with a Post-it.<br>
                    3. Extensions… still a mystery.
                    </p>
                </div>
                </div>                
            </fieldset>
        </fieldset>
    </div>
  </div>
    <script type="text/javascript">
        window.onload = function() {
            window.postMessage({command: "GET_USER_PARAMS"}, "*");
        }

        document.getElementById("signButton").addEventListener("click",async () => {
            const message = document.getElementById("message").value;
            if (message.length > 0) {
                window.postMessage({ command: "SHOW_SIGNING_DIALOG", message: message }, "*");
            } else {
                alert("Missing message to be signed?")
            }
            return false;
        });

        window.addEventListener("message", async function (e) {
            if (!e.data || typeof e.data !== "object") return;

            console.log(e);

            if (e.data.type === "USER_PARAMS_RESPONSE") {
                console.log(e);
                const {did, privateKey} = e.data;
                const didInput = document.getElementById("did");

                if (didInput) didInput.value = did || "";
            } else if (e.data.type === "SIGNATURE_READY") {
                const { signature } = e.data;
                console.log(signature);
                document.getElementById("proof").value = signature.signature;
            }
        });
    </script>

</body>
</html>