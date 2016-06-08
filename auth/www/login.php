<?php

    require("common.php");
    
    $fatal_msg = "";
    $error_msg = "";
    
    $redirect = "";
        
    function do_login()
    {
        //Check if user submitted username and password
        if(zc_string_empty($_POST['password']) or zc_string_empty($_POST['username']))
            return "Please enter a username and password.";
            
        //Verify their credentials
        $login_success = false;
        $userid_new = 0;
        $token_id = 0;
        
        $db = zc_init_pdo();
        
        try
        {
            //Now perform the login logic
            $login_q = $db->prepare("select * from `users` where `name`=:username");
            $login_q->execute(array(':username' => $_POST['username']));
            
            $row = $login_q->fetch();
            
            //Verify password, if username was found
            if($row)
            {
                if(password_verify($_POST['password'], $row['password']))
                {
                    $login_success = true;
                    $userid_new = intval($row['id']);
                }
            }
                       
        }
        catch(PDOException $ex)
        {
            $GLOBALS['fatal_msg'] = "Database error: " . $ex->getMessage();
            return "";
        }
        
        if(!$login_success)
            return "Your username and/or password was incorrect.";
            
        //Create a token for the user
        $token_new = bin2hex(openssl_random_pseudo_bytes(16));
        $expiration = time() + (60*45); //45 minutes plus current time
            
        try
        {
            $token_q = $db->prepare("insert into `tokens` (token, user, expiration) values (:token,:user,:expiration)");
            $token_q->execute(array(':token' => $token_new, ':user' => $userid_new, ':expiration' => $expiration));
            
            //Get ID of token inserted
            $token_id = $db->lastInsertId();
        }
        catch(PDOException $ex)
        {
            $GLOBALS['fatal_msg'] = "Database error: " . $ex->getMessage();
            return "";
        }
                
        //Create an access code
        $ac_new = bin2hex(openssl_random_pseudo_bytes(16));
        $ac_expiration = time() + (60*3); // 3 minutes plus current time
        
        //Appsecrets are no longer used for this because of security things
        //I don't even know why I thought that was a good idea
        //So now we just set that value as 0 and go on our merry way
        
        try
        {
            $ac_q = $db->prepare("insert into `tokencode` (code, appsecret, token, expiration) values (:code, 0, :token, :expiration)");
            $ac_q->execute(array(':code' => $ac_new, ':token' => $token_id, ':expiration' => $ac_expiration));
        }
        catch(PDOException $ex)
        {
            $GLOBALS['fatal_msg'] = "Database error: " . $ex->getMessage();
            return "";
        }
        
        //Redirect the user to the destination
        
        //Check to see how we should append the access code to the query string
        if(strpos($_POST['redirect'], '?') === false)
        {
            //No query string is present
            header('location: ' . $_POST['redirect'] . '?accesscode=' . urlencode($ac_new));
            exit;
        }
        else
        {
            //A query string is present, append to the end of the query string
            header('location: ' . $_POST['redirect'] . '&accesscode=' . urlencode($ac_new));
            exit;
        }
        
        
        
        
        
        
        
        //We aren't supposed to get here
        //Fallthrough
        $fatal_msg = "Internal server error.";
        return ""; 
    }
    
    //Redirect must be submitted
    if(zc_string_empty($_REQUEST['redirect']))
    {
        $fatal_msg = "Malformed login request.";
    }
    
    $GLOBALS['redirect'] = $_REQUEST['redirect'];
    
    //If the user has submitted the form and there are no fatal errors
    //Run the login code
    if($GLOBALS['fatal_msg'] == "" and !empty($_POST))
        $GLOBALS['error_msg'] = do_login();
   
    

