<?php
/***
* function: Debug
* desc: Print Debug contents when debug is turned on 
**/

	function Debug($label="", $content="", $color='#0C0') {

		if (isset($_SESSION['id']) && $_SESSION['id']==1) {
			
			if ($GLOBALS['DEBUG']) {
				
				if ($label == 'PDOException')
					$color = "#FF0000";
		
				echo "<pre style='border:1px solid {$color};background:#fff;padding:5px;'>";
					echo "<span style='color:#f60;font-weight:700'>DEBUG IS ON. TESTING IN PROGRESS....</span><br/>";
					echo "{$label}: <br>";
					print_r($content);
				echo "</pre>";
				
				echo "<br/>";
			}
		}
	}    
?>