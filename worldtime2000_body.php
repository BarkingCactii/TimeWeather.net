<?php
	echo '<table><tr>';
	echo '<td valign="top">';
	$product = 1;
	$count = 0;
	include ('products.php');
	include ('news.php');
	echo '</td>';
	echo '<td valign="top">';
	include ('worldtime2000_ss.php');
	echo '</td>';
	echo '</tr></table>';
?>