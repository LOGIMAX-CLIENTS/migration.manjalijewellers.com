<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authorize Subscription</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            margin: 0; 
            background: #f5f5f5; 
        }
        .loader-container { text-align: center; padding: 20px; }
        .loader-container h4 { color: #333; margin-bottom: 20px; }
        .spinner {
            width: 40px; height: 40px; margin: 20px auto;
            border: 4px solid #ddd; border-top: 4px solid #5bc0de;
            border-radius: 50%; animation: spin 1s linear infinite;
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        .error-msg { color: red; font-size: 16px; margin-top: 15px; display: none; }
    </style>
</head>
<body>
    <div class="loader-container">
        <h4>Redirecting to Cashfree for Authorization...</h4>
        <div class="spinner" id="spinner"></div>
        <p class="error-msg" id="error-msg"></p>
    </div>

    <script src="https://sdk.cashfree.com/js/v3/cashfree.js"></script>
    <script>
        (function(){
            var sessionId = "<?php echo htmlspecialchars($session_id, ENT_QUOTES, 'UTF-8'); ?>";
            var cfMode    = "<?php echo htmlspecialchars($cf_mode, ENT_QUOTES, 'UTF-8'); ?>";

            if(!sessionId || sessionId === '') {
                document.getElementById('spinner').style.display = 'none';
                var errEl = document.getElementById('error-msg');
                errEl.textContent = 'Authorization session has expired. Please create a new subscription.';
                errEl.style.display = 'block';
                return;
            }

            try {
                var cashfree = Cashfree({ mode: cfMode });
                cashfree.subscriptionsCheckout({
                    subsSessionId: sessionId,
                    redirectTarget: "_self"
                }).then(function(result){
                    if(result.error){
                        document.getElementById('spinner').style.display = 'none';
                        var errEl = document.getElementById('error-msg');
                        errEl.textContent = 'Authorization failed: ' + result.error.message;
                        errEl.style.display = 'block';
                    }
                });
            } catch(err) {
                document.getElementById('spinner').style.display = 'none';
                var errEl = document.getElementById('error-msg');
                errEl.textContent = 'Error: ' + err.message;
                errEl.style.display = 'block';
            }
        })();
    </script>
</body>
</html>
