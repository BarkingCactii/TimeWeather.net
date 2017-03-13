<?php
include "connect.php";
session_start();
if (isset($_SESSION['username']))
   {
      $doesexist="SELECT* from P_question";
      $doesexist2=mysql_query($doesexist);
      while($doesexist3=mysql_fetch_array($doesexist2))
         {
           $quest=$doesexist3[question];
         }
      if($quest)
         {
             if(isset($_POST['submit']))
               {
                  $pollchoice=$_POST['pollchoice'];
                  $addchoice="INSERT into P_choices(answer) VALUES('$pollchoice')";
                  mysql_query($addchoice);
                  print "<center><h3>Add a choice option</h3></center><br>"; 
                  print "<center>";
                  print "<table border='0' width='70%' cellspacing='20'>";
                  print "<tr><td width='25%' valign='top'>";
                  include 'left.php';
                  print "</td>";
                  print "<td valign='top' width='75%'>";
                  print "Choice added to poll<br><br>";
                  print "<form action='addchoice.php' method='post'>";
                  print "Type in poll answer: <input option='text' name='pollchoice' size='15'>";
                  print "<input type='submit' name='submit' value='Add Poll Option'></form>";
                  print "</td></tr></table>";
                  
                  
               }
 
             else
               {
                  print "<center><h3>Add a choice option</h3></center><br>"; 
                  print "<center>";
                  print "<table border='0' width='70%' cellspacing='20'>";
                  print "<tr><td width='25%' valign='top'>";
                  include 'left.php';
                  print "</td>";
                  print "<td valign='top' width='75%'>";
                  print "<form action='addchoice.php' method='post'>";
                  print "Type in poll answer: <input option='text' name='pollchoice' size='15'>";
                  print "<input type='submit' name='submit' value='Add Poll Option'></form>";
                  print "</td></tr></table>";
              }
         }
      else
         {
         
            print "<center><h3>Add a choice option</h3></center><br>"; 
            print "<center>";
            print "<table border='0' width='70%' cellspacing='20'>";
            print "<tr><td width='25%' valign='top'>";
            include 'left.php';
            print "</td>";
            print "<td valign='top' width='75%'>";
            print "There is no poll to add choices to, please create a poll first";
            print "</td></tr></table>";
         }

 
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
    
   