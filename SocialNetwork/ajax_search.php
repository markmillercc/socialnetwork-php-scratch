<?php
	require("php/globals.php");
	
	/* Confirm user is logged in */
	if (!isset($_SESSION['id'])) {
		header("Location: index.php");
		die;
	}
	/*******************************/
	
	/* Database handler */
	$Db = new PdoMySql($GLOBALS['DB_HOST'], $GLOBALS['DB_NAME'], $GLOBALS['DB_USERNAME'], $GLOBALS['DB_PASSWORD']);
	/*******************************/
	
	/* User handler
	 * Belongs to logged in user (current user) */
	$User = new User($_SESSION['id'], $Db);
	/*******************************/
	
	/* Get search results */
	$results = $User->search($_POST['terms']);
	/*******************************/
?>
	
<?php if ($results) { ?>
	
	<div id='result_container'>
	
		<?php foreach ($results as $r) { ?>
			
			<div class='result'>
				
				<img src='<?=$r['picture']['small']?>' />
				
				<a href='profile.php?id=<?=$r['id']?>'><?=ucwords($r['first_name'].' '.$r['last_name']).' '.(!$r['location']?'':'&#8226; '.$r['location'])?></span></a>
			
			</div><!--/.result-->
		
		<?php } ?>
	
	</div><!--/#result_container-->

<?php } elseif($_POST['terms'] != '') { ?>

	<div id='result_container'>no results</div>

<?php } ?>

<?php $Db = $User = false; ?>