<?php

use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRequestException;
use Facebook\FacebookJavaScriptLoginHelper;

function connectDb(){
	try {
		return new PDO(DSN, DB_USER, DB_PASSWORD);
	} catch (PDOException $e){
		echo $e->getMessage();
		exit;
	}
}

function h($s) {
	return htmlspecialchars($s, ENT_QUOTES, "UTF-8");
}

function setToken() {
	$token = sha1(uniqid(mt_rand(), true));
	$_SESSION['token'] = $token;
}

function checkToken() {
	if (empty($_SESSION['token']) || ($_SESSION['token'] != $_POST['token'])) {
		echo "不正な処理が行われました。";
		exit;
	}
}

function emailExist($email, $dbh){
	$sql = "select * from users where email = :email limit 1";
	$stmt = $dbh->prepare($sql);
	$stmt->execute(array(":email" => $email));
	$user = $stmt->fetch();
	return $user ? true : false;
}

function user_idExist($user_id, $dbh){
	$sql = "select * from users where id = :user_id limit 1";
	$stmt = $dbh->prepare($sql);
	$stmt->execute(array(":user_id" => $user_id));
	$user = $stmt->fetch();
	return $user ? true : false;
}



function getSha1Password($s){
	return (sha1(PASSWORD_KEY.$s));
}

function facebookLogin(){

  require '/Applications/MAMP/htdocs/project/facebook-php-sdk-v4/autoload.php';

  FacebookSession::setDefaultApplication(APP_ID, APP_SECRET);

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
}

FacebookLogin();

