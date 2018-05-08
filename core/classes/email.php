<?php
require '/var/www/html/portal/vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
$mail = new PHPMailer;
$mail->isSMTP();
$mail->Host = getenv("EMAIL_HOST");
$mail->Port = getenv("EMAIL_PORT");
//$mail->SMTPAuth = true;
<<<<<<< HEAD
$mail->Username = getenv(" EMAIL_USER");
=======
$mail->Username = getenv("EMAIL_USER");
>>>>>>> 7bdafca1277f31c5e0f31f8209ec60e80446f973
$mail->Password = getenv("EMAIL_PASSWORD");
$mail->SMTPDebug = 1;