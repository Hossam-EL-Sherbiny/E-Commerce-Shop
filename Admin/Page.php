<?php

    /* Categories => [Manage | Edit | Update | Add | Insert | Delete | Statistics]
       Condition ? True : False
    */
    
    // Shortcut of in condition.
    $do = isset($_GET['do']) ? $_GET['do'] : 'Manage';

    /*
    if(isset($_GET['do']))
    {
        $do = $_GET['do'];
    }
    else
        {
            $do = 'Mange';
        }
    */

    // If the page is main page
    if ($do == 'Manage')
    {
        echo 'Welcome you are in Manage Category page';
        echo '<a href="Page.php?do=Add">Add New Category</a>';
    }
    elseif($do == 'Add')
    {
        echo 'Welcome you are in Add Category page';
    }
    elseif($do == 'Insert')
    {
        echo 'Welcome you are in Insert Category page';
    }
    else
        {
            echo 'Error There\'s  no page with this name';
        }