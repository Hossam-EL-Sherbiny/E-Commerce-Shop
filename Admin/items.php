<?php
    // Items Page.

    ob_start();
    session_start();

    $pageTitle = 'Items';
    if(isset($_SESSION['UserSession']))
    {
        include 'initialize.php';

        $do = isset($_GET['do']) ? $_GET['do'] : 'Manage';

        if($do == 'Manage')
        {
            $stmt = $connect->prepare("SELECT
                                                    items.*,
                                                    categories.Name
                                                AS
                                                    category_name,
                                                    users.UserName
                                                FROM
                                                    items
                                                INNER JOIN
                                                    categories
                                                ON
                                                    categories.ID = items.Cat_ID
                                                INNER JOIN
                                                    users
                                                ON
                                                    users.UserID = items.Member_ID
                                                ORDER BY
                                                    Item_ID DESC     
                                                    ");
            $stmt->execute();
            $items = $stmt->fetchAll();   // Assign to variable

            if(!empty($items))
            {
?>
            <h1 class="text-center">Manage Items</h1>
            <div class="container">
                <div class="table-responsive">
                    <table class="main-table text-center table table-bordered">
                        <tr>
                            <td>#ID</td>
                            <td>Name</td>
                            <td>Description</td>
                            <td>Price</td>
                            <td>Adding Date</td>
                            <td>Category</td>
                            <td>Username</td>
                            <td>Control</td>
                        </tr>

                        <?php
                        foreach ($items as $item)
                        {
                            echo "<tr>";
                            echo "<td>" . $item['Item_ID'] . "</td>";
                            echo "<td>" . $item['Name'] . "</td>";
                            echo "<td>" . $item['Description'] . "</td>";
                            echo "<td>" . $item['Price'] . "</td>";
                            echo "<td>" . $item['Add_Date'] . "</td>";
                            echo "<td>" . $item['category_name'] . "</td>";
                            echo "<td>" . $item['UserName'] . "</td>";
                            echo "<td>
                                        <a href='items.php?do=Edit&itemId=" . $item['Item_ID'] . " ' class='btn btn-success'> <i class='fa fa-edit'></i>Edit</a>
                                        <a href='items.php?do=Delete&itemId=" . $item['Item_ID'] . " ' class='btn btn-danger confirm'> <i class='fa fa-close'></i>Delete</a>";
                                        if($item['Approve'] == 0)
                                        {
                                            echo "<a href='items.php?do=Approve&itemId=" . $item['Item_ID'] . " ' class='btn btn-info activate'> <i class='fa fa-check'></i>Approve</a>";
                                        }
                            echo "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </table>
                </div>
                <a href="items.php?do=Add" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> New Items</a>
            </div>

            <?php
            }
                else
                    {
                        echo '<div class="container">';
                        echo '<div class="alert alert-info">There\'s no items to show</div>';
                        echo '<a href="items.php?do=Add" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> New Items</a>';
                        echo '</div>';
                    }
            ?>

        <?php
        }

        /*
         * Add Page
         * */
        elseif ($do == 'Add')
        {
        ?>
            <h1 class="text-center">Add New Item</h1>
            <div class="container">
                <form class="form-horizontal" action="?do=Insert" method="POST">

                    <!-- Start Name Field-->
                        <div class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Name</label>
                            <div class="col-sm-10 col-md-6">
                                <input
                                    type="text"
                                    name="name"
                                    class="form-control"
                                    required="required"
                                    placeholder="Name Of The Item" />
                            </div>
                        </div>
                    <!-- End Name Field-->

                    <!-- Start Description Field-->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Description</label>
                        <div class="col-sm-10 col-md-6">
                            <input
                                type="text"
                                name="description"
                                class="form-control"
                                required="required"
                                placeholder="Description Of The Item" />
                        </div>
                    </div>
                    <!-- End Description Field-->

                    <!-- Start Price Field-->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Price</label>
                        <div class="col-sm-10 col-md-6">
                            <input
                                type="text"
                                name="price"
                                class="form-control"
                                required="required"
                                placeholder="Price Of The Item" />
                        </div>
                    </div>
                    <!-- End Price Field-->

                    <!-- Start Country_Made Field-->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Country</label>
                        <div class="col-sm-10 col-md-6">
                            <input
                                type="text"
                                name="country"
                                class="form-control"
                                required="required"
                                placeholder="Country Of Made" />
                        </div>
                    </div>
                    <!-- End Country_Made Field-->

                    <!-- Start Status Field-->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Status</label>
                        <div class="col-sm-10 col-md-6">
                            <select name="status">
                                <option value="0">...</option>
                                <option value="1">New</option>
                                <option value="2">Like New</option>
                                <option value="3">Used</option>
                                <option value="4">Old</option>
                            </select>
                        </div>
                    </div>
                    <!-- End Status Field-->

                    <!-- Start Members Field-->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Member</label>
                        <div class="col-sm-10 col-md-6">
                            <select name="member">
                                <option value="0">...</option>

                                <?php
                                    $allMembers = getAllFrom("*", "users", "", "", "UserID");
                                    foreach ($allMembers as $user)
                                    {
                                        echo "<option value=' " . $user['UserID'] . "'> " . $user['UserName'] . "</option>";
                                    }
                                ?>

                            </select>
                        </div>
                    </div>
                    <!-- End Members Field-->

                    <!-- Start Categories Field-->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Category</label>
                        <div class="col-sm-10 col-md-6">
                            <select name="category">

                                <?php
                                $allCats = getAllFrom("*", "categories", "where parent = 0", "", "ID");
                                foreach ($allCats as $cat)
                                {
                                    echo "<option value=' " . $cat['ID'] . "'> " . $cat['Name'] . "</option>";

                                    $childCats = getAllFrom("*", "categories", "where parent = {$cat['ID']}", "", "ID");
                                    foreach ($childCats as $child)
                                    {
                                        echo "<option value=' " . $child['ID'] . "'>--- " . $child['Name'] . "</option>";
                                    }
                                }
                                ?>

                            </select>
                        </div>
                    </div>
                    <!-- End Categories Field-->

                    <!-- Start Tags Field-->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Tags</label>
                        <div class="col-sm-10 col-md-6">
                            <input
                                    type="text"
                                    name="tags"
                                    class="form-control"
                                    placeholder="Separate Tags with comma (,)" />
                        </div>
                    </div>
                    <!-- End Tags Field-->

                    <!-- Start Submit Field-->
                    <div class="form-group form-group-lg">
                        <div class="col-sm-offset-2 col-sm-10">
                            <input type="submit" value="Add Item" class="btn btn-primary btn-sm" />
                        </div>
                    </div>
                    <!-- End Submit Field-->

                </form>
            </div>
        <?php

        }

        // INSERT PAGE.
        elseif ($do == 'Insert')
        {
            if($_SERVER['REQUEST_METHOD'] == 'POST')
            {
                echo "<h1 class='text-center'>Insert Item</h1>";
                echo "<div class='container'>";

                // Get Variables From The Form
                $name       = $_POST['name'];
                $describe   = $_POST['description'];
                $price      = $_POST['price'];
                $country    = $_POST['country'];
                $status     = $_POST['status'];
                $member     = $_POST['member'];
                $category   = $_POST['category'];
                $tags   = $_POST['tags'];

                // Validate The Form.
                $formErrors = array();

                if(empty($name))
                {
                    $formErrors [] = 'Name cant not be <strong>Empty</strong>';
                }

                if(empty($describe))
                {
                    $formErrors [] = 'Description cant not be <strong>Empty</strong>';
                }

                if(empty($price))
                {
                    $formErrors[] = 'Price cant not be <strong>Empty</strong>';
                }

                if(empty($country))
                {
                    $formErrors[] = 'Country cant not be <strong>Empty</strong>';
                }

                if(($status == 0))
                {
                    $formErrors[] = 'You must choose the <strong>Status</strong>';
                }

                if($member == 0)
                {
                    $formErrors[] = 'You must choose the <strong>Member</strong>';
                }

                if($category == 0)
                {
                    $formErrors[] = 'You must choose the <strong>Category</strong>';
                }

                // Loop into errors arrays and echo it.
                foreach($formErrors as $error)
                {
                    echo '<div class="alert alert-danger">' . $error . '</div>';
                }

                // Check if there's no proceed the update operation.
                if(empty($formErrors))
                {

                        // Insert UserInfo To Database.
                        $stItems = $connect->prepare("INSERT INTO
                                                                    items
                                                                    (Name,
                                                                     Description,
                                                                      Price,
                                                                       Country_Made,
                                                                        Status,
                                                                         Add_Date,
                                                                          Cat_ID,
                                                                           Member_ID,
                                                                            tags)
                                                            VALUES
                                                             (:Iname,
                                                              :Idesc,
                                                               :Iprice,
                                                                :Icountry,
                                                                 :Istatus,
                                                                  now(),
                                                                   :Icat,
                                                                    :Imember,
                                                                     :Itags)");
                    $stItems->execute(array(
                            'Iname'     => $name,
                            'Idesc'     => $describe,
                            'Iprice'    => $price,
                            'Icountry'  => $country,
                            'Istatus'   => $status,
                            'Icat'      => $category,
                            'Imember'   => $member,
                            'Itags'      => $tags
                        ));

                        // Echo Success Message
                        $theMsg =  "<div class='alert alert-success'>" . $stItems->rowCount(). 'Record Inserted</div>';
                        redirectHome($theMsg,'back');

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


        elseif ($do == 'Edit')
        {
            // Check if GET REQUEST itemId is numeric & Get the integer value of it .
            $itemId = isset($_GET['itemId']) && is_numeric($_GET['itemId']) ? intval($_GET['itemId']) : 0;

            $stmt = $connect->prepare("SELECT * FROM items WHERE Item_ID = ?");  // Select all data depend on this id .

            $stmt->execute(array($itemId));     // Execute Query.
            $item = $stmt->fetch();      // Fetch The Data.
            $count = $stmt->rowCount(); // The Row Count

            if($count > 0)
            { ?>

                <h1 class="text-center">Edit Item</h1>
                <div class="container">
                    <form class="form-horizontal" action="?do=Update" method="POST">
                        <input type="hidden" name="itemid" value="<?php echo $itemId; ?>" />

                        <!-- Start Name Field-->
                        <div class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Name</label>
                            <div class="col-sm-10 col-md-6">
                                <input
                                        type="text"
                                        name="name"
                                        class="form-control"
                                        required="required"
                                        placeholder="Name Of The Item"
                                        value="<?php echo $item['Name']; ?>" />
                            </div>
                        </div>
                        <!-- End Name Field-->

                        <!-- Start Description Field-->
                        <div class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Description</label>
                            <div class="col-sm-10 col-md-6">
                                <input
                                        type="text"
                                        name="description"
                                        class="form-control"
                                        required="required"
                                        placeholder="Description Of The Item"
                                        value="<?php echo $item['Description']; ?>"/>
                            </div>
                        </div>
                        <!-- End Description Field-->

                        <!-- Start Price Field-->
                        <div class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Price</label>
                            <div class="col-sm-10 col-md-6">
                                <input
                                        type="text"
                                        name="price"
                                        class="form-control"
                                        required="required"
                                        placeholder="Price Of The Item"
                                        value="<?php echo $item['Price']; ?>"/>
                            </div>
                        </div>
                        <!-- End Price Field-->

                        <!-- Start Country_Made Field-->
                        <div class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Country</label>
                            <div class="col-sm-10 col-md-6">
                                <input
                                        type="text"
                                        name="country"
                                        class="form-control"
                                        required="required"
                                        placeholder="Country Of Made"
                                        value="<?php echo $item['Country_Made']; ?>"/>
                            </div>
                        </div>
                        <!-- End Country_Made Field-->

                        <!-- Start Status Field-->
                        <div class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Status</label>
                            <div class="col-sm-10 col-md-6">
                                <select name="status">
                                    <option value="1"<?php if($item['Status'] == 1) { echo 'selected'; } ?>>New</option>
                                    <option value="2"<?php if($item['Status'] == 2) { echo 'selected'; } ?>>Like New</option>
                                    <option value="3"<?php if($item['Status'] == 3) { echo 'selected'; } ?>>Used</option>
                                    <option value="4"<?php if($item['Status'] == 4) { echo 'selected'; } ?>>Old</option>
                                </select>
                            </div>
                        </div>
                        <!-- End Status Field-->

                        <!-- Start Members Field-->
                        <div class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Member</label>
                            <div class="col-sm-10 col-md-6">
                                <select name="member">

                                    <?php
                                    $statement = $connect->prepare('SELECT * FROM users');
                                    $statement->execute();
                                    $users = $statement->fetchAll();
                                    foreach ($users as $user)
                                    {
                                        echo "<option value=' " . $user['UserID'] . "'";
                                        if($item['Member_ID'] == $user['UserID']) { echo 'selected'; }
                                        echo "> " . $user['UserName'] . "</option>";
                                    }
                                    ?>

                                </select>
                            </div>
                        </div>
                        <!-- End Members Field-->

                        <!-- Start Categories Field-->
                        <div class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Category</label>
                            <div class="col-sm-10 col-md-6">
                                <select name="category">
                                    <option value="0">...</option>

                                    <?php
                                    $stCategory = $connect->prepare('SELECT * FROM categories');
                                    $stCategory->execute();
                                    $cats = $stCategory->fetchAll();
                                    foreach ($cats as $cat)
                                    {
                                        echo "<option value=' " . $cat['ID'] . "'";
                                        if($item['Cat_ID'] == $cat['ID']) { echo 'selected'; }
                                        echo "> " . $cat['Name'] . "</option>";
                                    }
                                    ?>

                                </select>
                            </div>
                        </div>
                        <!-- End Categories Field-->

                        <!-- Start Tags Field-->
                        <div class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Tags</label>
                            <div class="col-sm-10 col-md-6">
                                <input
                                        type="text"
                                        name="tags"
                                        class="form-control"
                                        placeholder="Separate Tags with comma (,)" />
                            </div>
                        </div>
                        <!-- End Tags Field-->

                        <!-- Start Submit Field-->
                        <div class="form-group form-group-lg">
                            <div class="col-sm-offset-2 col-sm-10">
                                <input type="submit" value="Save Item" class="btn btn-primary btn-sm" />
                            </div>
                        </div>
                        <!-- End Submit Field-->

                    </form>

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
                                                        WHERE
                                                            item_id = ?    
                                                            ");
                    $stmt->execute(array($itemId));
                    $rows = $stmt->fetchAll();   // Assign to variable

                    if(!empty($rows))
                    {

                    ?>
                    <h1 class="text-center">Manage [ <?php echo $item['Name']; ?> ] Comments</h1>
                    <div class="container">
                        <div class="table-responsive">
                            <table class="main-table text-center table table-bordered">
                                <tr>
                                    <td>Comment</td>
                                    <td>User Name</td>
                                    <td>Added Date</td>
                                    <td>Control</td>
                                </tr>

                                <?php
                                foreach ($rows as $row)
                                {
                                    echo "<tr>";
                                    echo "<td>" . $row['comment'] . "</td>";
                                    echo "<td>" . $row['Member'] . "</td>";
                                    echo "<td>" . $row['comment_date'] . "</td>";
                                    echo "<td>
                                        <a href='Comments.php?do=Edit&commentId=" . $row['c_id'] . " ' class='btn btn-success'> <i class='fa fa-edit'></i>Edit</a>
                                        <a href='Comments.php?do=Delete&commentId=" . $row['c_id'] . " ' class='btn btn-danger confirm'> <i class='fa fa-close'></i>Delete</a>";

                                    if($row['status'] == 0)
                                    {
                                        echo "<a href='Comments.php?do=Approve&commentId=" . $row['c_id'] . " ' class='btn btn-info activate'> <i class='fa fa-check'></i>Approve</a>";
                                    }

                                    echo "</td>";
                                    echo "</tr>";
                                }
                                ?>

                            </table>
                        </div>
                    </div>
                        <?php } ?>
                </div>

            <?php
            }
            else
            {
                echo "<div class='container'>";
                $theMsg = '<div class="alert alert-danger">There is no such id !</div>';
                redirectHome($theMsg);
                echo "</div>";
            }
        }


        elseif ($do == 'Update')
        {

            echo "<h1 class='text-center'>Update Item</h1>";
            echo "<div class='container'> ";

            if($_SERVER['REQUEST_METHOD'] == 'POST')
            {
                //Get variables form the form
                $id          = $_POST['itemid'];
                $name        = $_POST['name'];
                $describe    = $_POST['description'];
                $price       = $_POST['price'];
                $country     = $_POST['country'];
                $status      = $_POST['status'];
                $category    = $_POST['category'];
                $member      = $_POST['member'];
                $tags        = $_POST['tags'];


                // Validate The Form.
                $formErrors = array();

                if(empty($name))
                {
                    $formErrors [] = 'Name cant not be <strong>Empty</strong>';
                }

                if(empty($describe))
                {
                    $formErrors [] = 'Description cant not be <strong>Empty</strong>';
                }

                if(empty($price))
                {
                    $formErrors[] = 'Price cant not be <strong>Empty</strong>';
                }

                if(empty($country))
                {
                    $formErrors[] = 'Country cant not be <strong>Empty</strong>';
                }

                if(($status == 0))
                {
                    $formErrors[] = 'You must choose the <strong>Status</strong>';
                }

                if($category == 0)
                {
                    $formErrors[] = 'You must choose the <strong>Category</strong>';
                }

                if($member == 0)
                {
                    $formErrors[] = 'You must choose the <strong>Member</strong>';
                }

                // Loop into errors arrays and echo it.
                foreach($formErrors as $error)
                {
                    echo '<div class="alert alert-danger">' . $error . '</div>';
                }

                // Check if there's no error proceed the update operation.
                if(empty($formErrors))
                {
                    // Update Database with this info.
                    $stmt = $connect->prepare("UPDATE
                                                            items
                                                        SET
                                                            Name = ?,
                                                            Description = ?,
                                                            Price = ?,
                                                            Country_Made = ?,
                                                            Status = ?,
                                                            Cat_ID = ?,
                                                            Member_ID = ?,
                                                            tags = ? 
                                                        WHERE
                                                            Item_ID = ? ");
                    $stmt->execute(array($name, $describe, $price, $country, $status, $category, $member, $tags, $id));
                    $theMsg =  "<div class='alert alert-success'>" . $stmt->rowCount(). ' Record Updated</div> ';
                    redirectHome($theMsg, 'back');
                }

            }
            else
            {
                $theMsg = '<div class="alert alert-danger"> Sorry you cant browse this page directly</div>';
                redirectHome($theMsg);
            }
            echo "</div>";
        }


        elseif ($do == 'Delete')
        {

            echo "<h1 class='text-center'>Delete Items</h1>";
            echo "<div class='container'> ";

            // Check if GET REQUEST userId is numeric $ GET the integer value of it !
            $deleteItem = isset($_GET['itemId']) && is_numeric($_GET['itemId']) ? intval($_GET['itemId']) : 0;

            // Select all data depend on this ID.
            $check = checkItem('Item_ID','items',$deleteItem);

            if($check > 0)
            {
                $stmt = $connect->prepare("DELETE FROM items WHERE Item_ID = :Zitem");
                $stmt->bindParam(":Zitem", $deleteItem);
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


        elseif ($do == 'Approve')
        {

            echo "<h1 class='text-center'>Approve Item</h1>";
            echo "<div class='container'>";

            // Check if GET REQUEST userId is numeric & GET the integer value of it !
            $approveItem = isset($_GET['itemId']) && is_numeric($_GET['itemId']) ? intval($_GET['itemId']) : 0;

            // Select all data depend on this ID.
            $check = checkItem('Item_ID', 'items', $approveItem);

            if($check > 0)
            {
                $stmt = $connect->prepare("UPDATE items SET Approve = 1 WHERE Item_ID = ?");

                $stmt->execute(array($approveItem));

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

    ob_end_flush();
?>