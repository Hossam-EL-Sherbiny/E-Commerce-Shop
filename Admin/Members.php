<?php

    /*
     *==================================
     * == Manage Members Page.
     * == You Can Add | Edit | Delete Members From Here.
     * =================================
     * */

    session_start();
    $pageTitle = 'Members';

    if(isset($_SESSION['UserSession']))
    {
        include 'initialize.php';

        $do = isset($_GET['do']) ? $_GET['do'] : 'Manage';

        // Start Manage Page
        if($do == 'Manage')
        {
            $query = '';
            if(isset($_GET['page']) && $_GET['page'] == 'Pending')
            {
                $query = 'AND RegisterStatus = 0';
            }

            // Select all users except admin.
            $stmt = $connect->prepare("SELECT * FROM users WHERE GroupID != 1 $query ORDER BY UserID DESC");
            $stmt->execute();
            $rows = $stmt->fetchAll();   // Assign to variable

            if(!empty($rows))
            {

         ?>
            <h1 class="text-center">Manage Members</h1>
                <div class="container">
                    <div class="table-responsive">
                        <table class="main-table manage-members text-center table table-bordered">
                            <tr>
                                <td>#ID</td>
                                <td>Profile</td>
                                <td>Username</td>
                                <td>Email</td>
                                <td>Full Name</td>
                                <td>Registered Date</td>
                                <td>Control</td>
                            </tr>

                            <?php
                                foreach ($rows as $row)
                                {
                                    echo "<tr>";
                                        echo "<td>" . $row['UserID'] . "</td>";

                                        echo "<td>";
                                            if(empty($row['profile']))
                                            {
                                                echo 'No Image';
                                            }else
                                                {
                                                    echo " <img src='uploads/images/" . $row['profile'] . "' alt='' />";
                                                }
                                            echo "</td>";

                                        echo "<td>" . $row['UserName'] . "</td>";
                                        echo "<td>" . $row['Email'] . "</td>";
                                        echo "<td>" . $row['FullName'] . "</td>";
                                        echo "<td>" . $row['Date'] . "</td>";
                                        echo "<td>
                                                <a href='Members.php?do=Edit&userId=" . $row['UserID'] . " ' class='btn btn-success'> <i class='fa fa-edit'></i>Edit</a>
                                                <a href='Members.php?do=Delete&userId=" . $row['UserID'] . " ' class='btn btn-danger confirm'> <i class='fa fa-close'></i>Delete</a>";

                                                if($row['RegisterStatus'] == 0)
                                                {
                                                    echo "<a href='Members.php?do=Activate&userId=" . $row['UserID'] . " ' class='btn btn-info activate'> <i class='fa fa-check'></i>Activate</a>";
                                                }

                                              echo "</td>";
                                    echo "</tr>";
                                }
                            ?>

                        </table>
                    </div>
                    <a href="Members.php?do=Add" class="btn btn-primary"><i class="fa fa-plus"></i> Add New Member</a>
                </div>

                <?php
                    }else
                        {
                            echo '<div class="container">';
                                echo '<div class="alert alert-info">There\'s no members to show</div>';
                                echo '<a href="Members.php?do=Add" class="btn btn-primary"><i class="fa fa-plus"></i> Add New Member</a>';
                            echo '</div>';
                        }
                ?>

        <?php }


        // Add Member Page.
        elseif($do == 'Add')
        { ?>
            <h1 class="text-center">Add New Member</h1>

                <div class="container">
                    <form class="form-horizontal" action="?do=Insert" method="POST" enctype="multipart/form-data">  <!-- This Action will go to the same page & do will go to the Update Page-->
                        <!-- Start Username field-->
                            <div class="form-group form-group-lg">
                                <label class="col-sm-2 control-label">Username</label>
                                <div class="col-sm-10 col-md-4">
                                    <input type="text" name="username" class="form-control" autocomplete="off" required="required" placeholder="" />
                                </div>
                            </div>
                        <!-- End Username field-->

                        <!-- Start Password field-->
                        <div  class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Password</label>
                            <div class="col-sm-10 col-md-4">
                                <input type="password" name="password" class="password form-control" autocomplete="new-password" required="required" placeholder=""/>
                                <i class="show-pass fa fa-eye fa-1x"></i>
                            </div>
                        </div>
                        <!-- End Password field-->

                        <!-- Start Email field-->
                            <div class="form-group form-group-lg">
                                <label class="col-sm-2 control-label">Email</label>
                                <div class="col-sm-10 col-md-4">
                                    <input type="email" name="email" class="form-control" required="required" placeholder="" />
                                </div>
                            </div>
                        <!-- End Email field-->

                        <!-- Start FullName field-->
                            <div class="form-group form-group-lg">
                                <label class="col-sm-2 control-label">Full Name</label>
                                <div class="col-sm-10 col-md-4">
                                    <input type="text" name="full" class="form-control" required="required" placeholder="Full Name appear in your profile page" />
                                </div>
                            </div>
                        <!-- End FullName field-->

                        <!-- Start Profile Image field-->
                        <div class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Profile Image</label>
                            <div class="col-sm-10 col-md-4">
                                <input type="file" name="profile" class="form-control" required="required" />
                            </div>
                        </div>
                        <!-- End Profile Image field-->

                        <!-- Start Submit field-->
                            <div class="form-group">
                                <div class=" col-sm-offset-2 col-sm-10">
                                    <input type="submit" value="Add Member" class="btn btn-primary btn-lg" />
                                </div>
                            </div>
                        <!-- End Submit field-->

                    </form>
                </div>
  <?php }

        // Insert Member Page.
        elseif ($do == 'Insert')
        {

            if($_SERVER['REQUEST_METHOD'] == 'POST')
            {
                // Insert Member Page
                echo "<h1 class='text-center'>Insert Member</h1>";
                echo "<div class='container'>";

                // Upload Variables.
                $profileName = $_FILES['profile']['name'];
                $profileSize = $_FILES['profile']['type'];
                $profileTemp = $_FILES['profile']['tmp_name'];
                $profileType = $_FILES['profile']['size'];

                // List Of Allowed File Typed To Upload
                $profileAllowedExtension = array("jpeg", "jpg", "png", "gif");

                // Get Profile Extensions
                $profileExtension = explode('.', $profileName);
                $dump = strtolower(end($profileExtension));

                // Get Variables From The Form
                $user  = $_POST['username'];
                $pass  = $_POST['password'];
                $email = $_POST['email'];
                $name  = $_POST['full'];
                $hasPass = sha1($_POST['password']);

                // Validate The Form.
                $formErrors = array();

                if(strlen($user) < 4)
                {
                    $formErrors [] = 'Username cant not be less than <strong>4 character</strong>';
                }

                if(strlen($user) > 20)
                {
                    $formErrors [] = 'Username can not be more than <strong>20 character</strong>';
                }

                if(empty($user))
                {
                    $formErrors[] = 'Username can not be <strong>Empty</strong>';
                }

                if(empty($pass))
                {
                    $formErrors[] = 'Username can not be <strong>Empty</strong>';
                }

                if(empty($name))
                {
                    $formErrors[] = 'Full Name can not be <strong>Empty</strong>';
                }

                if(empty($email))
                {
                    $formErrors[] = 'Email can not be <strong>Empty</strong>';
                }

                if(!empty($profileName) && !in_array($dump, $profileAllowedExtension))
                {
                    $formErrors[] = 'This extension is not <strong>Allowed</strong>';
                }

                if(empty($profileName))
                {
                    $formErrors[] = 'Image is <strong>Required</strong>';
                }

                if($profileSize > 4194304)
                {
                    $formErrors[] = 'Profile cant be larger than <strong>4MB</strong>';
                }

                // Loop into errors arrays and echo it.
                foreach($formErrors as $error)
                {
                    echo '<div class="alert alert-danger">' . $error . '</div>';
                }


                // Check if there's no proceed the update operation.
                if(empty($formErrors))
                {
                    $profile = rand(0, 100000) . '_' . $profileName;
                    move_uploaded_file($profileTemp, "uploads\images\\" . $profile);

                    // Check if the user exist in Database !
                    $check = checkItem("Username", "users", $user);
                    if($check == 1)
                    {
                        $theMsg = '<div class="alert alert-danger"> Sorry this user exist</div>';
                        redirectHome($theMsg,'back');
                    }
                    else
                        {
                        // Insert UserInfo To Database.
                        $stmt = $connect->prepare("INSERT INTO users(UserName, Password, Email, FullName, RegisterStatus, Date, profile)
                                                            VALUES (:Zuser, :Zpass, :Zmail, :Zname, 1, now(), :Zprofile)");
                        $stmt->execute(array(
                                'Zuser'    => $user,
                                'Zpass'    => $hasPass,
                                'Zmail'    => $email,
                                'Zname'    => $name,
                                'Zprofile' => $profile
                        ));

                        // Echo Success Message
                        $theMsg =  "<div class='alert alert-success'>" . $stmt->rowCount(). 'Record Inserted</div>';
                        redirectHome($theMsg,'back');

                        }
                }

            }
            else
                {
                    echo "<div class='container'>";
                    $theMsg = '<div class="alert alert-danger">Sorry you can not browse this page directly</div>';
                    redirectHome($theMsg);
                    echo "</div>";
                }
            echo "</div>";

        }

        // Edit Page.
        elseif ($do == 'Edit')
        {
            // Check if GET REQUEST userId is numeric & Get the integer value of it .
            $userid = isset($_GET['userId']) && is_numeric($_GET['userId']) ? intval($_GET['userId']) : 0;

            $stmt = $connect->prepare("SELECT * FROM users WHERE UserID = ? LIMIT 1");  // Select all data depend on this id .

            $stmt->execute(array($userid));     // Execute Query.
            $row = $stmt->fetch();      // Fetch The Data.
            $count = $stmt->rowCount(); // The Row Count

            if($count > 0)
            { ?>      <!-- If There's Such ID , Show The Form. -->

                <h1 class="text-center">Edit Member</h1>

                <div class="container">
                    <form class="form-horizontal" action="?do=Update" method="POST">  <!-- This Action will go to the same page & do will go to the Update Page-->
                        <input type="hidden" name="userid" value="<?php echo $userid?>" />
                        <!-- Start Username field-->
                            <div class="form-group form-group-lg">
                                <label class="col-sm-2 control-label">Username</label>
                                <div class="col-sm-10 col-md-4">
                                    <input type="text" name="username" class="form-control" value="<?php echo $row['UserName']?> " autocomplete="off" required="required"/>
                                </div>
                            </div>
                        <!-- End Username field-->

                        <!-- Start Password field-->
                        <div  class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Password</label>
                            <div class="col-sm-10 col-md-4">
                                <input type="hidden" name="oldPassword" value="<?php echo $row['Password']?>" />
                                <input type="password" name="newPassword" class="form-control" autocomplete="new-password" placeholder="Leave it blank if you dont want to change it"/>
                            </div>
                        </div>
                        <!-- End Password field-->

                        <!-- Start Email field-->
                            <div class="form-group form-group-lg">
                                <label class="col-sm-2 control-label">Email</label>
                                <div class="col-sm-10 col-md-4">
                                    <input type="email" name="email" value="<?php echo $row['Email']?>" class="form-control" required="required"/>
                                </div>
                            </div>
                        <!-- End Email field-->

                        <!-- Start FullName field-->
                            <div class="form-group form-group-lg">
                                <label class="col-sm-2 control-label">Fullname</label>
                                <div class="col-sm-10 col-md-4">
                                    <input type="text" name="full" value="<?php echo $row['FullName']?>" class="form-control" required="required"/>
                                </div>
                            </div>
                        <!-- End FullName field-->

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
            echo "<h1 class='text-center'>Update Member</h1>";
            echo "<div class='container'> ";

            if($_SERVER['REQUEST_METHOD'] == 'POST')
            {
                //Get variables form the form
                $user       = $_POST['username'];
                $id         = $_POST['userid'];
                $email      = $_POST['email'];
                $fullName   = $_POST['full'];

                // Password Trick
                // Condition ? True : False;

                $pass = empty($_POST['newPassword']) ? $_POST['oldPassword'] : sha1($_POST['newPassword']);

                // Validate Form
                $formErrors = array();

                if(strlen($user) < 4)
                {
                    $formErrors [] = 'Username cant not be less than <strong>4 character</strong>';
                }

                if(strlen($user) > 20)
                {
                    $formErrors [] = 'Username can not be more than <strong>20 character</strong>';
                }

                if(empty($user))
                {
                    $formErrors[] = 'Username can not be <strong>Empty</strong>';
                }

                if(empty($fullName))
                {
                    $formErrors[] = 'Full Name can not be <strong>Empty</strong>';
                }

                if(empty($email))
                {
                    $formErrors[] = 'Email can not be <strong>Empty</strong>';
                }

                // Loop into errors arrays and echo it.
                foreach($formErrors as $error)
                {
                    echo '<div class="alert alert-danger">' . $error . '</div>';
                }

                // Check if there's no error proceed the update operation.
                if(empty($formErrors))
                {
                    $stmt2 = $connect->prepare("SELECT * FROM users WHERE UserName = ? AND UserID != ?");

                    $stmt2->execute(array($user, $id));

                    $count = $stmt2->rowCount();

                    if($count == 1)
                    {
                        echo '<div class="alert alert-danger">Sorry this user is exist</div>';
                    }else{

                    // Update Database with this info.
                    $stmt = $connect->prepare("UPDATE users SET UserName = ?, Email = ?, FullName = ?, Password = ? WHERE UserID = ? ");
                    $stmt->execute(array($user, $email, $fullName, $pass, $id));
                    $theMsg =  "<div class='alert alert-success'>" . $stmt->rowCount(). ' Record Updated</div> ';
                    redirectHome($theMsg, 'back');
                        }
                }

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
            echo "<h1 class='text-center'>Delete Member</h1>";
            echo "<div class='container'> ";

                // Check if GET REQUEST userId is numeric $ GET the integer value of it !
                $userid = isset($_GET['userId']) && is_numeric($_GET['userId']) ? intval($_GET['userId']) : 0;

                // Select all data depend on this ID.
                $check = checkItem('userid','users',$userid);

                if($check > 0)
                {
                    $stmt = $connect->prepare("DELETE FROM users WHERE UserID = :Zuser");
                    $stmt->bindParam(":Zuser", $userid);
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
        elseif($do == 'Activate')
        {
            echo "<h1 class='text-center'>Activate Page</h1>";
            echo "<div class='container'>";

            // Check if GET REQUEST userId is numeric & GET the integer value of it !
            $userid = isset($_GET['userId']) && is_numeric($_GET['userId']) ? intval($_GET['userId']) : 0;

            // Select all data depend on this ID.
            $check = checkItem('userid', 'users', $userid);

            if($check > 0)
            {
                $stmt = $connect->prepare("UPDATE users SET RegisterStatus = 1 WHERE UserID = ?");

                $stmt->execute(array($userid));

                $theMsg =  "<div class='alert alert-success'>" . $stmt->rowCount(). ' Record Activated</div> ';
                redirectHome($theMsg);
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
