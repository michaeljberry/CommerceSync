<?php

class Template{
    public function get_header(){
        include WEBROOT . 'header.php';
    }
    public function get_footer(){
        include WEBROOT . 'footer.php';
    }
    public function get_menu(){
        include WEBROOT . 'menu.php';
    }
}