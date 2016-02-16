<?php
	require('php/globals.php');
	
	/* Confirm user is not already logged in */
	if (isset($_SESSION['id'])) {
		header("Location: profile.php");
		die;
	}
	/*******************************/
	
	/* Database handler */
	$Db = new PdoMySql($GLOBALS['DB_HOST'], $GLOBALS['DB_NAME'], $GLOBALS['DB_USERNAME'], $GLOBALS['DB_PASSWORD']);
	/*******************************/
	
	/* Login/registration handler */
	$Reg = new Registrar($Db);
	/*******************************/

	/* Handle local user login */
	$login_errors = array();
	if (isset($_POST['local_login'])) {
		if (!$_POST['email'] || !$_POST['password'])
			$login_errors[] = "Please enter email and password.";
		elseif ($login = $Reg->localLogin($_POST['email'], $_POST['password'])) {
			$_SESSION['id'] = $login;
			header("Location: profile.php");
			die;
		}
		else $login_errors[] = "Incorrect email and/or password.";
	}
	/*******************************/
	
	/* Handle Facebook login */
	if (isset($_POST['fb_id'])) {
		if ($login = $Reg->fbLogin($_POST)) {
			$_SESSION['id'] = $login;
			header("Location: profile.php");
			die;
		}
	}
	/*******************************/
	
	/* Handle new registration */
	$reg_errors = array();
	if (isset($_POST['register'])) {
		
		if (!$_POST['first_name'])
			$reg_errors[] = "First name is required.";
		
		if (!$_POST['last_name'])
			$reg_errors[] = "Last name is required.";
			
		if (!$_POST['email'])
			$reg_errors[] = "Email address is required";
		
		elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
			$reg_errors[] = $_POST['email']." is not a valid email address";
		
		if (!$_POST['password'])
			$reg_errors[] = "Password is required.";
		
		elseif (!$_POST['repassword'])
			$reg_errors[] = "Please re-type password.";
		
		elseif ($_POST['password'] != $_POST['repassword'])
			$reg_errors[] = "Passwords did not match.";
		
		if (!$_POST['dob'])
			unset($_POST['dob']);
		else 
			if (!$dob = strtotime($_POST['dob']))
				$reg_errors[] = "There was an error reading your birthday. Try again in the form of m/d/y.";
			else 
				$_POST['dob'] = date('Y-m-d', $dob);
		
		if (!$reg_errors) {
			unset($_POST['register']);
			unset($_POST['repassword']);
			
			foreach ($_POST as $i => $p)
				if (!$p) unset($_POST[$i]);
			
			if (!$login = $Reg->registerNew($_POST))
				$reg_errors[] = "An unexpected error occurred. Registration failed.";
			else {
				$_SESSION['id'] = $login;
				header("Location: profile.php");
				die;
			}
		}
	}
	/*******************************/
?>