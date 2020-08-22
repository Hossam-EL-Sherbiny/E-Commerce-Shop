<?php include 'initialize2.php';?>

   <div class="container">

      <h1 class="text-center">Show Category Items</h1>
       <div class="row">
           <?php
               if(isset($_GET['pageId']) && is_numeric($_GET['pageId']))
               {
                   $category = intval($_GET['pageId']);

                   $allItems = getAllFrom("*", "items", "where Cat_ID = {$category}", "AND Approve = 1", "Item_ID" );
                    foreach ($allItems as $item)
                    {
                        echo '<div class="col-sm-6 col-md-3">';
                            echo '<div class="card item-box">';
                                echo '<span class="price-tag">' . $item['Price'] . '</span>';
                                echo '<img class="img-responsive" src="Hacker.JPG" alt="" />';
                                echo '<div class="caption">';
                                    echo '<h3><a href="items2.php?itemId='.$item['Item_ID'].'">'. $item['Name'] .'</a></h3>';
                                    echo '<p>' . $item['Description'] .'</p>';
                                    echo '<div class="date">' . $item['Add_Date'] .'</div>';
                                echo '</div>';
                            echo '</div>';
                        echo '</div>';
                    }
               }else
                   {
                       echo 'You must add page ID';
                   }
           ?>
       </div>

   </div>

<?php include $tempDirectory . 'footer2.php'; ?>
