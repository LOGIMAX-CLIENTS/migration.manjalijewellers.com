<?php
require_once '/home/retaillogimaxind/public_html/webhooks/PHPMailer/PHPMailer.php';
require_once '/home/retaillogimaxind/public_html/webhooks/PHPMailer/SMTP.php';
require_once '/home/retaillogimaxind/public_html/webhooks/PHPMailer/Exception.php';

$mail = new PHPMailer\PHPMailer\PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'deploy.logimax@gmail.com';
    $mail->Password = 'your-app-password'; // Replace with actual app password
    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    
    $mail->setFrom('deploy.logimax@gmail.com', 'Test Bot');
    $mail->addAddress('kanagasundar@logimaxindia.com');
    
    $mail->isHTML(true);
    $mail->Subject = 'PHPMailer Test';
    $mail->Body = '<h1>PHPMailer is working!</h1><p>This is a test email.</p>';
    
    $mail->send();
    echo "Test email sent successfully!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
