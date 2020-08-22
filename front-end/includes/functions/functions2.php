<?php

    /*
     * Get All Functions v2.0
     * Function to Get All Records From Any Database Table.
     * */

    function getAllFrom($field, $table, $where = NULL, $and = NULL, $orderingField, $ordering = "DESC")
    {
        global $connect;
        $getAll = $connect->prepare("SELECT $field FROM $table $where $and ORDER BY $orderingField $ordering  ");
        $getAll->execute();
        $all = $getAll->fetchAll();
        return $all;
    }

    /*
     * Get Categories Function v1.0
     * Function to Get Categories From Database
     * */

    function getCategory()
    {
        global $connect;
        $getCate = $connect->prepare("SELECT * FROM categories ORDER BY ID ASC");
        $getCate->execute();
        $cates = $getCate->fetchAll();
        return $cates;
    }

    /*
     * Title function that echo the page title in case the page
     *  has the variable $pageTitle and echo Default title for other pages.
     * */

    function getTitle()
    {
        global $pageTitle;

        if(isset($pageTitle))
        {
            echo $pageTitle;
        }
        else
            {
                echo 'Default';
            }
    }

    /*
     * Home Redirect Function v2.0
     * Function This Function Accept Parameters.
     * $theMsg = Echo The Message [Error | Success | Warning]
     * $url = The Link You Want To Redirect
     * $seconds = Seconds Before Redirecting.
     * */

    function redirectHome($theMsg, $url = null, $seconds = 3)
    {
        if($url === null)
        {
            $url = 'index2.php';
        }
        else
            {
                $url = isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] !== '' ? $_SERVER['HTTP_REFERER'] : 'index2.php';
            }

        echo $theMsg;

        echo "<div class='alert alert-info'>You'll be redirecting to homepage after $seconds seconds.</div>";

        header("refresh:$seconds;url=$url");
        exit();
    }

    /*
     * Check Items Function v1.0
     * Function To Check Item In Database [Function Accept Parameter].
     * $select = The Item To SELECT [Example: user, items, categories].
     * $from = The Table To SELECT FROM [Example: users, items, categories].
     * $value = The Value Of SELECT [Example: Hossam, Box, Electronics].
     * */

    function checkItem($select, $from, $value)
    {
        global $connect;
        $statement = $connect->prepare("SELECT $select FROM $from WHERE $select = ?");
        $statement->execute(array($value));
        $count = $statement->rowCount();

        return $count;
    }

    /*
     * Count Number Of Items Function v1.0.
     * Function To Count Number Of Items Rows.
     * $item = The Item To Count.
     * $table = The Table To Choose From.
     * */

    function countItems($item, $table)
    {
        global $connect;
        $statement2 = $connect->prepare("SELECT COUNT($item) FROM $table");
        $statement2->execute();
        return $statement2->fetchColumn();
    }

    /*
     * Get latest Record Function v1.0
     * Function to Get Latest Items From Database [Users, Items, Comments]
     * $select = Field to select
     * $table = The table to choose from
     * $order = The DESC Ordering
     * $limit = Number of records to Get
     * */

    function getLatest($select, $table, $order, $limit = 3)
    {
        global $connect;
        $getStatement = $connect->prepare("SELECT $select FROM $table ORDER BY $order DESC LIMIT $limit");
        $getStatement->execute();
        $rows = $getStatement->fetchAll();
        return $rows;
    }


    /*
     * Check if user is not activated !
     * Function to check the RegisterStatus of the user !
     * */
    function checkUserStatus($user)
    {
        global $connect;
        $stmtStatus =$connect->prepare("SELECT
                                                        UserName, RegisterStatus
                                                    FROM
                                                        users
                                                    WHERE
                                                        UserName = ?
                                                    AND
                                                        RegisterStatus = 0");

        $stmtStatus->execute(array($user));
        $count = $stmtStatus->rowCount();
        return $count;
    }