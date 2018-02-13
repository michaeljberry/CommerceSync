<?php
//include_once 'core/init.php';
global $template;
?>
<!DOCTYPE html>
<html lang='en' xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title><?= APP_NAME ?></title>
    <meta charset="UTF-8">
    <meta content="text/html;charset=utf-8">
    <meta content="utf-8" http-equiv="encoding">
    <link type="text/css" rel='stylesheet' href='<?php echo WEBCSS; ?>colorbox.css'/>
    <link type="text/css" rel='stylesheet' href='<?php echo WEBCSS; ?>jquery-ui.css'/>
    <link type="text/css" rel='stylesheet' href='<?php echo WEBCSS; ?>flick/jquery-ui-1.10.3.custom.css'/>
    <link type="text/css" rel='stylesheet' href='<?php echo WEBCSS; ?>style.css'/>
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <link type="text/css" rel='stylesheet' href='<?php echo WEBCSS; ?>dataTables.tableTools.css'/>
    <link type="text/css" rel='stylesheet' href='<?php echo WEBCSS; ?>dataTables.fixedHeader.css'/>
    <link type="text/css" rel='stylesheet'
          href='<?php echo WEBINCLUDES; ?>DataTables-1.10.7/media/css/jquery.dataTables.css'/>
    <link type="text/css" rel='stylesheet' href='<?php echo WEBCSS; ?>c3.min.css'/>
    <script type="text/javascript" src="<?php echo WEBJS; ?>jquery-2.1.4.min.js"></script>
    <script type="text/javascript" src='<?php echo WEBJS; ?>jquery-ui.js'></script>
    <script type="text/javascript" src='<?php echo WEBJS; ?>jquery.colorbox-min.js'></script>
    <script type="text/javascript" src='<?php echo WEBJS; ?>stickyMojo.min.js'></script>
    <script type="text/javascript" src='<?php echo WEBINCLUDES; ?>ckeditor/ckeditor.js'></script>
    <script type="text/javascript" src='<?php echo WEBINCLUDES; ?>ckfinder/ckfinder.js'></script>
    <script type="text/javascript"
            src='<?php echo WEBINCLUDES; ?>DataTables-1.10.7/media/js/jquery.dataTables.js'></script>
    <script type="text/javascript" src='<?php echo WEBJS; ?>dataTables.tableTools.js'></script>
    <script type="text/javascript" src='<?php echo WEBJS; ?>dataTables.fixedHeader.js'></script>
    <script type="text/javascript" src='<?php echo WEBJS; ?>dataTables.fixedColumns.js'></script>
    <script type="text/javascript" src='<?php echo WEBJS; ?>jquery.validate.min.js'></script>
    <script type="text/javascript" src='<?php echo WEBJS; ?>additional-methods.min.js'></script>
    <script type="text/javascript" src='<?php echo WEBJS; ?>d3.min.js'></script>
    <script type="text/javascript" src='<?php echo WEBJS; ?>c3.min.js'></script>
    <script type="text/javascript" src='<?php echo WEBJS; ?>c3.chart.js'></script>
</head>
<body>
<noscript>To use this site, please enable Javascript in your Browser.</noscript>
<div id="wrapper">
    <div id="main">
        <?php $template->get_menu();?>
        <div id="maincontainer">