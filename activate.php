<?php
include_once 'core/init.php';
?>
    <div id='container'>
        <h1>Activate your account</h1>
        <?php
        if (isset($_GET['success']) === true && empty($_GET['success']) === true) {
            ?><h3>Thank you, we've activated your account. You're free to log in!</h3>
            <?php
        } else if (isset($_GET['email'], $_GET['email_code']) === true) {
            $email = trim($_GET['email']);
            if ($users->email_exists($email) === false) {
                $errors[] = 'Sorry, we couldn\'t find that email address.';
            } else if ($users->activate($email) === false) {
                $errors[] = 'Sorry, we couldn\'t activate your account.';
            }
            if (empty($errors) === false) {
                echo '<p>' . implode('</p><p>', $errors) . '</p>';
            } else {
                header('Location: activate.php?success');
                exit();
            }
        } else {
            header('Location: index.php');
            exit();
        }
        ?>
    </div>
<?php

?>