<?php

include_once 'core/init.php';

if($user_id){
    $template->get_header();
    if($rbac->check('management',$user_id)) {
        echo 'Hey Howdy Hey, ' . $firstname . '!';
    }elseif($rbac->check('musikey', $user_id)){
        include 'plugin/musikey/musikey.php';
    }
    echo "<br>";

    ?>
    </div>
    <?php
    $template->get_footer();
    ?>
<?php
}else{
    header("Location: login.php");
}
?>
