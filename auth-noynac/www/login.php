<?php include 'Header.php';

    require("common.php");
    
    $submitted_username = '';
    
    if(!empty($_POST))
    {

        $query = "
            SELECT
                id,
                username,
                password,
                salt,
                email
            FROM users
            WHERE
                username = :username
        ";
        
        $query_params = array(
            ':username' => $_POST['username']
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
        
        $login_ok = false;
        

        $row = $stmt->fetch();
        if($row)
        {

            $check_password = hash('sha256', $_POST['password'] . $row['salt']);
            for($round = 0; $round < 65536; $round++)
            {
                $check_password = hash('sha256', $check_password . $row['salt']);
            }
            
            if($check_password === $row['password'])
            
                $login_ok = true;
            }
        }
        

        if($login_ok)
        {

            unset($row['salt']);
            unset($row['password']);
            
            $_SESSION['user'] = $row;
            
            header("Location: home.php");
            die("Redirecting to: home.php");
        }
        else
        {
            print("");
            
            $submitted_username = htmlentities($_POST['username'], ENT_QUOTES, 'UTF-8');
        }
    
    
?>


<form action="login.php" method="post">
<div id="contact"> 
<form action="login.php" method="post">
        <div class="input_label user"> 
            <label for="name"> 
<form action="login.php" method="post">
                Username</label></div> 
<form action="login.php" method="post">
        <input id="name" class="name" size="30" type="text" name="username" value="<?php echo $submitted_username; ?>" /> 
    </div> 
<br> 
<div id="contact"> 
        <div class="input_label user"> 
            <label for="name"> 
                Password</label></div> 
        <input type="text" id="name" class="name" size="30" type="password" name="password" value="" /> 
<br /><br />
    <input type="submit" value="Login" />   
 </div> 
 </form>
<a href="register.php">Register</a>
 
<style> 

</style> 
