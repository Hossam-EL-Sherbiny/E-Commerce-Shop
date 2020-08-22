<?php

    ob_start();
    session_start();

    if(isset($_SESSION['UserSession']))
    {
        $pageTitle = 'Dashboard';
        include 'initialize.php';
        
        /* Start Dashboard Page*/

        $numUsers = 6;  // Number Of Latest Users

        $latestUsers = getLatest("*","users","UserID", $numUsers);  // Latest Users Array

        $numItems = 6; // Number Of Latest Items

        $latestItems = getLatest('*', 'items', 'Item_ID', $numItems);  // Latest Items Array

        $numComments = 4; // Number Of Comments

            ?>
                <div class="container home-stats text-center">
                    <h1>Dashboard</h1>
                    <div class="col-md-3">
                        <div class="stat st-members">
                            <i class="fa fa-users"></i>
                            <div class="info">
                                Total Members
                                <span>
                                <a href="Members.php"><?php echo countItems('UserID','users') ?></a>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="stat st-pending">
                            <i class="fa fa-user-plus"></i>
                            <div class="info">
                                Pending Members
                                <span> <a href="Members.php?do=Manage&page=Pending">
                                    <?php echo checkItem('RegisterStatus', 'users', 0)?>
                                   </a>
                            </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="stat st-items">
                           <i class="fa fa-tag"></i>
                            <div class="info">
                                Total Items
                                <span>
                                    <a href="items.php"><?php echo countItems('Item_ID','items') ?></a>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="stat st-comments">
                            <i class="fa fa-comments"></i>
                            <div class="info">
                                Total Comments
                                <span>
                                    <a href="Comments.php"><?php echo countItems('c_id','comments') ?></a>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class=" latest">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <?php  ?>
                                    <div class="panel-heading">
                                        <i class="fa fa-users"></i> Latest <?php echo $numUsers; ?> Registered Users
                                        <span class="toggle-info pull-right">
                                            <i class="fa fa-plus fa-lg"></i>
                                        </span>
                                    </div>
                                    <div class="panel-body">
                                        <ul class="list-unstyled latest-users">
                                            <?php
                                            if(!empty($latestUsers))
                                            {
                                                foreach ($latestUsers as $user)
                                                {
                                                    echo '<li>';
                                                        echo $user['UserName'];
                                                        echo '<a href="Members.php?do=Edit&userId=' . $user['UserID'] .' ">';
                                                            echo '<span class="btn btn-success pull-right">';
                                                                echo '<i class="fa fa-edit"></i>Edit';

                                                                    //
                                                                    if($user['RegisterStatus'] == 0)
                                                                    {
                                                                        echo "<a href='Members.php?do=Activate&userId=" . $user['UserID'] . " ' class='btn btn-info pull-right activate'> <i class='fa fa-check'></i>Activate</a>";
                                                                    }

                                                            echo "</span>";
                                                        echo '</a>';
                                                    echo '</li>';
                                                }
                                            }
                                            else
                                                {
                                                    echo 'There\'s no members to show';
                                                }
                                            ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <i class="fa fa-tag"></i>
                                            Latest <?php echo $numItems;?> Items
                                        <span class="toggle-info pull-right">
                                            <i class="fa fa-plus fa-lg"></i>
                                        </span>
                                    </div>
                                    <div class="panel-body">
                                        <ul class="list-unstyled latest-users">
                                            <?php

                                            if(!empty($latestItems))
                                            {
                                                foreach ($latestItems as $item)
                                                {
                                                    echo '<li>';
                                                    echo $item['Name'];
                                                    echo '<a href="items.php?do=Edit&itemId=' . $item['Item_ID'] .' ">';
                                                    echo '<span class="btn btn-success pull-right">';
                                                    echo '<i class="fa fa-edit"></i>Edit';

                                                    //
                                                    if($item['Approve'] == 0)
                                                    {
                                                        echo "<a href='items.php?do=Approve&itemId=" . $item['Item_ID'] . " ' class='btn btn-info pull-right activate'> <i class='fa fa-check'></i>Approve</a>";
                                                    }

                                                    echo "</span>";
                                                    echo '</a>';
                                                    echo '</li>';
                                                }
                                            }else
                                                {
                                                    echo 'There\'s no items to show';
                                                }
                                            ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Start Latest Comments-->
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="panel panel-default">
                                    <?php  ?>
                                    <div class="panel-heading">
                                        <i class="fa fa-comments-o"></i>
                                            Latest <?php echo $numComments;?> Comments
                                        <span class="toggle-info pull-right">
                                            <i class="fa fa-plus fa-lg"></i>
                                        </span>
                                    </div>
                                    <div class="panel-body">
                                        <?php
                                            // Select all users except admin.
                                            $stmt = $connect->prepare("SELECT
                                                                comments.*,
                                                                users.UserName AS Member
                                                            FROM
                                                                comments
                                                            INNER JOIN
                                                                users
                                                            ON
                                                                users.UserID = comments.user_id
                                                            ORDER BY
                                                                c_id DESC         
                                                                LIMIT $numComments
                                                                ");
                                            $stmt->execute();
                                            $comments = $stmt->fetchAll();   // Assign to variable

                                            if(!empty($comments))
                                            {
                                                foreach ($comments as $comment)
                                                {
                                                    echo "<div class='comment-box'>";
                                                        echo  '<span class="member-name">' . $comment['Member'] . '</span>';
                                                        echo  '<p class="member-comment">' . $comment['comment'] . '</p>';
                                                    echo "</div>";
                                                }
                                            }else
                                                {
                                                    echo 'There\'s no comments to show';
                                                }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Start Latest Comments-->

                    </div>
                </div>

            <?php
        /* End Dashboard Page*/

        include $tempDirectory .'footer.php';
    }
    else
        {
            header('Location: index2.php');
            exit();
        }

    ob_end_flush();
    ?>