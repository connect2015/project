<?php

define('DSN','mysql:host=localhost;dbname=connect');
define('DB_USER','dbuser');
define('DB_PASSWORD','kohei1993');

define('SITE_URL','http://localhost/project/');
define('PASSWORD_KEY','kohei524');

error_reporting(E_ALL & ~E_NOTICE);
ini_set( 'display_errors', 1 );


session_set_cookie_params(0,'/project/');

//画像関連
define('IMAGES_DIR', dirname($_SERVER['SCRIPT_FILENAME'])."/images");
define('MAX_SIZE', 30720000);

//GD
if(!function_exists('imagecreatetruecolor')){
	echo "GDがインストールされていません!";
	exit;
}



