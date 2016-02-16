<?php require('php/login.php');?>
<!doctype html>
<html>
	
	<head>
		<meta charset= "utf-8" />
		<title>SocialNetwork | Login</title>
		<link rel="stylesheet" type="text/css" href="css/style.css">
	</head>

	<body>
	
		<div id="header">
			
			<h1>SocialNetwork</h1>
			
			<div style="clear:both"></div>
		
		</div><!--/#header-->

		<div id="wrap">
			
			<div id="welcome">
				
				<div id="login" style="display:<?=(!$reg_errors?'':'none')?>">
					<?php
						if ($login_errors) {
							echo "<br/>";
							foreach($login_errors as $e)
								echo "&#8226; {$e}<br/>";
							echo "<br/>";
						}
					?>
					<h2>Login</h2>
					
					<form method="POST">
						
						<div class="field">
							<h3>Email:</h3>
							<input type="text" name="email" placeholder="email" />
						</div>
						
						<div class="field">
							<h3>Password:</h3>
							<input type="password" name="password" placeholder="password" />
						</div>
						
						<div class="field">
							<input class="submit" type="submit" name="local_login" value="Login" />
						</div>
                        
						<?php if (!empty($GLOBALS['FACEBOOK_APP_ID'])) {  ?>                                                                                                                
                            <div class="field">
                                <fb:login-button size="large" data-scope="email" max-rows="1">Login with Facebook</fb:login-button>
                            </div> 
                            <div style="clear:both"></div>                           
                        <?php } ?>                        
						
						<div class="field" style="float:right">
							<h3><a id="reg_button">Create a new account</a></h3>
						</div>

					</form>

					<form id="fb_login_form" method="POST">

						<input id="fb_id_input" type="hidden" name="fb_id" />

						<input id="fb_email_input" type="hidden" name="fb_email" />

						<input id="fb_first_name_input" type="hidden" name="fb_first_name" />

						<input id="fb_last_name_input" type="hidden" name="fb_last_name" />

					</form>

				</div><!--/login-->
				
				<div id="reg" style="display:<?=(!$reg_errors?'none':'')?>">
					<?php
						if ($reg_errors) {
							echo "<br/>";
							foreach($reg_errors as $e)
								echo "&#8226; {$e}<br/>";
							echo "<br/>";
						}
					?>
					<h2>Create a new account</h2>
				
					<form method="POST">
						
						<div class="field">
							<h3>First name:*</h3>
							<input type="text" name="first_name" value="<?=(isset($_POST['first_name'])?$_POST['first_name']:'')?>"/>
						</div>
						
						<div class="field">
							<h3>Last name:*</h3>
							<input type="text" name="last_name" value="<?=(isset($_POST['last_name'])?$_POST['last_name']:'')?>"/>
						</div>
						
						<div class="field">
							<h3>Email:*</h3>
							<input type="text" name="email" value="<?=(isset($_POST['email'])?$_POST['email']:'')?>"/>
						</div>
						
						<div class="field">
							<h3>Password:*</h3>
							<input type="password" name="password" value="<?=(isset($_POST['password'])?$_POST['password']:'')?>"/>
						</div>

						<div class="field">
							<h3>Re-type password:*</h3>
							<input type="password" name="repassword" value="<?=(isset($_POST['repassword'])?$_POST['repassword']:'')?>"/>
						</div>
						
						<h2>Tell us about yourself (optional)</h2>
						
						<div class="field">
							<h3>What's your favorite quote?</h3>
							<input name="quote" type="text" maxlength=255 placeholder="ex. Freeeeeedom!!" value="<?=(isset($_POST['quote'])?$_POST['quote']:'')?>"/>
						</div>
						
						<div class="field">
							<h3>Who said that?</h3>
							<input name="quote_src" type="text" maxlength=75 placeholder="ex. William Wallace" value="<?=(isset($_POST['first_name'])?$_POST['first_name']:'')?>"/>
						</div>
						
						<div class="field">
							<h3>Where do you currently live?</h3>
							<input name="location" type="text" maxlength=75 placeholder="Any combo of city, state, country, region, etc" value="<?=(isset($_POST['location'])?$_POST['location']:'')?>"/>
						</div>
						
						<div class="field">
							<h3>Where are you from?</h3>
							<input name="hometown" type="text" maxlength=75 placeholder="Your hometown" value="<?=(isset($_POST['hometown'])?$_POST['hometown']:'')?>"/>
						</div>
						
						<div class="field">
							<h3>Where did you go to school?</h3>
							<input name="education" type="text" maxlength=75 placeholder="ex. Starfleet Academy" value="<?=(isset($_POST['education'])?$_POST['education']:'')?>"/>
						</div>
						
						<div class="field">
							<h3>Where do you work? What do you do?</h3>
							<input name="work" type="text" maxlength=75 placeholder="ex. Astrophysicist at SNASA (secret NASA)" value="<?=(isset($_POST['work'])?$_POST['work']:'')?>"/>
						</div>
						
						<div class="field">
							<h3>What is your relationship status?</h3>
							<input name="relationship" type="text" maxlength=75 placeholder="Single? Married? Other?" value="<?=(isset($_POST['relationship'])?$_POST['relationship']:'')?>"/>
						</div>
						
						<div class="field">
							<h3>Your gender?</h3>
							<select name="gender">
								<option> </option>
								<option <?=(isset($_POST['gender'])&&$_POST['gender']=='m'?'selected':'')?> value="m">Male</option>
								<option <?=(isset($_POST['gender'])&&$_POST['gender']=='f'?'selected':'')?> value="f">Female</option>
							</select>
						</div>
						
						<div class="field">
							<h3>When is your birthday?</h3>
							<input name="dob" type="text" maxlength=50 placeholder="ex. March 13, 1984 or 3/13/1984" value="<?=(isset($_POST['dob'])?$_POST['dob']:'')?>"/>
						</div>
						
						<div class="field">
							<h3>Anything else you want to share?</h3>
							<textarea name="about" maxlength=2000><?=(isset($_POST['about'])?$_POST['about']:'')?></textarea>
						</div>
						
						<div class="field">
							<input class="submit" type="submit" name="register" value="Submit" />
							<a href="index.php">Cancel</a>
						</div>

					</form>
					
				</div><!--/#reg-->
				
			</div><!--/#welcome-->

		</div><!--/#wrap-->
		
		<div id="fb-root"></div>
		
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js" type="text/javascript"></script>
		<script src="js/functions.js" type="text/javascript"></script>
		
		<?php include_once('js/fb.js.php');?>
	
	</body>

</html>