<?php
include "connect.php";
$totalvotes=0;
$ans="SELECT * from P_choices";
$ans2=mysql_query($ans);
while($ans3=mysql_fetch_array($ans2))
  {
   $total=$total+$ans3[votes];
  }
//now display results
$ans4="SELECT * from P_choices";
$ans5=mysql_query($ans4);
while($ans6=mysql_fetch_array($ans5))
  {
    if($total>0)
      {
       $imagewidth=(100*$ans6[votes])/$total;
     
 
       print "<img src='pollpic.gif' height='5' width='$imagewidth' border='0'>$ans6[answer]($imagewidth %)<br>";
      } 
   else
      {
       print "No votes have been cast yet";
      }
  }
?>

<font size="2">Powered by © <A href="http://www.chipmunk-scripts.com">Chipmunk Poll</a></center>