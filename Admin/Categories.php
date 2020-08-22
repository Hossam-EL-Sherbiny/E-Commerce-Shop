<?php

    /* Category Page*/

    ob_start();
    session_start();

    $pageTitle = 'Categories';
    if(isset($_SESSION['UserSession']))
    {
        include 'initialize.php';

        $do = isset($_GET['do']) ? $_GET['do'] : 'Manage';

        if($do == 'Manage')
        {
            $sort = 'ASC';
            $sort_array = array('ASC', 'DESC');

            if(isset($_GET['sort']) && in_array($_GET['sort'], $sort_array))
            {
                $sort = $_GET['sort'];
            }

            $statement = $connect->prepare("SELECT * FROM categories WHERE parent = 0 ORDER BY Ordering $sort");
            $statement->execute();
            $categories = $statement->fetchAll();

            if(!empty($categories))
            {
            ?>

            <h1 class="text-center">Manage Categories</h1>
            <div class="container categories">
                <div class="panel panel-default">

                    <div class="panel panel-heading">
                        <i class="fa fa-edit"></i> Manage Categories
                        <div class="option pull-right">
                           <i class="fa fa-sort"></i> Ordering:[
                            <a class="<?php if($sort == 'ASC') {echo 'active';} ?>" href="?sort=ASC">Asc</a> |
                            <a class="<?php if($sort == 'DESC') {echo 'active';} ?>" href="?sort=DESC">Desc</a>]
                           <i class="fa fa-eye"></i> View:[
                            <span class="active" data-view="full" >Full</span> |
                            <span>Classic</span>]
                        </div>
                    </div>

                    <div class="panel-body">
                        <?php
                            foreach ($categories as $category)
                            {
                                echo "<div class='cate'>";
                                    echo "<div class='hidden-buttons'>";
                                        echo "<a href='Categories.php?do=Edit&categoryId=" . $category['ID'] . "' class='btn btn-xs btn-primary'><i class='fa fa-edit'></i>Edit</a> ";
                                        echo "<a href='Categories.php?do=Delete&categoryId=" . $category['ID'] . "' class='confirm btn btn-xs btn-danger'><i class='fa fa-close'></i>Delete</a>";
                                    echo "</div>";
                                    echo "<h3>" . $category['Name'] . '</h3>';
                                    echo "<div class='full-view'>";
                                        echo "<p>"; if($category['Description'] == '') {echo 'This category has no description';} else{echo $category['Description'];}  echo '</p>';
                                        if ($category['Visibility'] == 1) { echo '<span class="visibility category-span"> <i class="fa fa-eye"></i> Hidden</span>';}
                                        if ($category['Allow_Comment'] == 1) { echo '<span class="comment category-span"> <i class="fa fa-close"></i> Comment disabled</span>';}
                                        if ($category['Allow_Ads'] == 1) { echo '<span class="advertises category-span"> <i class="fa fa-close"></i> Ads disabled</span>';}
                                    echo "</div>";

                                    // Get child categories
                                    $childCates = getAllFrom("*", "categories", "where parent = {$category['ID']}", "", "ID", "ASC");
                                    if (!empty($childCates))
                                    {
                                        echo "<h4 class='child-head'>Child Category</h4>";
                                        echo '<ul class="list-unstyled child-cats">';
                                        foreach ($childCates as $child)
                                        {
                                            echo "<li class='child-link'>
                                                    <a href='Categories.php?do=Edit&categoryId=" . $child['ID'] . "' >". $child['Name'] . "</a>
                                                    <a href='Categories.php?do=Delete&categoryId=" . $child['ID'] . "' class='show-delete confirm'>Delete</a>
                                                  </li>";
                                        }
                                        echo '</ul>';
                                    }

                                echo "</div>";
                                echo "<hr>";
                            }
                        ?>
                    </div>
                </div>
                <a class="add-category btn btn-primary" href="Categories.php?do=Add"><i class="fa fa-plus"></i>Add New Category</a>
            </div>

            <?php
            }
            else
                {
                    echo '<div class="container">';
                    echo '<div class="alert alert-info">There\'s no Categories to show</div>';
                    echo '<a href="Categories.php?do=Add" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> New Category</a>';
                    echo '</div>';
                }
        }
        elseif ($do == 'Add')
        { ?>
            <h1 class="text-center">Add New Category</h1>

            <div class="container">
                <form class="form-horizontal" action="?do=Insert" method="POST">

                    <!-- Start Name Field-->
                        <div class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Name</label>
                            <div class="col-sm-10 col-md-6">
                                <input type="text" name="name" class="form-control" autocomplete="off"
                                       required="required" placeholder="Name Of The Category" />
                            </div>
                        </div>
                    <!-- End Name Field-->

                    <!-- Start Description Field-->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Description</label>
                        <div class="col-sm-10 col-md-6">
                            <input type="text" name="description" class="form-control" placeholder="Describe The Category" />
                        </div>
                    </div>
                    <!-- End Description Field-->

                    <!-- Start Ordering Field-->
                        <div class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Ordering</label>
                            <div class="col-sm-10 col-md-6">
                                <input type="text" name="ordering" class="form-control" placeholder="Number To Arrange The Categories" />
                            </div>
                        </div>
                    <!-- End Ordering Field-->


                    <!-- Start Category Type-->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Parent ?</label>
                        <div class="col-sm-10 col-md-6">
                            <select name="parent">
                                <option value="0">None</option>
                                <?php
                                    $allCats = getAllFrom("*", "categories", "WHERE parent = 0", "", "ID","ASC");

                                    foreach ($allCats as $cat)
                                    {
                                        echo "<option value='". $cat['ID'] ."'>". $cat['Name'] ."</option>";
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <!-- End Category Type-->


                    <!-- Start Visibility Field-->
                        <div class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Visible</label>
                            <div class="col-sm-10 col-md-6">
                                <div>
                                    <input id="vis-yes" type="radio" name="visibility" value="0" checked />
                                    <label for="vis-yes">Yes</label>
                                </div>

                                <div>
                                    <input id="vis-no" type="radio" name="visibility" value="1" />
                                    <label for="vis-no">No</label>
                                </div>
                            </div>
                        </div>
                    <!-- End Visibility Field-->

                    <!-- Start Commenting Field-->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Allow Commenting</label>
                        <div class="col-sm-10 col-md-6">
                            <div>
                                <input id="comment-yes" type="radio" name="commenting" value="0" checked />
                                <label for="comment-yes">Yes</label>
                            </div>

                            <div>
                                <input id="comment-no" type="radio" name="commenting" value="1" />
                                <label for="comment-no">No</label>
                            </div>
                        </div>
                    </div>
                    <!-- End Visibility Field-->

                    <!-- Start Ads Field-->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Allow Ads</label>
                        <div class="col-sm-10 col-md-6">
                            <div>
                                <input id="ads-yes" type="radio" name="ads" value="0" checked />
                                <label for="ads-yes">Yes</label>
                            </div>

                            <div>
                                <input id="ads-no" type="radio" name="ads" value="1" />
                                <label for="ads-no">No</label>
                            </div>
                        </div>
                    </div>
                    <!-- End Ads Field-->

                    <!-- Start Submit Field-->
                        <div class="form-group form-group-lg">
                            <div class="col-sm-offset-2 col-sm-10">
                                <input type="submit" value="Add Category" class="btn btn-primary btn-lg" />
                            </div>
                        </div>
                    <!-- End Submit Field-->

                </form>
            </div>

            <?php

        }
        elseif($do == 'Insert')
        {
            if($_SERVER['REQUEST_METHOD'] == 'POST')
            {
                echo "<h1 class='text-center'>Insert Category</h1>";
                echo "<div class='container'>";

                // Get Variables From The Form
                $name     = $_POST['name'];
                $desc     = $_POST['description'];
                $parent   = $_POST['parent'];
                $order    = $_POST['ordering'];
                $visible  = $_POST['visibility'];
                $comment  = $_POST['commenting'];
                $ads      = $_POST['ads'];

                    // Check if the category exist in Database !
                    $check = checkItem("Name", "categories", $name);
                    if($check == 1)
                    {
                        $theMsg = '<div class="alert alert-danger"> Sorry this Category is exist</div>';
                        redirectHome($theMsg,'back');
                    }
                    else
                        {
                        // Insert Category To Database.
                        $stmt = $connect->prepare(
                                "INSERT INTO
                                            categories(Name,
                                             Description,
                                             parent,
                                              Ordering,
                                               Visibility,
                                                Allow_Comment,
                                                 Allow_Ads)
                                          VALUES (:Xname, :Xdesc, :Xparent, :Xorder, :Xvisible, :Xcomment, :Xads)");
                        $stmt->execute(array(
                                'Xname'     => $name,
                                'Xdesc'     => $desc,
                                'Xparent'   => $parent,
                                'Xorder'    => $order,
                                'Xvisible'  => $visible,
                                'Xcomment'  => $comment,
                                'Xads'      => $ads
                        ));

                        // Echo Success Message
                        $theMsg =  "<div class='alert alert-success'>" . $stmt->rowCount(). 'Record Inserted</div>';
                        redirectHome($theMsg,'back');

                        }
            }
            else
                {
                    echo "<div class='container'>";
                    $theMsg = '<div class="alert alert-danger">Sorry you can not browse this page directly</div>';
                    redirectHome($theMsg, 'back');
                    echo "</div>";
                }
            echo "</div>";

        }
        elseif ($do == 'Edit')
        {
            // Check if GET REQUEST CategoryId is numeric & Get the integer value of it .
            $categoryId = isset($_GET['categoryId']) && is_numeric($_GET['categoryId']) ? intval($_GET['categoryId']) : 0;

            $stmt = $connect->prepare("SELECT * FROM categories WHERE ID = ?");  // Select all data depend on this id .

            $stmt->execute(array($categoryId));     // Execute Query.
            $catRow = $stmt->fetch();      // Fetch The Data.
            $count = $stmt->rowCount(); // The Row Count

            // If there is such id show the form
            if($count > 0)
            { ?>
                <h1 class="text-center">Edit Category</h1>

                <div class="container">
                    <form class="form-horizontal" action="?do=Update" method="POST">
                        <input type="hidden" name="categoryId" value="<?php echo $categoryId ?>" />

                        <!-- Start Name Field-->
                        <div class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Name</label>
                            <div class="col-sm-10 col-md-6">
                                <input type="text" name="name" class="form-control"
                                       required="required" placeholder="Name Of The Category" value="<?php echo $catRow['Name'];?>" />
                            </div>
                        </div>
                        <!-- End Name Field-->

                        <!-- Start Description Field-->
                        <div class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Description</label>
                            <div class="col-sm-10 col-md-6">
                                <input type="text" name="description" class="form-control" placeholder="Describe The Category" value="<?php echo $catRow['Description'];?>" />
                            </div>
                        </div>
                        <!-- End Description Field-->

                        <!-- Start Ordering Field-->
                        <div class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Ordering</label>
                            <div class="col-sm-10 col-md-6">
                                <input type="text" name="ordering" class="form-control" placeholder="Number To Arrange The Categories" value="<?php echo $catRow['Ordering'];?>" />
                            </div>
                        </div>
                        <!-- End Ordering Field-->

                        <!-- Start Category Type-->
                        <div class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Parent ?</label>
                            <div class="col-sm-10 col-md-6">
                                <select name="parent">
                                    <option value="0">None</option>
                                    <?php
                                    $allCats = getAllFrom("*", "categories", "WHERE parent = 0", "", "ID","ASC");

                                    foreach ($allCats as $cat)
                                    {
                                        echo "<option value='". $cat['ID'] ."'";
                                            if($catRow['parent'] == $cat['ID']) {echo 'Selected';}
                                        echo ">". $cat['Name'] ."</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <!-- End Category Type-->

                        <!-- Start Visibility Field-->
                        <div class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Visible</label>
                            <div class="col-sm-10 col-md-6">
                                <div>
                                    <input id="vis-yes" type="radio" name="visibility" value="0" <?php if($catRow['Visibility'] == 0) {echo 'checked';} ?> />
                                    <label for="vis-yes">Yes</label>
                                </div>

                                <div>
                                    <input id="vis-no" type="radio" name="visibility" value="1" <?php if($catRow['Visibility'] == 1) {echo 'checked';} ?> />
                                    <label for="vis-no">No</label>
                                </div>
                            </div>
                        </div>
                        <!-- End Visibility Field-->

                        <!-- Start Commenting Field-->
                        <div class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Allow Commenting</label>
                            <div class="col-sm-10 col-md-6">
                                <div>
                                    <input id="comment-yes" type="radio" name="commenting" value="0" <?php if($catRow['Allow_Comment'] == 0) {echo 'checked';} ?> />
                                    <label for="comment-yes">Yes</label>
                                </div>

                                <div>
                                    <input id="comment-no" type="radio" name="commenting" value="1" <?php if($catRow['Allow_Comment'] == 1) {echo 'checked';} ?> />
                                    <label for="comment-no">No</label>
                                </div>
                            </div>
                        </div>
                        <!-- End Visibility Field-->

                        <!-- Start Ads Field-->
                        <div class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Allow Ads</label>
                            <div class="col-sm-10 col-md-6">
                                <div>
                                    <input id="ads-yes" type="radio" name="ads" value="0" <?php if($catRow['Allow_Ads'] == 0) {echo 'checked';} ?> />
                                    <label for="ads-yes">Yes</label>
                                </div>

                                <div>
                                    <input id="ads-no" type="radio" name="ads" value="1" <?php if($catRow['Allow_Ads'] == 1) {echo 'checked';} ?> />
                                    <label for="ads-no">No</label>
                                </div>
                            </div>
                        </div>
                        <!-- End Ads Field-->

                        <!-- Start Submit Field-->
                        <div class="form-group form-group-lg">
                            <div class="col-sm-offset-2 col-sm-10">
                                <input type="submit" value="Save" class="btn btn-primary btn-lg" />
                            </div>
                        </div>
                        <!-- End Submit Field-->

                    </form>
                </div>
            <?php
            }

            // if there's no such id show error message
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
            echo "<h1 class='text-center'>Update Category</h1>";
            echo "<div class='container'> ";

            if($_SERVER['REQUEST_METHOD'] == 'POST')
            {
                //Get variables form the form
                $catId    = $_POST['categoryId'];
                $name     = $_POST['name'];
                $describe = $_POST['description'];
                $ordering = $_POST['ordering'];
                $parent   = $_POST['parent'];
                $vis      = $_POST['visibility'];
                $com      = $_POST['commenting'];
                $advert   = $_POST['ads'];

                // Update Database with this info.
                $stUpdate = $connect->prepare("UPDATE
                                                    categories SET Name = ?,
                                                     Description = ?,
                                                      Ordering = ?,
                                                       parent = ?,
                                                       Visibility = ?,
                                                        Allow_Comment = ?,
                                                         Allow_Ads =?
                                                        WHERE ID = ? ");
                $stUpdate->execute(array($name, $describe, $ordering, $parent, $vis, $com, $advert, $catId));
                $theMsg =  "<div class='alert alert-success'>" . $stUpdate->rowCount(). 'Record Updated</div>';
                redirectHome($theMsg, 'back');

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
            echo "<h1 class='text-center'>Delete Category</h1>";
            echo "<div class='container'>";

            // Check if GET REQUEST userId is numeric $ GET the integer value of it !
            $categoryId = isset($_GET['categoryId']) && is_numeric($_GET['categoryId']) ? intval($_GET['categoryId']) : 0;

            // Select all data depend on this ID.
                $check = checkItem('ID','categories',$categoryId);

                if($check > 0)
                {
                    $stmt = $connect->prepare("DELETE FROM categories WHERE ID = :Zid");
                    $stmt->bindParam("Zid", $categoryId);
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

        include $tempDirectory . 'footer.php';
    }
    else
        {
            header('Location: index2.php');
            exit();
        }

        ob_end_flush();
?>


