<?php
$navlist = array (
        "names" => array("Home", "WorldTime2000", "WhenOnEarth", "WorldTime2003", "Employment", "Site Map", "About Us"),
        "url" => array("/default.php", "/worldtime2000.php", "/whenonearth.php", "/worldtime2003.php", "/employment.php", "/sitemap.php", "/about.php")
);



echo '<font size="2" face="Arial">';
$navcount = sizeof($navlist[names]);
for ($i = 0; $i < $navcount; $i++)
        {
        

        if (strtolower($_SERVER['URL']) == $navlist['url'][$i])
                {
		echo '<i><b><a class="nav" href="';
		echo $navlist['url'][$i];
		echo '">';
		echo $navlist['names'][$i];
		echo '</a></b></i>';
                }

		else
		
				
		{
		echo '<a class="nav" href="';
		echo $navlist['url'][$i];
		echo '">';
		echo $navlist['names'][$i];
		echo '</a>';
                }

	echo '  |  ';             
                
        }
echo '</font>';        
        
?>