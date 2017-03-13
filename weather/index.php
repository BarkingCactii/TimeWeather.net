<?php
###################################################################################
#
# Weather report 1.1 index.php, by Aid Arslanagic, version 0.1
# http://arsla.epn.ba
#
# This code is released under The GNU General Public License (GPL).
# Read the license at http://www.opensource.org/licenses/gpl-license.php
#
###################################################################################

require ("inc/config.php");

$xml = @fread(fopen($query,"r"),10000) or die("Cant open $source!");
echo $query;
$data = XML_unserialize($xml);

###################################################################################
# Change the layout for your site below
###################################################################################

echo "<TABLE border=0><TR><TD>";
echo "<IMG SRC='weather/icons/" . $data[weather][cc][icon] . ".png'>";
echo "</TD></TR>";
echo "<TR><TD>";
echo "It's " . $data[weather][cc][tmp] . "°" . $data[weather][head][ut] . " in " . $data[weather][loc][dnam] . ".";
echo "</TD></TR>";
echo "</TABLE>";
?>
