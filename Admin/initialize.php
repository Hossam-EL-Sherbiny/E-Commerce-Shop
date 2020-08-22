<?php
    include 'connect.php';

    $tempDirectory = 'includes/templates/';
    $css           = 'layout/css/';
    $js            = 'layout/js/';
    $lang          = 'includes/languages/';
    $func          = 'includes/functions/';

    // Include the important files.
    include $func . 'functions.php';
    include $lang . 'english.php';
    include $tempDirectory . 'header.php';

    if(!isset($noNavBar)){ include $tempDirectory . 'navBar.php';}