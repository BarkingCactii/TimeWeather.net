<?php
include "connect.php";
session_start();
if (isset($_SESSION['username']))
   {
     print "<center><h3>Poll Admin Panel</h3></center><br>";
     print "<center>";
     print "<table border='0' width='70%' cellspacing='20'>";
     print "<tr><td width='25%' valign='top'>";
     include 'left.php';
     print "</td>";
     print "<td valign='top' width='75%'>";
     if(isset($_POST['submit']))
     {
       $ID=$_POST['ID'];
       $choice=$_POST['choice'];
       $upans="UPDATE P_choices set answer='$choice' where ID='$ID'";
       mysql_query($upans) or die("Could not update answer");
       print "Answer Updated";

     }
     else if(isset($_GET['ID']))
     {     
       print "Edit choice:<br>";
       $ID=$_GET['ID'];
       $getchoice="SELECT * from P_choices where ID='$ID'";
       $getchoice2=mysql_query($getchoice) or die("Could not get choice");
       $getchoice3=mysql_fetch_array($getchoice2);
       print "<form action='editchoice.php' method='post'>";
       print "<input type='hidden' name='ID' value='$ID'>";
       print "<input type='text' name='choice' value='$getchoice3[answer]'><br>";
       print "<input type='submit' name='submit' value='Edit Poll Choice'></form>";  

     }
     else
     {
       print "<table border='1'><tr><td>Answer</td><td>Edit</td></tr>";
       $getans="SELECT * from P_choices";
       $getans2=mysql_query($getans) or die("Could not get answer");
       while($getans3=mysql_fetch_array($getans2))
       {
         print "<tr><td>$getans3[answer]</td><td><A href='editchoice.php?ID=$getans3[ID]'>Edit</a></td></tr>";
       }
       print "</table>";


     }
     print "</td></tr></table>";    
     print "</center>";
   }
else
   {
     print "You are not logged in as Administrator, please log in.";
     print "<form method='POST' action='authenticate.php'>";
     print "Type Username Here: <input type='text' name='username' size='15'><br>";
     print "Type Password Here: <input type='password' name='password' size='15'><br>";
     print "<input type='submit' value='submit' name='submit'>";
     print "</form>";
   }
?>
    
   