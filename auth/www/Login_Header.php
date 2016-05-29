<!DOCTYPE html>
<html>
<head>
<style>
ul {
    list-style-type: none;
    margin: 0;
    padding: 0;
    overflow: hidden;
    background-color: #333;
}

li {
    float: center;
    display: inline-block;

}

li a {
    display: block;
    color: white;
    text-align: center;
    padding: 16px 18px;
    text-decoration: none;
}

a:hover:not(.active) {
    background-color: #111;
}

.active {
background-color:#4CAF50;
}




.button {
    
    background-color: #000000; /* Green */
    border: none;
    color: white;
    padding: 35px 72px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 96px;
    margin: 4px 2px;
    cursor: pointer;
    position: absolute;
    right: 590px;
    top: 340px;
   border-radius: 5px; 
-moz-border-radius: 5px; 
-webkit-border-radius: 5px; 
border: 2px solid;
}

.button1 {font-size: 10px;}
.button2 {font-size: 12px;}
.button3 {font-size: 36px;}
.button4 {font-size: 20px;}
.button5 {font-size: 24px;}






</style>


</head>
<body bgcolor="">

<ul>
<center>
  <li><a href="home.php">Home</a></li>
  <li><a href="#news">News</a></li>
  <li><a href="#contact">Contact</a></li>
  <li><a href="#about">About</a></li>
<center/>
</ul>
<ul>
<center>
  <li><a href="memberlist.php">Members</a></li>
  <li><a href="edit_account.php">Account</a></li>
  <li><a href="logout.php">Logout</a></li>
<center/>
</ul>

</body>
</html>