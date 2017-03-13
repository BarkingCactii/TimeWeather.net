<?php
include "connect.php";
$s=$_SERVER["REMOTE_ADDR"];
$ipchecks="SELECT*from P_ip where IP='$s'";
$ipchecks2=mysql_query($ipchecks);
while($ipchecks3=mysql_fetch_array($ipchecks2))
    {
     $isip=$ipchecks3[IP];
    }

if($isip)
    {
     print "You have already voted in this poll";
    }
     
else
    {
        $ID=$_POST['answer'];    
        $ipinsert="Insert into P_ip(IP) VALUES('$s')";
        mysql_query($ipinsert);
      	$vote = "UPDATE P_choices SET votes=votes+1 
		WHERE ID = '$ID'";
        mysql_query($vote);
        print "Thanks for voting, <A href='../default.php'>Back</a>";
    }


     


?>