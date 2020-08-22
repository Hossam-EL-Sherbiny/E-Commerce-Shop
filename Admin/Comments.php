<?php

/*
 *==================================
 * == Manage Comments Page.
 * == You Can Edit | Delete | Approve Members From Here.
 * =================================
 * */

session_start();
$pageTitle = 'Comments';

    if(isset($_SESSION['UserSession']))
    {
        include 'initialize.php';

        $do = isset($_GET['do']) ? $_GET['do'] : 'Manage';

        // Start Manage Page
        if($do == 'Manage')
        {
            // Select all users except admin.
            $stmt = $connect->prepare("SELECT
                                                    comments.*,
                                                    items.Name AS Item_Name,
                                                    users.UserName AS Member
                                                FROM
                                                    comments
                                                INNER JOIN
                                                    items
                                                ON      
                                                    items.Item_ID = comments.item_id
                                                INNER JOIN
                                                    users
                                                ON 
                                                   users.UserID = comments.user_id
                                                ORDER BY 
                                                    c_id DESC   
                                                   ");
            $stmt->execute();
            $comments = $stmt->fetchAll();   // Assign to variable

            if(!empty($comments))
            {

            ?>
            <h1 class="text-center">Manage Comments</h1>
            <div class="container">
                <div class="table-responsive">
                    <table class="main-table text-center table table-bordered">
                        <tr>
                            <td>ID</td>
                            <td>Comment</td>
                            <td>Item Name</td>
                            <td>User Name</td>
                            <td>Added Date</td>
                            <td>Control</td>
                        </tr>

                        <?php
                        foreach ($comments as $comment)
                        {
                            echo "<tr>";
                            echo "<td>" . $comment['c_id'] . "</td>";
                            echo "<td>" . $comment['comment'] . "</td>";
                            echo "<td>" . $comment['Item_Name'] . "</td>";
                            echo "<td>" . $comment['Member'] . "</td>";
                            echo "<td>" . $comment['comment_date'] . "</td>";
                            echo "<td>
                                    <a href='Comments.php?do=Edit&commentId=" . $comment['c_id'] . " ' class='btn btn-success'> <i class='fa fa-edit'></i>Edit</a>
                                    <a href='Comments.php?do=Delete&commentId=" . $comment['c_id'] . " ' class='btn btn-danger confirm'> <i class='fa fa-close'></i>Delete</a>";

                            if($comment['status'] == 0)
                            {
                                echo "<a href='Comments.php?do=Approve&commentId=" . $comment['c_id'] . " ' class='btn btn-info activate'> <i class='fa fa-check'></i>Approve</a>";
                            }

                            echo "</td>";
                            echo "</tr>";
                        }
                        ?>

                    </table>
                </div>
            </div>

             <?php
                }else
                    {
                        echo '<div class="container">';
                            echo '<div class="alert alert-info">There\'s no comments to show</div>';
                        echo '</div>';
                    }
            ?>
    <?php
        }

    // Edit Page.
    elseif ($do == 'Edit')
    {
        // Check if GET REQUEST userId is numeric & Get the integer value of it .
        $commentId = isset($_GET['commentId']) && is_numeric($_GET['commentId']) ? intval($_GET['commentId']) : 0;

        $stmt = $connect->prepare("SELECT * FROM comments WHERE c_id = ?");  // Select all data depend on this id .

        $stmt->execute(array($commentId));     // Execute Query.
        $row = $stmt->fetch();      // Fetch The Data.
        $count = $stmt->rowCount(); // The Row Count

        if($count > 0)
        { ?>      <!-- If There's Such ID , Show The Form. -->

            <h1 class="text-center">Edit Comment</h1>

            <div class="container">
                <form class="form-horizontal" action="?do=Update" method="POST">  <!-- This Action will go to the same page & do will go to the Update Page-->
                    <input type="hidden" name="commentId" value="<?php echo $commentId?>" />

                    <!-- Start Comment field-->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Comment</label>
                        <div class="col-sm-10 col-md-4">
                            <textarea class="form-control" name="comment"> <?php echo $row['comment']; ?> </textarea>
                        </div>
                    </div>
                    <!-- End Comment field-->

                    <!-- Start Submit Field-->
                    <div class="form-group">
                        <div class=" col-sm-offset-2 col-sm-10">
                            <input type="submit" value="Save" class="btn btn-primary btn-lg" />
                        </div>
                    </div>
                    <!-- End Submit Field-->

                </form>
            </div>
        <?php }
        else
        {
            echo "<div class='container'>";
            $theMsg = '<div class="alert alert-danger">There is no such id !</div>';
            redirectHome($theMsg);
            echo "</div>";
        }
    }

    // Update Page.
    elseif($do == 'Update')
    {
        echo "<h1 class='text-center'>Update Comment</h1>";
        echo "<div class='container'> ";

        if($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            //Get variables form the form
            $commentId       = $_POST['commentId'];
            $comment         = $_POST['comment'];

            // Update Database with this info.
            $stmt = $connect->prepare("UPDATE comments SET comment = ? WHERE c_id = ? ");

            $stmt->execute(array($comment, $commentId));

            $theMsg =  "<div class='alert alert-success'>" . $stmt->rowCount(). ' Record Updated</div> ';

            redirectHome($theMsg, 'back');

        }
        else
        {
            $theMsg = '<div class="alert alert-danger"> Sorry you cant browse this page directly</div>';
            redirectHome($theMsg);
        }
        echo "</div>";
    }


    // Delete Members Page.
    elseif ($do == 'Delete')
    {
        echo "<h1 class='text-center'>Delete Comment</h1>";
        echo "<div class='container'> ";

        // Check if GET REQUEST userId is numeric $ GET the integer value of it !
        $comDelete = isset($_GET['commentId']) && is_numeric($_GET['commentId']) ? intval($_GET['commentId']) : 0;

        // Select all data depend on this ID.
        $check = checkItem('c_id','comments',$comDelete);

        if($check > 0)
        {
            $stmt = $connect->prepare("DELETE FROM comments WHERE c_id = :Icomment");
            $stmt->bindParam(":Icomment", $comDelete);
            $stmt->execute();

            $theMsg =  "<div class='alert alert-success'>" . $stmt->rowCount(). ' Record Deleted</div> ';
            redirectHome($theMsg,'back');
        }
        else
        {
            $theMsg = '<div class="alert alert-danger"> This ID is not exist</div>';
            redirectHome($theMsg);
        }

        echo '</div>';
    }

    // Activate Page
    elseif($do == 'Approve')
    {
        echo "<h1 class='text-center'>Approve Comment</h1>";
        echo "<div class='container'>";

        // Check if GET REQUEST userId is numeric & GET the integer value of it !
        $appComment = isset($_GET['commentId']) && is_numeric($_GET['commentId']) ? intval($_GET['commentId']) : 0;

        // Select all data depend on this ID.
        $check = checkItem('c_id', 'comments', $appComment);

        if($check > 0)
        {
            $stmt = $connect->prepare("UPDATE comments SET status = 1 WHERE c_id = ?");

            $stmt->execute(array($appComment));

            $theMsg =  "<div class='alert alert-success'>" . $stmt->rowCount(). ' Record Approved</div> ';
            redirectHome($theMsg,'back');
        }
        else
        {
            $theMsg = '<div class="alert alert-danger"> This ID is not exist</div>';
            redirectHome($theMsg);
        }

        echo '</div>';
    }

    include $tempDirectory . 'footer.php';
}
else
{
    header('Location: index2.php');
    exit();
}

