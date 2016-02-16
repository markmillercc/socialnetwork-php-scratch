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
	
	/* Get required user information */
	$user_info = $User->getInfo(false, array('id', 'first_name', 'last_name'));
	/*******************************/
	
	/* Get user profile picture */
	$user_pic = $User->getProfilePicture();
	/*******************************/
	
	/* Insert comment */
	$User->postComment($_POST['post_id'], $_POST['comment']);
	/*******************************/
?>
	<div class="comment">
										
		<img class="comment_author_pic" src="<?=$user_pic['small']?>" />
	
		<div class="comment_body">
			
			<a href="profile.php?id=<?=$user_info['id']?>"><?=ucwords($user_info['first_name'].' '.$user_info['last_name'])?></a>
		
			<span class="comment_date"><?=date('M j, Y g:ia')?></span>
			
			<div class="comment_text"><?=strip_tags($_POST['comment'])?></div>
			
		</div><!--/.comment_body-->
	
		<div style="clear:both"></div>
	
	</div><!--/.comment-->
	
<?php $Db = $User = false; ?>