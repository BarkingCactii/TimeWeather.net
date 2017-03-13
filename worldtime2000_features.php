<?php


/* Redirige le client vers le site PHP */  
header("Location: http://www.php.net/");

/* Garantie que le code ci-dessous n'est jamais exécuté. */
exit();

define ( PRODUCT, -1 );
define ( COUNT, -1 );

define ("BODY", "worldtime2000_features_body.php", true );
//define ("LEFT_SIDEBAR", "worldtime2003_leftside.php", true );
//define ("RIGHT_SIDEBAR", "worldtime2003_rightside.php", true );
include ('template.php')

?>