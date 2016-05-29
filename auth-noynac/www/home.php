<?php include 'Login_Header.php';
?>


<?php


    require("common.php");
    

    if(empty($_SESSION['user']))
    {

        header("Location: login.php");
        

        die("Redirecting to login.php");
    }
?>
Welcome, <?php echo htmlentities($_SESSION['user']['username'], ENT_QUOTES, 'UTF-8'); ?><br />