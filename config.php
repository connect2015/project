<?php

session_start();

//データベース関連
define('DSN', 'mysql:host=localhost;dbname=connect');
define('DB_USER', 'dbuser');
define('DB_PASSWORD', 'connect2015');

//facebook関連
define('APP_ID', '1588772488021580');
define('APP_SECRET', '0049bd6e09b21a3fd257461f7b675ec2');
define('FACEBOOK_SDK_V4_SRC_DIR', '/Applications/MAMP/htdocs/project/facebook-php-sdk-v4/src/Facebook/');


//画像関連
define('IMAGES_DIR', dirname($_SERVER['SCRIPT_FILENAME'])."/images");
define('THUMBNAILS_DIR', dirname($_SERVER['SCRIPT_FILENAME'])."/thumbnails");
define('THUMBNAILS_WIDTH', 72);
define('MAX_SIZE', 307200);

//GD
if(!function_exists('imagecreatetruecolor')){
	echo "GDがインストールされていません!";
	exit;
}

//その他
define('SITE_URL', 'http://localhost/project/');
define('PASSWORD_KEY', 'sfoasnvosa');

error_reporting(E_ALL & ~E_NOTICE);
ini_set( 'display_errors', 1 );

session_set_cookie_params(0, '/project/');