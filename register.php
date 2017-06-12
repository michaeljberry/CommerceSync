<?php
include_once 'core/init.php';

if(isset($_POST['username'])){
    echo 'Submitted';
    if(empty($_POST['username']) || empty($_POST['password']) || empty($_POST['email'])){
        $errors[] = 'All fields are required.';
    }else{
        if($users->user_exists($_POST['username']) === true){
            $errors[] = 'That username already exists';
        }
        if(!ctype_alnum($_POST['username'])){
            $errors[] = 'Please enter a username with only alphanumeric characters.';
        }
        if(strlen($_POST['password']) <6){
            $errors[] = 'Your password must be at least 6 characters';
        }else if(strlen($_POST['password']) >18){
            $errors[] = 'Your password cannot be more than 18 characters long';
        }
        if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === false){
            $errors[] = 'Please enter a valid email address';
        }else if($users->email_exists($_POST['email']) === true){
            $errors[] = 'That email already exists.';
        }
        if(empty($errors) === true){
            $username = htmlentities($_POST['username']);
            $password = htmlentities($_POST['password']);
            $email = htmlentities($_POST['email']);

            $users->register($username,$password,$email, $mail);
            header('Location: login.php?success');
            exit();
        }
    }
}
?>
<style>
    button.submit, #forgot a{
        background-color: #E31E27;
        background: -webkit-gradient(linear, left top, left bottom, from(#E31E27), to(#C91E23));
        background: -webkit-linear-gradient(top, #E31E27, #C91E23);
        background: -moz-linear-gradient(top, #E31E27, #C91E23);
        background: -ms-linear-gradient(top, #E31E27, #C91E23);e
    background: -o-linear-gradient(top, #E31E27, #C91E23);
        background: linear-gradient(top, #E31E27, #C91E23);
        border: 1px solid #F9222A;
        border-bottom: 1px solid #C91E23;
        box-shadow: inset 0 1px 0 0 #E27679;
        -webkit-box-shadow: 0 1px 0 0 #E27679 inset ;
        -moz-box-shadow: 0 1px 0 0 #E27679 inset;
        -ms-box-shadow: 0 1px 0 0 #E27679 inset;
        -o-box-shadow: 0 1px 0 0 #E27679 inset;
        color: white !important;
        padding: 6px;
        text-align: center;
        font-family: "msc-opensans-regular-webfont";
        font-size: 14px;
    }
    button.submit:hover, #forgot a:hover{
        cursor: pointer;
        background-color: #E31E27;
        background: -webkit-gradient(linear, left top, left bottom, from(#FF0F17), to(#E31E27));
        background: -webkit-linear-gradient(top, #FF0F17, #E31E27);
        background: -moz-linear-gradient(top, #FF0F17, #E31E27);
        background: -ms-linear-gradient(top, #FF0F17, #E31E27);
        background: -o-linear-gradient(top, #FF0F17, #E31E27);
        background: linear-gradient(top, #FF0F17, #E31E27);
    }
    button.submit:active, #forgot a:active{
        border: 1px solid #C91E23;
        box-shadow: 0 0 10px 5px #A5171B inset;
        -webkit-box-shadow:0 0 10px 5px #A5171B inset ;
        -moz-box-shadow: 0 0 10px 5px #A5171B inset;
        -ms-box-shadow: 0 0 10px 5px #A5171B inset;
        -o-box-shadow: 0 0 10px 5px #A5171B inset;
    }
</style>
<div id='container'>
    <h1>Register</h1>
    <form method='post' action='register.php'>
        <h4>Username: </h4>
        <input type="text" name='username' value='<?php /*if(isset($_POST['username'])) echo htmlentities($_POST['username']);*/ ?>' />
        <h4>Password: </h4>
        <input type="password" name='password' />
        <h4>Email: </h4>
        <input type="text" name='email' value='<?php /*if(isset($_POST['email'])) echo htmlentities($_POST['email']);*/ ?>' />
        <br />
        <br />
        <button type="submit" class='submit'>Register</button>
    </form>
    <?php
    if(empty($errors) === false){
        echo '<p>' . implode('</p><p>', $errors). '</p>';
    }
    ?>
</div>
<?php
//$template->get_footer();
?>
