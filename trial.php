<?php 

define('FACEBOOK_SDK_V4_SRC_DIR', '/Applications/MAMP/htdocs/project/facebook-php-sdk-v4/src/Facebook/');

require '/Applications/MAMP/htdocs/project/facebook-php-sdk-v4/autoload.php';
require_once('config.php');

use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRequestException;
use Facebook\FacebookJavaScriptLoginHelper;

FacebookSession::setDefaultApplication(APP_ID, APP_SECRET);


$helper = new FacebookJavaScriptLoginHelper();
try {
  $session = $helper->getSession();
} catch(FacebookRequestException $ex) {
  // When Facebook returns an error
} catch(\Exception $ex) {
  // When validation fails or other local issues
}
if ($session) {
  // Logged in
}


if($session) {
  try {
    $user_profile = (new FacebookRequest(
      $session, 'GET', '/me'
    ))->execute()->getGraphObject(GraphUser::className());
    echo "Name: " . $user_profile->getName();
    $user_url = $user_profile->getLink();
  } catch(FacebookRequestException $e) {
    echo "Exception occured, code: " . $e->getCode();
    echo " with message: " . $e->getMessage();
  }   
}

?>


<!DOCTYPE html>
<html>
<head>
<title>Facebook Login PHP Example</title>
<meta charset="UTF-8">
</head>
<body>
  <script>
  // This is called with the results from from FB.getLoginStatus().
  function statusChangeCallback(response) {
    console.log('statusChangeCallback');
    console.log(response);
    // The response object is returned with a status field that lets the
    // app know the current login status of the person.
    // Full docs on the response object can be found in the documentation
    // for FB.getLoginStatus().
    if (response.status === 'connected') {
      // Logged into your app and Facebook.
      testAPI();
      info();
    } else if (response.status === 'not_authorized') {
      // The person is logged into Facebook, but not your app.
      document.getElementById('status').innerHTML = 'Please log ' +
        'into this app.';
    } else {
      // The person is not logged into Facebook, so we're not sure if
      // they are logged into this app or not.
      document.getElementById('status').innerHTML = 'Please log ' +
        'into Facebook.';
    }
  }

  // This function is called when someone finishes with the Login
  // Button.  See the onlogin handler attached to it in the sample
  // code below.
  function checkLoginState() {
    FB.getLoginStatus(function(response) {
      statusChangeCallback(response);
    });
  }

  window.fbAsyncInit = function() {
  FB.init({
    appId      : '1588772488021580',
    cookie     : true,  // enable cookies to allow the server to access 
                        // the session
    xfbml      : true,  // parse social plugins on this page
    version    : 'v2.3' // use version 2.2
  });

  // Now that we've initialized the JavaScript SDK, we call 
  // FB.getLoginStatus().  This function gets the state of the
  // person visiting this page and can return one of three states to
  // the callback you provide.  They can be:
  //
  // 1. Logged into your app ('connected')
  // 2. Logged into Facebook, but not your app ('not_authorized')
  // 3. Not logged into Facebook and can't tell if they are logged into
  //    your app or not.
  //
  // These three cases are handled in the callback function.

  FB.getLoginStatus(function(response) {
    statusChangeCallback(response);
  });

  };

  // Load the SDK asynchronously
  (function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));

  // Here we run a very simple test of the Graph API after login is
  // successful.  See statusChangeCallback() for when this call is made.
  function testAPI() {
    console.log('Welcome!  Fetching your information.... ');
    FB.api('/me', function(response) {
      console.log('Successful login for: ' + response.name);
      document.getElementById('status').innerHTML =
        'Thanks for logging in, ' + response.name + '!';
      });
  }

// function info(){
//   FB.api('/me', function(response) {
//   document.getElementById('profile').innerHTML =　'hello' + response.name;
// });
// }


</script>

<!--
  Below we include the Login Button social plugin. This button uses
  the JavaScript SDK to present a graphical Login button that triggers
  the FB.login() function when clicked.
-->

<fb:login-button scope="public_profile,email" onlogin="checkLoginState();" auto_logout_link="true">
</fb:login-button>



<div id="status">
</div>


<div id="profile">
</div>

<a href="<?php echo $user_url ?>">userpage</a>


<div class="fb-comments" data-href="http://localhost/facebook_login/facebook.php" data-version="v2.3"></div>

<div class="fb-like" data-href="http://localhost/facebook_login/trial.php" data-layout="standard" data-action="like" data-show-faces="true" data-share="true"></div>



</body>
</html>