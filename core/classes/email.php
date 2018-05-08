<?php
require '/var/www/html/portal/vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
$mail = new PHPMailer;
$mail->isSMTP();
$mail->Host = getenv("EMAIL_HOST");
$mail->Port = getenv("EMAIL_PORT");
//$mail->SMTPAuth = true;
$mail->Username = getenv(" EMAIL_USER");
$mail->Password = getenv("EMAIL_PASSWORD");
$mail->SMTPDebug = 1;