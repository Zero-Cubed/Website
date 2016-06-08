<?php
require 'common.php';

$message = "";


function doRegistration()
{
    $db = zc_init_pdo();
    
    //Check if the user has submitted registration data
    if(!empty($_POST))
    {
        //Yes
        
        //Perform preliminary validation of all user data
        if(zc_string_empty($_POST['username']))
        {
            return "<p class='error'>Please enter a username.</p>";
            
        }
        
        if(zc_string_empty($_POST['password']))
        {
            return "<p class='error'>Please enter a password.</p>";
            
        }
        
        if(strlen($_POST['password']) < 8)
        {
            
            return "<p class='error'>Your password must be at least 8 characters in length.</p>";
            
        }
        
        
        if(zc_string_empty($_POST['email']))
        {
            return "<p class='error'>Please enter a valid e-mail address.</p>";
            
        }
        
        if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
        {
            return "<p class='error'>Please enter a valid e-mail address.</p>";
            
        }
        
        //Ensure username is not already taken
        try
        {
            $username_q = $db->prepare("SELECT COUNT(*) FROM `users` WHERE `name`=:username");
            $username_q->execute(array(':username' => $_POST['username']));
            
            if($username_q->fetchColumn() > 0)
            {
                //Username already exists
                return "<p class='error'>The username you chose is already taken.</p>";
               
            }
            
            
        }
        catch(PDOException $ex)
        {
            die("Failed to connect to the database: " . $ex->getMessage());
        }
        
        //Now insert the data
        try
        {
            //Note: the active field is to indiciate whether the account has undergone activation
            //Since activation has not been implemented yet, this value is set to 1 by default
            //When activation is implemented, ensure this value is changed to 0
            
            $registration_q = $db->prepare("INSERT INTO `users` (name,password,email,active,games,friends,infriends,outfriends,bio) VALUES (:name,:password,:email,:active,:games,:friends,:infriends,:outfriends,:bio)");
            $registration_q->execute(array(':name' => $_POST['username'], ':password' => password_hash($_POST['password'], PASSWORD_DEFAULT), ':email' => $_POST['email'], ':active' => 1, ':games' => "", ':friends' => "", ':infriends' => "", ':outfriends' => "", ':bio' => ""));
        }
        catch(PDOException $ex)
        {
            die("Failed to connect to the database: " . $ex->getMessage());
        }
        
        //Registration successful.
        return "success";
            
           
        
        
        
        
        
    }
}

$message = doRegistration();


if($message == "success")
{
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<title>Z3 Account Registration</title>
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
<h2>Z3 Account</h2>
<p>You have successfully registered a Z3 account.</p>
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
<title>Z³ Account Registration</title>
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

.error
{
    font-weight: bold;
    color: red;
}

</style>


</head>
<body>
<div class="loginbox">
<h2>Register a Z3 Account</h2>
<p>A Z3 account lets you download and play Zero<sup>3</sup> games as well as network with other players.</p>
<?php echo $message; ?>
<form action="register.php" method="POST">
<table border="0" style="margin: auto;text-align: left">
<tr>
<td><label for="username">Username:</label></td><td><input id="username" name="username" type="text" class="textinput" required/></td>
</tr>
<tr>
<td style="font-size: small;text-align: left;" colspan="2">This can be changed later.</td>
</tr>
<tr>
<td><label for="password">Password:</label></td><td><input id="password" name="password" type="password" class="textinput" required/></td>
</tr>
<tr>
<td style="font-size: small;text-align: left;" colspan="2">Must be at least 8 characters.</td>
</tr>
<tr>
<td><label for="email">E-mail:</label></td><td><input id="email" name="email" type="email" class="textinput" required/></td>
</tr>
<tr>
<td style="font-size: small;text-align: left;" colspan="2">Needed for activation.</td>
</tr>
<tr>
<td>&nbsp;</td><td><input type="submit" class="button" value="Register" style="float: right"/></td>
</tr>
</table>
<!--<p>Already have a Z<sup>3</sup> account? Login <a href="login.php">here</a>.</p>-->
</div>
</body>
</html>
<?php
}
?>
