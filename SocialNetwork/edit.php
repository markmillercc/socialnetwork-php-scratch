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
	
	/* Array of current profile info */
	$user_info = $User->getInfo();
	/*******************************/
?>
<!doctype html>
<html>
	
	<head>
		
		<meta charset= "utf-8" />
		<title>SocialNetwork | Profile Information</title>
		<link rel="stylesheet" type="text/css" href="css/style.css">
		
	</head>
	
	<body>
		<div id="edit_form">
		
			<h1>Tell us about yourself</h1>
			
			<form action="profile.php" method="POST">
				
				<div class="field">
					<h3>What's your favorite quote?</h3>
					<input name="quote" type="text" maxlength=255 value="<?=$user_info['quote']?>" placeholder="ex. Freeeeeedom!!"/>
				</div>
				
				<div class="field">
					<h3>Who said that?</h3>
					<input name="quote_src" type="text" maxlength=75 value="<?=$user_info['quote_src']?>" placeholder="ex. William Wallace"/>
				</div>
				
				<div class="field">
					<h3>Where do you currently live?</h3>
					<input name="location" type="text" maxlength=75 value="<?=$user_info['location']?>" placeholder="Any combo of city, state, country, region, etc"/>
				</div>
				
				<div class="field">
					<h3>Where are you from?</h3>
					<input name="hometown" type="text" maxlength=75 value="<?=$user_info['hometown']?>" placeholder="Your hometown"/>
				</div>
				
				<div class="field">
					<h3>Where did you go to school?</h3>
					<input name="education" type="text" maxlength=75 value="<?=$user_info['education']?>" placeholder="ex. Starfleet Academy"/>
				</div>
				
				<div class="field">
					<h3>Where do you work? What do you do?</h3>
					<input name="work" type="text" maxlength=75 value="<?=$user_info['work']?>" placeholder="ex. Astrophysicist at SNASA (secret NASA)"/>
				</div>
				
				<div class="field">
					<h3>What is your relationship status?</h3>
					<input name="relationship" type="text" maxlength=75 value="<?=$user_info['relationship']?>" placeholder="Single? Married? Other?"/>
				</div>
				
				<div class="field">
					<h3>Your gender?</h3>
					<select name="gender">
						<option> </option>
						<option <?=(isset($user_info['gender'])&&$user_info['gender']=='m'?'selected':'')?> value="m">Male</option>
						<option <?=(isset($user_info['gender'])&&$user_info['gender']=='f'?'selected':'')?> value="f">Female</option>
					</select>
				</div>
				
				<div class="field">
					<h3>When is your birthday?</h3>
					<input name="dob" type="text" maxlength=50 value="<?=($user_info['dob']?date('F d, Y', strtotime($user_info['dob'])):'')?>" placeholder="ex. March 13, 1984 or 3/13/1984"/>
				</div>
				
				<div class="field">
					<h3>Anything else you want to share?</h3>
					<textarea name="about" maxlength=2000><?=$user_info['about']?></textarea>
				</div>
				
				<div class="field">
					<input class="submit" name="update_profile" type="submit" value="Save"/>
					<a href="profile.php">Cancel</a>
				</div>
			
			</form>
		
		</div><!--/#edit_form-->
	
	</body>

</html>