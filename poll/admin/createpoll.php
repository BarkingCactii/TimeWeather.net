<?php
include "connect.php";
session_start();
if (isset($_SESSION['username']))
   {
    //Check to see if there is already a poll
    $checkpoll="SELECT * from P_question";
    $checkpoll2=mysql_query($checkpoll);
    while($checkpoll3=mysql_fetch_array($checkpoll2))
        {
          $isthere=$checkpoll3[question];
        }
    if($isthere)
        {
          print "<center><h3>Create Poll</h3></center><br>";
          print "<center>";
          print "<table border='0' width='70%' cellspacing='20'>";
          print "<tr><td width='25%' valign='top'>";
          include 'left.php';
          print "</td>";
          print "<td valign='top' width='75%'>";
          print "There is already an existing poll, you cannot create another poll";
          print "</td></tr></table>";
        }
    else
        {

          if (isset($_POST['submit']))
              { 
                $pollname=$_POST['pollname'];
                $createpoll="INSERT into P_question(question) values('$pollname')";
                mysql_query($createpoll);
                print "<center><h3>Create Poll</h3></center><br>";
                print "<center>";
                print "<table border='0' width='70%' cellspacing='20'>";
                print "<tr><td width='25%' valign='top'>";
                include 'left.php';
                print "</td>";
                print "<td valign='top' width='75%'>";
                print "Poll Created";
                print "</td></tr></table>";                
               
              }
          else
              {
                print "<center><h3>Create Poll</h3></center><br>";
                print "<center>";
                print "<table border='0' width='70%' cellspacing='20'>";
                print "<tr><td width='25%' valign='top'>";
                include 'left.php';
                print "</td>";
                print "<td valign='top' width='75%'>";
                print "<form action='createpoll.php' method='post'>";
                print "Poll Question: <input type='text' name='pollname' size='20'>";
                print "<input type='submit' name='submit' value='Create Poll'>";
                print "</td></tr></table>";
     

             }
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
    
   