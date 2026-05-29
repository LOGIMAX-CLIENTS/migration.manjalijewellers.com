<?php
header('Content-Type: text/plain');
echo "Webhook test successful!\n";
echo "Server: " . $_SERVER['SERVER_NAME'] . "\n";
echo "Method: " . $_SERVER['REQUEST_METHOD'] . "\n";
?>
