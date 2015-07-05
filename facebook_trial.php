<?php

require_once('config.php');
require_once('function.php');

facebookLogin();

?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<title>Top</title>
</head>
<body>

<fb:login-button scope="public_profile,email" onlogin="checkLoginState();" auto_logout_link="true">
</fb:login-button>

<div id="status">
</div>



<div id="profile">
</div>

<a href="<?php echo $user_url ?>">userpage</a>

<?php echo $user_id ;?>

<div class="fb-comments" data-href="http://localhost/facebook_login/facebook.php" data-version="v2.3"></div>


<div class="fb-like" data-href="http://localhost/facebook_login/trial.php" data-layout="standard" data-action="like" data-show-faces="true" data-share="true"></div>

<script src="connect.js"></script>
</body>
</html>

