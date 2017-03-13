<?php
###################################################################################
#
# Weather report 1.1 config.php, by Aid Arslanagic, version 0.1
# http://arsla.epn.ba
#
# This code is released under The GNU General Public License (GPL).
# Read the license at http://www.opensource.org/licenses/gpl-license.php
#
###################################################################################
require ("xmllib.php");
###################################################################################
# XML Library, by Keith Devens, version 1.2
# http://keithdevens.com/software/phpxml
###################################################################################

$source = "http://xoap.weather.com/weather/local/";
$prod = "xoap";
###################################################################################
# Check your location at this address:
# http://xoap.weather.com/search/search?where=sarajevo # replace "sarajevo" with
# your city and enter your location here:
$code = "ASXX0016";
## $code = "BKXX0004";
###################################################################################
$cc = "*";
###################################################################################
# You can change units from Metric "m" to Standard "s":
$unit = "m";
###################################################################################
$par = "1005217190";
$key = "2e4490982af206e0";
$query =  $source . $code . "?prod=" . $prod . "&cc=" . $cc . "&unit=" . $unit . "&par=" . $par . "&key=" . $key;
?>
