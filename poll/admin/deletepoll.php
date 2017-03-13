<?php
include "connect.php";
session_start();
if (isset($_SESSION['username']))
   {

   if (isset($_POST['submit']))
      {
        $check="Select * from P_question";
        $check2=mysql_query($check);
        while($check3=mysql_fetch_array($check2))
            {
               $check4=$check3[question];
            }
        if($check4)
            {
               $deletequestion="DELETE from P_question";
               mysql_query($deletequestion);
               $deleteip="DELETE from P_ip";
               mysql_query($deleteip);
               $deletechoices="DELETE from P_choices";
               mysql_query($deletechoices);
               print "<center><h3>Delete Poll</h3></center><br>"; 
               print "<center>";
               print "<table border='0' width='70%' cellspacing='20'>";
               print "<tr><td width='25%' valign='top'>";
               include 'left.php';
               print "</td>";
               print "<td valign='top' width='75%'>";
               print "The poll has been deleted, you can now create another poll";
           }

        else
           {
               print "<center><h3>Delete Poll</h3></center><br>"; 
               print "<center>";
               print "<table border='0' width='70%' cellspacing='20'>";
               print "<tr><td width='25%' valign='top'>";
               include 'left.php';
               print "</td>";
               print "<td valign='top' width='75%'>";
               print "There is no poll to delete";
          }
   

      }
    else
      {
        print "<center><h3>Delete Poll</h3></center><br>";
        print "<center>";
        print "<table border='0' width='70%' cellspacing='20'>";
        print "<tr><td width='25%' valign='top'>";
        include 'left.php';
        print "</td>";
        print "<td valign='top' width='75%'>";
        print "Warning! Pressing the button below will empty all data to the current poll<br>";
        print "<form action='deletepoll.php' method='post'>";
        print "<input type='submit' name='submit' value='Delete Poll'>";
        print "</form>";
        print "</td></tr></table>";    
        print "</center>";
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
    
   