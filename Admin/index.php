<?php

    session_start();
    session_unset();

    $noNavBar = '';
    $pageTitle = 'Login';

    if(isset($_SESSION['UserSession']))
    {
        header('Location: dashboard.php');  // Redirect to dashboard page.
    }

    include 'initialize.php';

    // Check if user coming from HTTP POST REQUEST !
    if($_SERVER['REQUEST_METHOD'] == 'POST')
    {
        $username   = $_POST['user'];
        $password   = $_POST['pass'];
        $hashedPass = sha1($password);

        $stmt = $connect->prepare("SELECT
                                                UserID, Username, Password
                                            FROM 
                                                users 
                                            WHERE 
                                                Username = ? 
                                            AND 
                                                Password = ? 
                                            AND 
                                                GroupID = 1
                                            LIMIT 1");

        $stmt->execute(array($username, $hashedPass));
        $row = $stmt->fetch();
        $count = $stmt->rowCount();

        // If count > 0 This mean the Database contain record about this Username .
        if($count > 0)
        {
            $_SESSION['UserSession'] = $username;   // Register session name.
            $_SESSION['ID'] = $row['UserID'];   // Register Session ID.
            header('Location: dashboard.php');  // Redirect to dashboard page.
            exit();
        }
    }
?>

    <form class="login" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
        <h4 class="text-center">Admin Login</h4>
        <input class="form-control" type="text" name="user" placeholder="Username" autocomplete="off" />
        <input class="form-control" type="password" name="pass" placeholder="Password" autocomplete="new-password" />
        <input class="btn btn-primary btn-block" type="submit" value="Login" />
    </form>

<?php include $tempDirectory .'footer.php'; ?>
