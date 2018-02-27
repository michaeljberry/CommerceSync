<?php
include_once 'core/init.php';

if ($userID) {
    $template->get_header();
    // if ($rbac->check('management', $userID)) {
    //     echo 'Hey Howdy Hey, ' . $firstname . '!';
    // } elseif ($rbac->check('musikey', $userID)) {
    //     include 'plugin/musikey/musikey.php';
    // }
    echo "<br>";

    ?>
    </div>
    <?php
    $template->get_footer();
    ?>
    <?php
} else {
    header("Location: login.php");
}