

<?php
include "connect.php";
$s=$_SERVER["REMOTE_ADDR"];
$ipcheck="SELECT * FROM P_ip where IP='$s'";
$ipcheck2=mysql_query($ipcheck);
while($ipcheck3=mysql_fetch_array($ipcheck2))
{
$ip=$ipcheck3[IP];
}
if($ip)
{

print "You have already voted in this poll<br>";
$pollquest="SELECT*From P_question";
  $p2=mysql_query($pollquest);
  while($p3 = mysql_fetch_array($p2))
    {
      print "<b>$p3[question]</b><br>";
    }
//Calculate total number of votes
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
       $imagewidth = number_format($imagewidth, 1, '.', '');

 
       print "<img src='poll/pollpic.gif' height='5' width='$imagewidth' border='0'>$ans6[answer]($imagewidth %)<br>";
      } 
   else
      {
       print "No votes have been cast yet";
      }
 }
print "Total votes: $total";
}



else
{
  $pollquest="SELECT*From P_question";
  $p2=mysql_query($pollquest);
  while($p3 = mysql_fetch_array($p2))
    {
      print "$p3[question] <br>";
    }
  $pollans="Select * from P_choices";
  $pollans2=mysql_query($pollans);
  print "<form action='poll/pollit.php' method='post'>";
  while($pollans3=mysql_fetch_array($pollans2))
    {
      print "<input type='radio' name='answer' value='$pollans3[ID]'> $pollans3[answer]<br>";
    }
  print "<input type='submit' value='submit' value='vote'>";
  print "</form>";
//  print "<A href='results.php' target='top' height='300' width='300' scrollbars='0'>Results</a>";

}
?>
