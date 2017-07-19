<?php
require '/var/www/html/portal/vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
$mail = new PHPMailer;
$mail->isSMTP();
$mail->Host = EMAIL_HOST;
$mail->Port = EMAIL_PORT;
//$mail->SMTPAuth = true;
$mail->Username = EMAIL_USER;
$mail->Password = EMAIL_PASSWORD;
$mail->SMTPDebug = 1;