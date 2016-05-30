<?php

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
            
            header("location: home.php");
            die("Redirecting to: home.php");
        }
        else
        {
            print("");
            
            $submitted_username = htmlentities($_POST['username'], ENT_QUOTES, 'UTF-8');
        }
    

require 'Header.php';
?>

<div class="loginbox"/>
<h2>Zero³ Login</h2>
<form action="login.php" method="POST">
<label for="username">Username:</label><input id="username" name="username" type="text"/>
<br/>
<label for="password">Password:</label><input id="password" name="password" type="password"/>
<br/>
<input type="button" class="button" value="Login"/>
<p>Don't have a Zero³ account? <a href="register.php">Register</a>.</p>
</div>

</body>
</html>

