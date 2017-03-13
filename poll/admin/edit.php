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
       $question=$_POST['question'];
       $changequestion="UPDATE P_question set question='$question'";
       mysql_query($changequestion) or die("Could not edit question");
       print "Question Edited.";

     }
     else
     {     
       $getpollquest="SELECT * from P_question";
       $getpollquest2=mysql_query($getpollquest) or die("Could not get poll question");
       $getpollquest3=mysql_fetch_array($getpollquest2);
       print "<form action='edit.php' method='post'>";
       print "Poll Question:<br>";
       print "<input type='text' name='question' size='40' value='$getpollquest3[question]'><br>";
       print "<input type='submit' name='submit' value='edit poll'></form>";

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
    
   