if($GLOBALS['fatal_msg'] != "")
{
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<title>Z3 Login</title>
<style>
body
{
    font-family: sans-serif;
    background-color: #777;
    background-image: url('noise-bg-777.png');
}

.button {
    
    background-color: #fff;
    color: black;
    padding: 2px 15px;
    text-align: center;
    font-size: large;
    cursor: pointer;
    border: 2px solid black;
    /*border-radius: 5px; 
    -moz-border-radius: 5px; 
    -webkit-border-radius: 5px;*/

}

.button:hover
{
    background-color: #000;
    color: #fff;
}

.loginbox {
    
    background-color: #fff;
    color: black;
   
    
    text-align: center;
   
    
    border: 2px solid #fff;
    border-radius: 3px;
    -moz-border-radius: 3px; 
    -webkit-border-radius: 3px;
    
    transition: 1s;
    left: 0;
    
    
    
    width: 500px;
    height: 250px;
    margin: 0 auto;
    margin-top: 4%;
    
    
    
    box-shadow: 10px 10px 5px #333;
    -moz-box-shadow: 10px 10px 5px #333;
    -webkit-box-shadow: 10px 10px 5px #333;

}

.textinput
{
    border: 2px solid black;
    /*border-radius: 5px; 
    -moz-border-radius: 5px; 
    -webkit-border-radius: 5px;*/
    background-color: #fff;
}

h1,h2,h3,h4,h5,h6
{
    
    letter-spacing: -1px;
}

</style>


</head>
<body>
<div class="loginbox">
<h2>Z3 Login</h2>
<p style="font-weight: bold; color: red"><?php echo $GLOBALS['fatal_msg']; ?></p>
</div>

</body>
</html>
<?php
}
else
{
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<title>Z3 Login</title>
<style>
body
{
    font-family: sans-serif;
    background-color: #777;
    background-image: url('noise-bg-777.png');
}

.button {
    
    background-color: #fff;
    color: black;
    padding: 2px 15px;
    text-align: center;
    font-size: large;
    cursor: pointer;
    border: 2px solid black;
    /*border-radius: 5px; 
    -moz-border-radius: 5px; 
    -webkit-border-radius: 5px;*/

}

.button:hover
{
    background-color: #000;
    color: #fff;
}

.loginbox {
    
    background-color: #fff;
    color: black;
   
    
    text-align: center;
   
    
    border: 2px solid #fff;
    border-radius: 3px;
    -moz-border-radius: 3px; 
    -webkit-border-radius: 3px;
    
    transition: 1s;
    left: 0;
    
    
    
    width: 500px;
    height: 250px;
    margin: 0 auto;
    margin-top: 4%;
    
    
    
    box-shadow: 10px 10px 5px #333;
    -moz-box-shadow: 10px 10px 5px #333;
    -webkit-box-shadow: 10px 10px 5px #333;

}

.textinput
{
    border: 2px solid black;
    /*border-radius: 5px; 
    -moz-border-radius: 5px; 
    -webkit-border-radius: 5px;*/
    background-color: #fff;
}

h1,h2,h3,h4,h5,h6
{
    
    letter-spacing: -1px;
}

</style>


</head>
<body>
<div class="loginbox">
<h2>Z3 Account Login</h2>
<form action="login.php" method="POST">
<input type="hidden" name="redirect" value="<?php echo $GLOBALS['redirect']; ?>"/>
<span style="font-weight: bold; color: red"><?php echo $GLOBALS['error_msg']; ?></span>
<table border="0" style="margin: auto">
<tr>
<td><label for="username">Username:</label></td><td><input id="username" name="username" type="text" class="textinput"/></td>
</tr>
<tr>
<td><label for="password">Password:</label></td><td><input id="password" name="password" type="password" class="textinput"/></td>
</tr>
<tr>
<td>&nbsp;</td><td><input type="submit" class="button" value="Login" style="float: right"/></td>
</tr>
</table>

<p>Don't have a Z3 account? Register for one <a href="register.php" target="_blank">here</a>.</p>
</div>

</body>
</html>
<?php
}
?>
