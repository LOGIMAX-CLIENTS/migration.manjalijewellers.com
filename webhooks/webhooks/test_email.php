<?php
$to = "kanagasundar@logimaxindia.com";
$subject = "Test Email from Server";
$message = "This is a test email from your server.";
$headers = "From: webmaster@retail.logimaxindia.com";

if (mail($to, $subject, $message, $headers)) {
    echo "Test email sent successfully!\n";
} else {
    echo "Failed to send test email.\n";
    echo "Error: " . error_get_last()['message'] . "\n";
}
?>
