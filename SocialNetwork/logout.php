<?php
	include_once('php/globals.php');
	session_destroy();
?>
<!doctype html>
<html>		
	<head><title>Logout</title></head>
	
	<body onload="setTimeout(function(){window.location='index.php'},1000)">	
		
		Logging you out...
		
		<?php include_once('js/fb.js.php'); ?>		
	
	</body>
</html>