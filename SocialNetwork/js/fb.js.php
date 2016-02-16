<script type='text/javascript'>
	window.fbAsyncInit = function() {
		FB.init({
			appId      : '<?=$GLOBALS['FACEBOOK_APP_ID']?>',
			status     : true,
			xfbml      : true,
			cookie     : true
		});

		FB.Event.subscribe('auth.authResponseChange', function(response) {
			if (response.status === 'connected') {
				if (document.URL.indexOf('logout') != -1)
					FB.logout(); // If on logout page, logout
				else 
					fbLogin(); // If on login page, login
			} 
		});
	};

	// Load the SDK asynchronously
	(function(d){
		var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
		if (d.getElementById(id)) {return;}
		js = d.createElement('script'); js.id = id; js.async = true;
		js.src = "//connect.facebook.net/en_US/all.js";
		ref.parentNode.insertBefore(js, ref);
	}(document));		 

	function fbLogin() {
		FB.api('/me', function(response) {
			document.getElementById('fb_id_input').value = response.id;
			document.getElementById('fb_email_input').value = response.email;
			document.getElementById('fb_first_name_input').value = response.first_name;
			document.getElementById('fb_last_name_input').value = response.last_name;
			document.getElementById('fb_login_form').submit();
		});
	}
</script>