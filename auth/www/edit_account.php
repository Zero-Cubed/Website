<?php include 'Login_Header.php';
?>



<?php

    require("common.php");
   
    if(empty($_SESSION['user']))
    {
        header("Location: login.php");
        
        die("Redirecting to login.php");
    }
    
    if(!empty($_POST))
    {

        if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
        {
            die("Invalid E-Mail Address");
        }
        

        if($_POST['email'] != $_SESSION['user']['email'])
        {

            $query = "
                SELECT
                    1
                FROM users
                WHERE
                    email = :email
            ";
            

            $query_params = array(
                ':email' => $_POST['email']
            );
            
            try
            {

                $stmt = $db->prepare($query);
                $result = $stmt->execute($query_params);
            }
            catch(PDOException $ex)
            {

                die("Failed to run query: " . $ex->getMessage());
            }
            

            $row = $stmt->fetch();
            if($row)
            {
                die("This E-Mail address is already in use");
            }
        }
        
        if(!empty($_POST['password']))
        {
            $salt = dechex(mt_rand(0, 2147483647)) . dechex(mt_rand(0, 2147483647));
            $password = hash('sha256', $_POST['password'] . $salt);
            for($round = 0; $round < 65536; $round++)
            {
                $password = hash('sha256', $password . $salt);
            }
        }
        else
        {
            $password = null;
            $salt = null;
        }
        
        $query_params = array(
            ':email' => $_POST['email'],
            ':user_id' => $_SESSION['user']['id'],
        );
        
        if($password !== null)
        {
            $query_params[':password'] = $password;
            $query_params[':salt'] = $salt;
        }
        
        $query = "
            UPDATE users
            SET
                email = :email
        ";
        
        if($password !== null)
        {
            $query .= "
                , password = :password
                , salt = :salt
            ";
        }
        
        $query .= "
            WHERE
                id = :user_id
        ";
        
        try
        {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex)
        {
            die("Failed to run query: " . $ex->getMessage());
        }
        
        $_SESSION['user']['email'] = $_POST['email'];
        

        header("Location: home.php");
        
        die("Redirecting to private.php");
    }
    
?>
<h1>Edit Account</h1>
<form action="edit_account.php" method="post">
    Username:<br />
    <b><?php echo htmlentities($_SESSION['user']['username'], ENT_QUOTES, 'UTF-8'); ?></b>
    <br /><br />
    E-Mail Address:<br />
    <input type="text" name="email" value="<?php echo htmlentities($_SESSION['user']['email'], ENT_QUOTES, 'UTF-8'); ?>" />
    <br /><br />
    Password:<br />
    <input type="password" name="password" value="" /><br />
    <i>(leave blank if you do not want to change your password)</i>
    <br /><br />
    <input type="submit" value="Update Account" />
</form>