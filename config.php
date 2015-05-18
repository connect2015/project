<?php

define('DSN','mysql:host=localhost;dbname=connect');
define('DB_USER','dbuser');
define('DB_PASSWORD','kohei1993');

define('SITE_URL','http://localhost/project/');
define('PASSWORD_KEY','kohei524');

error_reporting(E_ALL & ~E_NOTICE);
ini_set( 'display_errors', 1 );


session_set_cookie_params(0,'/project/');
