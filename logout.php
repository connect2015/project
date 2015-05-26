<?php

require_once('config.php');
require_once('function.php');
require_once('facebook.php');

session_start();
$_SESSION = array();
if(isset($_COOKIE[session_name()])){
	setcookie(session_name(), '', time()- 86400, '/connect/');
}

session_destroy();


$config = array(
    'appId'   => APP_ID,
    'secret'  => APP_SECRET,
);
$facebook = new Facebook($config);

$facebook->destroySession(); 

header("Location: login.php");
exit;