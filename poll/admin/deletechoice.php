<?php
include "connect.php";
session_start();
if (isset($_SESSION['username']))
   {

      if(isset($_POST['submit']))
      {
         $choice=$_POST['choice'];
         $delchoice="DELETE from P_choices where answer='$choice'";
         mysql_query($delchoice);
         print "<center><h3>Delete Poll Choice</h3></center><br>"; 
         print "<center>";
         print "<table border='0' width='70%' cellspacing='20'>";
         print "<tr><td width='25%' valign='top'>";
         include 'left.php';
         print "</td>";
         print "<td valign='top' width='75%'>";
         print "Poll Choice Deleted<br>";
         print "<form action='deletechoice.php' method='post'>";
                 $result = mysql_query("SELECT answer FROM P_choices");
        $row_array=mysql_fetch_row($result);

        $string='<select name=choice><option value="">Select option to delete</option>';

        for ($i=0; $i < mysql_num_rows($result); $i++) {

        if ($row_array[0] == "") {
          $row_array=mysql_fetch_row($result);
      } else {
          $string .='<option value="'.$row_array[0].'">'.$row_array[0];
          $row_array=mysql_fetch_row($result);
      }

      }

   $string .='</SELECT>';

   echo $string;
   print "<br><input type='submit' name='submit' value='submit'>";
   print "</form></td></tr></table>";
      }
         
          
      else
      {
        print "<center><h3>Delete Poll Choice</h3></center><br>"; 
        print "<center>";
        print "<table border='0' width='70%' cellspacing='20'>";
        print "<tr><td width='25%' valign='top'>";
        include 'left.php';
        print "</td>";
        print "<td valign='top' width='75%'>";
        print "<form action='deletechoice.php' method='post'>";
        $result = mysql_query("SELECT answer FROM P_choices");
        $row_array=mysql_fetch_row($result);

        $string='<select name=choice><option value="">Select option to delete</option>';

        for ($i=0; $i < mysql_num_rows($result); $i++) {

        if ($row_array[0] == "") {
          $row_array=mysql_fetch_row($result);
      } else {
          $string .='<option value="'.$row_array[0].'">'.$row_array[0];
          $row_array=mysql_fetch_row($result);
      }

      }

   $string .='</SELECT>';

   echo $string;
   print "<br><input type='submit' name='submit' value='submit'>";
   print "</form></td></tr></table>";
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
    
   