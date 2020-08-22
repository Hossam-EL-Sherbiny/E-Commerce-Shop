<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title><?php getTitle();?> </title>
        <link rel="stylesheet" href="<?php echo $css; ?>bootstrap.min.css" />
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
        <link rel="stylesheet" href="<?php echo $css; ?>jquery-ui.css" />
        <link rel="stylesheet" href="<?php echo $css; ?>jquery.selectBoxIt.css" />
        <link rel="stylesheet" href="<?php echo $css; ?>front-end.css" />
    </head>
    <body>

    <div class="upper-bar">
        <div class="container">

            <?php
                if(isset($_SESSION['userSession']))
                { ?>

                    <img class="my-image img-thumbnail img-circle" src="Hacker.jpg" alt="" />
                    <div class="btn-group my-info">
                        <span class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                            <?php echo $sessionUser?>
                            <span class="caret"></span>
                        </span>
                            <ul class="dropdown-menu">
                                <li><a href="Profile.php">My Profile</a></li>
                                <li><a href="New-Add.php">New Item</a></li>
                                <li><a href="Profile.php#my-ads">My Items</a></li>
                                <li><a href="Logout2.php">Logout</a></li>
                            </ul>
                    </div>

                    <?php

                }
                else
                    {
            ?>
                <a href="Login.php"><span class="pull-right">Login/Signup</span></a>
            <?php
                    }
            ?>

        </div>
    </div>

    <nav class="navbar navbar-inverse">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-nav" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index2.php">Home Page</a>
            </div>

            <div class="collapse navbar-collapse" id="app-nav">
                <ul class="nav navbar-nav navbar-right">
                    <?php
                    $allCates = getAllFrom('*', 'categories', 'where parent = 0', '', 'ID', 'ASC');
                    foreach ($allCates as $cate)
                    {
                        echo '<li><a href="Categories2.php?pageId='.$cate['ID'].'">'.$cate['Name'].'</a></li>';
                    }
                    ?>
                </ul>

            </div>
        </div>
    </nav>
