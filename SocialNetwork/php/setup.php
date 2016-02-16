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
	 * Handles all user actions and profile data retrieval */
	$User = new User($_SESSION['id'], $Db);
	/*******************************/
	
	/* The id of the user to which page belongs
	 * If false, then page belongs to current user, i.e. logged-in user */
	$user_id = false;
	/*******************************/
	
	/* Check for profile id - GET
	 * If $_SESSION['id'], page belongs to current user
	 * If not current user, validate id and set $user_id
	 * If id not valid, redirect to current user profile page */
	if (isset($_GET['id'])) {
		if ($_GET['id'] == $_SESSION['id']) {
			header("Location: profile.php");
			die;
		}
		elseif (!$User->getInfo($_GET['id'], 'id')) {
			header("Location: profile.php");
			die;
		}
		else $user_id = $_GET['id'];
	}
	/*******************************/
	
	/* Handle profile info update */
	if (isset($_POST['update_profile'])) {
		unset($_POST['update_profile']);
		
		if (!$_POST['dob'])
			unset($_POST['dob']);
		else 
			if (!$dob = strtotime($_POST['dob']))
				echo "Error updating birthday.";
			else 
				$_POST['dob'] = date('Y-m-d', $dob);
		
		foreach ($_POST as $i => $p)
			if (!$p) unset($_POST[$i]);
		
		if (!$User->updateProfile($_POST))
			echo "Profile update failed.";
	}
	/*******************************/
	
	/* Handle profile picture update */
	if (isset($_GET['set_pic'])) {
		if (!$User->setProfilePicture($_GET['set_pic']))
			echo "Failed to set profile picture";
		else {
			header("Location: profile.php");
			die;
		}
	}
	/*******************************/
	
	/* Handle profile picture delete */
	if (isset($_GET['delete_pic'])) {
		if (!$User->deleteProfileImage($_GET['delete_pic']))
			echo "Failed to delete profile picture";
		else {
			header("Location: profile.php");
			die;
		}
	}
	/*******************************/
	
	/* Handle a new wall post */
	if (isset($_POST['post'])) {
		if ($_FILES['image']['error'] == 0)
			$file = $_FILES['image'];
		else $file = false;
		if (!$User->postToWall($user_id, $_POST['text'], $_POST['url'], $file))
			echo "Error posting to wall!";
	}
	/*******************************/
	
	/* Handle new profile image upload */
	if (isset($_FILES['profile_image']))
		if (!$User->addProfileImage($_FILES['profile_image']))
			echo "Failed to upload image.";
	/*******************************/
	
	/* Array of current profile info */
	$user_info = $User->getInfo($user_id);
	/*******************************/
	
	/* Current main profile picture */
	$user_pic = $User->getProfilePicture($user_id);
	/*******************************/ 
	
	/* Array of all other profile pictures */
	$all_user_pics = $User->getAllImages($user_id);
	/*******************************/
	
	/* Set up pagination for wall posts
	 * Get array of wall posts for current profile id and current page # */
	$current_pg = $last_pg = $pages = 1;
	$start = 0;
	$qty = 25;
	
	if (isset($_GET['p']))
		$current_pg = $_GET['p'];
	
	$wall_num = $User->countWall($user_id);

	if ($wall_num > $qty) {
		$pages = ceil($wall_num/$qty);
		
		if ($current_pg < $pages)
			$last_pg = false;

		$start = $qty*($current_pg-1);
	}
	$first = $start + 1;
	$last = $first + ( !$last_pg ? ($qty-1) : $wall_num-(($pages-1)*$qty)-1 );
	
	$wall = $User->getWall($user_id, $start, $qty);
	/*******************************/
	
	/* Set up profile picture display (all pics)
	 * Step 1: Set number of cols and pic size depending on total number of pics */
	$num_pics = count($all_user_pics);
	
	if ($num_pics <= 10) {
		$num_cols = 1;
		$pic_size = 'medium';
		$max_width = '326px';
	}
	elseif ($num_pics <= 20) {
		$num_cols = 2;
		$pic_size = 'medium';
		$max_width = '161px';
	}
	elseif ($num_pics <= 40) {
		$num_cols = 3;
		$pic_size = 'small';
		$max_width = '106px';
	}
	elseif ($num_pics <= 80) {
		$num_cols = 4;
		$pic_size = 'small';
		$max_width = '78px';
	}
	else {
		$num_cols = 5;
		$max_width = '62px';
		$pic_size = 'small';
	}
	/*******************************/
	
	/* Set up profile picture display (all pics)
	 * Step 2: Set up each pic column and determine how many pics/col */
	$cols = array();
	
	for ($i=0; $i<$num_cols; $i++) 
		$cols[$i] = floor($num_pics/$num_cols);
	
	if ($num_pics % $num_cols != 0)
		for ($i=0; $i<$num_pics%$num_cols; $i++)
			$cols[$i]++;
	/*******************************/
	
	/* Get first_name last_name for logged in user */
	$logged_user = array();
	if (!$user_id) {
		$logged_user['first_name'] = $user_info['first_name'];
		$logged_user['last_name'] = $user_info['last_name'];
	}
	else
		$logged_user = $User->getInfo(false, array('first_name', 'last_name'));
	/*******************************/
?>