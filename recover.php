<?php
include_once 'core/init.php';
$general->logged_in_protect();
?>
<html>
<head>
    <!--    <link rel='stylesheet' href='includes/css/style.css'/>-->
    <style>
        body {
            padding: 10px;
        }
    </style>
</head>
<body>
<div id='container'>
    <?php
    if (isset($_GET['success']) === true && empty($_GET['success']) === true) {
        ?>
        <h3>Thank you, we've sent you a randomly generated password to your email.</h3>
        <div>Go ahead and <a href="login.php">login</a></div>
        <?php
    } else if (isset($_GET['email'], $_GET['generated_string']) === true) {
        try {
            $email = trim(htmlentities($_GET['email']));
            $string = trim(htmlentities($_GET['generated_string']));
            $generated_password = $users->recover($email, $string);
            if ($users->email_exists($email) === false || $generated_password === false) {
                $errors[] = 'Sorry, something went wrong and we couldn\'t recover your password. Please try again.';
            } else {
                echo 'Please check your email again for a randomly generated password. You can change your password after successful login.';
                $message = 'Hello, your new password is: ' . $generated_password . '. Please change your password once you have logged in using this password.';
                $mail->addAddress($email);
                $mail->Subject = 'Password';
                $mail->Body = $message;
                $mail->send();
                //mail($email,'Password Change',$message);
            }
            if (empty($errors) === false) {
                echo '<p>' . implode('</p><p>', $errors) . '</p>';
            } else {
                header('Location: recover.php?success');
                exit();
            }
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    } else {
        header('Location: index.php');
        exit();
    }
    ?>
</div>
</body>