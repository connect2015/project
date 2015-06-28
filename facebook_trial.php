<?php

require_once('config.php');
require_once('function.php');


define('FACEBOOK_SDK_V4_SRC_DIR', '/Applications/MAMP/htdocs/project/facebook-php-sdk-v4/src/Facebook/');
//require __DIR__ . '/facebook-php-sdk-v4/autoload.php';


require '/Applications/MAMP/htdocs/project/facebook-php-sdk-v4/autoload.php';

use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRequestException;
use Facebook\FacebookJavaScriptLoginHelper;

FacebookSession::setDefaultApplication('436865333162259', 'e5734ceef09b1e70dbaea90660ede073');



// Add `use Facebook\FacebookJavaScriptLoginHelper;` to top of file
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

//var_dump($session);


if($session) {
  try {
    $user_profile = (new FacebookRequest(
      $session, 'GET', '/me'
    ))->execute()->getGraphObject(GraphUser::className());
    echo "Name: " . $user_profile->getName();
    $username = $user_profile->getName();    
    $user_url = $user_profile->getLink();
    $user_id = $user_profile->getId();
    //var_dump($user_profile);

    //新規登録されてるかチェック
    $dbh = connectDb();
    if(!user_idExist($user_id,$dbh)){
    
    //新規登録処理
    $sql = "insert into users
    (id, username, created, modified) 
    values
    (:user_id, :name, now(),now())";


    $stmt = $dbh->prepare($sql);

    $params = array(
      ":user_id"=> $user_id,
      ":name"=> $username,
      );

    $stmt->execute($params);
    }
    

  } catch(FacebookRequestException $e) {
    echo "Exception occured, code: " . $e->getCode();
    echo " with message: " . $e->getMessage();
  }   
}



echo "<script src='connect.js'></script>";


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

