<?php
include_once 'core/init.php';
?>
<html>
<head>
<!--    <link rel='stylesheet' href='includes/css/style.css'/>-->
    <style>
        body{padding:10px;}
    </style>
</head>
<body>
<div id='container'>
    <?php
    if (isset($_GET['success']) === true && empty($_GET['success']) === true){
        ?>
        <h3>Thanks, please check your email to confirm your request for a password.</h3>
    <?php }else {
        if(isset($_POST['email']) === true && empty($_POST['email']) === false){
            if($users->email_exists($_POST['email']) === true){
                $email = htmlentities($_POST['email']);
                $generated_string = $users->confirm_recover($email);
                if(!empty($generated_string)){
                    $message = "Hello " . $username . "Please click this link to generate a new password: http://192.168.61.128/portal/recover.php?email=" . $email . "&generated_string=" . $generated_string . ". We will generate a new password for you and send it back to your email address. ";
                    $mail->addAddress($email);
                    $mail->Subject = 'Password';
                    $mail->Body = $message;
                    if($mail->send()) {
                        header('Location: confirm-recover.php?success');
                        exit();
                    }else{
                        echo 'Password Recovery email could not be sent.';
                        echo 'Mailer Error: ' . $mail->ErrorInfo;
                    }
                    //mail($email, 'Recover Password', $message);
                }
            }else{
                echo 'Sorry, that email doesn\'t exist.';
            }
        }?>
        <h2>Recover Username / Password</h2>
        <p>Enter your email below so we can confirm your request.</p>
        <hr />
        <form action="" method='post'>
            <ul class='listnone'>
                <li>
                    <input type="text" required name='email' />
                </li>
                <li>&nbsp;</li>
                <li>
                    <button type="submit" class='submit'>Recover</button>
                </li>
            </ul>
        </form>
    <?php
    }?>
</div>
</body>