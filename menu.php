<?php
$plugin = $_SERVER['REQUEST_URI'];
global $user_id;
global $rbac;

$current_page = "";
$plugin_array = array("musikey", "am", "eb", "rev", "mml");
foreach($plugin_array as $p){
    if(strpos($plugin, $p)){
        $current_page = $p;
    }
}
$links = "";
$menu = array(
    //Array Structure
    //First element - $m[0] Page Name
    //Second element - $m[1] permission required to access that page
    //Third element - $m[2] font icon name
    //Fourth element - $m[2] For sub-links
    array("Home","","home"),
    array("Departments","management","building",
        array(
            array("Marketing","marketing"),
            array("Operations","operations")
        )
    ),
    array("Marketplaces", "e_commerce","cloud",
        array(
            array("Amazon","am"),
            array("Ebay","eb"),
            array("Reverb","rev"),
            array("MML","bc"),
            array("WooCommerce", 'wc'),
            array("Walmart", "wm")
        )
    ),
    array("Musikey", "musikey","key"),
    array("Account", "", "cogs",
        array(
            array("Change Password",""), //First part is link title, second is folder name
            array("Settings","")
        )
    ),
    array("Admin", "management", "lock",
        array(
            array("Map Categories", "admin"),
            array("Amazon Categories", "admin"),
            array("Ebay Categories", "admin"),
            array("BigCommerce Categories", "admin"),
            array("Amazon Taxes", "admin")
        )
    ),
    array("Log Out","","power-off")
);
foreach($menu as $m){
    $hassubmenu = false;
    if(isset($m[3])){
        $hassubmenu = true;
    }
    if (!empty($m[1])) {
        $permission = $m[1];
        if ($rbac->check($permission, $user_id)) {
            $links .= "<li class='has-sub'><i class='fa fa-$m[2] fa-fw'></i><a class='plugin' href='$m[0]/$m[0]'>$m[0]</a>";
            if($hassubmenu) {
                $links = set_subs($m[3], $links);
            }
            $links .= "</li>";
        }
    }else{
        $links .= "<li";
        if($hassubmenu) {
            $links .= " class='has-sub'";
        }
        $links .= "><i class='fa fa-$m[2] fa-fw'></i><a href='$m[0]'>$m[0]</a>";
        if($hassubmenu) {
            $links = set_subs($m[3], $links);
        }
        $links .= "</li>";
    }
}
function set_subs($menuarray, $links){
    $links .= "<ul>";
    foreach($menuarray as $s){
        $plugin = $s[0];
        $plugin_folder = $s[0];
        if($plugin == 'MML'){
            $plugin_folder = 'BC';
        }
        if(!empty($s[1])) {
            $links .= "<li><a class='plugin' href='$s[1]/$plugin_folder'>$plugin</a></li>";
        }else{
            $links .= "<li><a class='' href='$plugin_folder'>$plugin</a></li>";
        }
    }
    $links .= "</ul>";
    return $links;
}
?>
<div id="sticky">
    <div id="navigator">
        <div id="navcontainer">
            <ul id="nav">
                <?php echo $links; ?>
            </ul>
        </div>
        <div id="error"></div>
    </div>
</div>