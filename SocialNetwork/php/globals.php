<?php
session_start();

date_default_timezone_set('America/Los_Angeles');

/* $GLOBALS['DEBUG'] toggles debug mode
 * For best debugging results, manually set to 1,
 * Otherwise use GET query in page URL
 * Must be logged in as "dev@debug.site" */
$GLOBALS['DEBUG'] = 0;

if (isset($_GET['debug']))
	$GLOBALS['DEBUG'] = $_GET['debug'];
/********************************************/

$GLOBALS['FACEBOOK_APP_ID'] = ''; 		/* INSERT FACEBOOK APP ID */

$GLOBALS['DB_HOST'] = ''; 		/* INSERT DATABASE HOST */
$GLOBALS['DB_NAME'] = ''; 		/* INSERT DATABASE NAME */
$GLOBALS['DB_USERNAME'] = ''; 	/* INSERT DATABASE USERNAME */
$GLOBALS['DB_PASSWORD'] = ''; 	/* INSERT DATABASE PASSWORD */


include_once("debug.php");

include_once("classes/PdoMySql.class.php");
include_once("classes/Registrar.class.php");
include_once("classes/User.class.php");
?>
