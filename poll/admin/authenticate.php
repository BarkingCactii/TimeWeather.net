<?php

include "connect.php";

if (isset($_POST['submit'])) // name of submit button
{
    $username=$_POST['username'];
    $password=$_POST['password'];
    $password=md5($password);
    $query = "select * from P_admin where username='$username' and password='$password'"; 
    $result = mysql_query($query) or die(mysql_error()) ;
    $result2= mysql_fetch_array($result);
    if($result2)
    {
       session_start();
       $_SESSION['username']=$username;
       print "Logged in succesfully, please go to <A href='index.php'>Admin Panel</a>";
    }
    else
    {
        print "Wrong username or password";
    }
  

}

?>