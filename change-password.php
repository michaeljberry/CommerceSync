<?php
include_once 'core/init.php';
?>
    <div id='container'>
        <?php
        if(empty($_POST) === false){
            if(empty($_POST['current_password']) || empty($_POST['password']) || empty($_POST['password_again'])){
                $errors[] = 'All fields are required';
            }else if(Bcrypt::verifyPass($_POST['current_password'], $user['password']) === true){
                if(trim($_POST['password']) != trim($_POST['password_again'])){
                    $errors[] = 'Your new passwords do not match.';
                }else if(strlen($_POST['password'])< 6){
                    $errors[] = 'Your password must be at least 6 characters long.';
                }else if (strlen($_POST['password'])> 18){
                    $errors[] = 'Your password cannot be more than 18 characters long.';
                }
            }else{
                $errors[] = 'Your current password is incorrect.';
            }
        }
        if(isset($_GET['success']) === true && empty($_GET['success']) === true){
            echo '<p>Your password has been changed successfully!';
            echo '<br />Go back to the <a href="home.php">home page</a>';
        }else {?>
            <h2>Change Password</h2>
            <hr />
            <?php
            if(empty($_POST) === false && empty($errors) === true){
                $users->change_password($user['id'],$_POST['password']);
                header('Location: change-password.php?success');
            }else if (empty($errors) === false){
                echo '<p>' . implode('</p><p>',$errors).'</p>';
            }
            ?>
            <form action="" method='post' class='form'>
                <h4>Current Password:</h4>
                <input type="password" name='current_password'></input>
                <h4>New Password:</h4>
                <input type="password" name='password'></input>
                <h4>Confirm New Password:</h4>
                <input type="password" name='password_again'></input>
                <br />
                <br />
                <button class="submit">Change Password</button>
            </form>
        <?php } ?>
    </div>
<?php

?>