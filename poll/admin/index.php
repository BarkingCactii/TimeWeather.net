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
     print "This your admin panel, from here you can create a poll, delete a poll, and add or delete poll choices.";
     print "Chipmunk poll does not support multiple polls, you can only have one poll going at a time.";
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
    
   