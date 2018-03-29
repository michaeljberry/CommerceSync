<?php
include_once 'core/init.php';

if (empty($_POST) === false) {
    $username = trim(htmlentities($_POST['username']));
    $password = trim(htmlentities($_POST['password']));

    if (empty($username) === true || empty($password) === true) {
        $errors[] = 'Sorry, but we need your username and password.';
    } else if ($users->user_exists($username) === false) {
        $errors[] = 'Sorry that username doesn\'t exist.';
    } else {
        $login = $users->login($username, $password);
        if ($login === false) {
            $errors[] = 'Sorry, that username/password is invalid';
        } else {
            session_regenerate_id(true);
            $_SESSION['id'] = $login;
            $_SESSION['page'] = 'main';
            header('Location: home.php');
            exit();
        }
    }
}
if (isset($_GET['success']) && empty($_GET['success'])) {
    echo 'Thank you for registering. Please check your email to activate your account. Michael has been notified to setup your permissions.';
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang='en'>
<head>
    <meta charset="UTF-8">
    <title><?= getenv("APP_NAME") ?> - Login</title>
    <!-- <link rel='stylesheet' href='includes/css/style.css'/> -->
    <!-- <link rel='stylesheet' href='includes/css/jquery-ui.css'/>  -->
    <!--    <link rel='stylesheet' href='includes/css/colorbox.css'/>-->
    <style>
        #capsLockNotice {
            display: none;
            position: relative;
        }

        #capsLockNotice img {
            left: 150px;
            position:;
            top: -5px;
        }

        <?php echo $background; ?>
        #login {
            background: #fff;
            padding: 10px;
            text-decoration: underline;
            width: 280px;
            height: 280px;
            text-align: center;
            top: calc(50% - 150px);
            left: calc(50% - 150px);
            position: relative;
            background-color: rgba(255, 255, 255, 0);
            border-radius: 5px;
        }

        #header {
            font-weight: bold;
            font-size: 22px;
        }

        #wrap {
            top: 0;
            bottom: 0;
            right: 0;
            left: 0;
            position: absolute;
            height: 100%;
            width: 100%;
        }

        button.submit, #forgot a {
            background-color: #E31E27;
            background: -webkit-gradient(linear, left top, left bottom, from(#E31E27), to(#C91E23));
            background: -webkit-linear-gradient(top, #E31E27, #C91E23);
            background: -moz-linear-gradient(top, #E31E27, #C91E23);
            background: -ms-linear-gradient(top, #E31E27, #C91E23);
            e background: -o-linear-gradient(top, #E31E27, #C91E23);
            background: linear-gradient(top, #E31E27, #C91E23);
            border: 1px solid #F9222A;
            border-bottom: 1px solid #C91E23;
            box-shadow: inset 0 1px 0 0 #E27679;
            -webkit-box-shadow: 0 1px 0 0 #E27679 inset;
            -moz-box-shadow: 0 1px 0 0 #E27679 inset;
            -ms-box-shadow: 0 1px 0 0 #E27679 inset;
            -o-box-shadow: 0 1px 0 0 #E27679 inset;
            color: white !important;
            padding: 6px;
            text-align: center;
            font-family: "msc-opensans-regular-webfont";
            font-size: 14px;
        }

        button.submit:hover, #forgot a:hover {
            cursor: pointer;
            background-color: #E31E27;
            background: -webkit-gradient(linear, left top, left bottom, from(#FF0F17), to(#E31E27));
            background: -webkit-linear-gradient(top, #FF0F17, #E31E27);
            background: -moz-linear-gradient(top, #FF0F17, #E31E27);
            background: -ms-linear-gradient(top, #FF0F17, #E31E27);
            background: -o-linear-gradient(top, #FF0F17, #E31E27);
            background: linear-gradient(top, #FF0F17, #E31E27);
        }

        button.submit:active, #forgot a:active {
            border: 1px solid #C91E23;
            box-shadow: 0 0 10px 5px #A5171B inset;
            -webkit-box-shadow: 0 0 10px 5px #A5171B inset;
            -moz-box-shadow: 0 0 10px 5px #A5171B inset;
            -ms-box-shadow: 0 0 10px 5px #A5171B inset;
            -o-box-shadow: 0 0 10px 5px #A5171B inset;
        }

        #forgot a:visited {
            color: white;
        }

        #birthdayimg {
            width: 700px;
            margin-left: 50px;
        }

        #birthdaytext {
            font-size: 55px;
            font-style: italic;
            font-weight: bold;
            margin-left: 25px;
            max-width: 750px;
            text-align: center;
        }

        #wrong {
            background: #ffff00;
            opacity: 0.8;
        }

        #wrong p {
            padding: 5px;
        }
    </style>
    <!--    <script src="includes/js/jquery.min.js"></script>-->
    <!--    <script src='includes/js/ui/jquery-ui.js'></script>-->
    <!--    <script src='includes/js/jquery.colorbox-min.js'></script>-->
</head>
<body>
<div id='wrap'>
    <div id='login'>
        <!-- <div id='header'>Please Login</div> -->
        <?php
        if (empty($errors) === false) {
            echo '<div id="wrong"><p>' . implode('</p><p>', $errors) . '</p></div>';
        }
        ?>
        <form method='post' action="login.php">
            <!-- <h4>Username: </h4> -->
            <input type="text" name='username' placeholder="Username"
                   value='<?php if (isset($_POST['username'])) echo htmlentities($_POST['username']); ?>'/>
            <br/>
            <br/>
            <!-- <h4>Password:</h4> -->
            <div id="capsLockNotice">
                <img class="ssdlogo" alt="Caps Lock Is ON" title="Caps Lock Is ON"
                     src="includes/img/capslock-notice.png">
            </div>
            <input type="password" name='password' placeholder="Password" class="capLocksCheck"/>
            <br/>
            <br/>
            <button type="submit" class='submit'>Login</button>
        </form>
        <br/>
        <div id='forgot'><a href="confirm-recover.php" title="Forgot Password">Forgot your username/password?</a></div>
        <br/>
        <div><a href='register.php'>Register</a></div>
    </div>
</div>
<script>
    //clear_sub();
    //    var existing = window.onload;
    //    window.onload = function()
    //    {
    /* if(typeof(existing) == "function")
     {
     existing();
     } */
    //        loadCapsChecker();
    //    }

    function loadCapsChecker() {
        capsClass = "capLocksCheck";
        capsNotice = "capsLockNotice";

        var inputs = document.getElementsByTagName('INPUT');
        var elements = new Array();
        for (var i = 0; i < inputs.length; i++) {
            if (inputs[i].className.indexOf(capsClass) != -1) {
                elements[elements.length] = inputs[i];
            }
        }
        for (var i = 0; i < elements.length; i++) {
            if (document.addEventListener) {
                elements[i].addEventListener("keypress", checkCaps, "false");
            }
            else {
                elements[i].attachEvent("onkeypress", checkCaps);
            }
        }
    }

    function checkCaps(e) {
        var pushed = (e.charCode) ? e.charCode : e.keyCode;
        var shifted = false;
        if (e.shiftKey) {
            shifted = e.shiftKey;
        }
        else if (e.modifiers) {
            shifted = !!(e.modifiers & 4);
        }
        var upper = (pushed >= 65 && pushed <= 90);
        var lower = (pushed >= 97 && pushed <= 122);
        if ((upper && !shifted) || (lower && shifted)) {
            if (document.getElementById(capsNotice)) {
                document.getElementById(capsNotice).style.display = 'block';
            }
            else {
                alert("Caps lock is on");
            }
        }
        else if ((lower && !shifted) || (upper && shifted)) {
            if (document.getElementById(capsNotice)) {
                document.getElementById(capsNotice).style.display = 'none';
            }
        }
    }
</script>
</body>
</html>