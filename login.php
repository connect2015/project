<?php

session_start();

require_once('config.php');
require_once('function.php');
require_once("facebook.php");

//facebookログイン用
$config = array(
    'appId'  => '1588772488021580',
    'secret' => '0049bd6e09b21a3fd257461f7b675ec2'
);
$facebook = new Facebook($config);

if ($facebook->getUser()) {
	try {
		$user = $facebook->api('/me','GET');
	} catch(FacebookApiException $e) {
		//取得に失敗したら例外をキャッチしてエラーログに出力
		error_log($e->getType());
		error_log($e->getMessage());
	}
}

//ヘッダー設定
Head($_SESSION['me']['username']);

?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<title>ログイン</title>
</head>
<body>

	<h1>ログイン</h1>
<?php
		if (isset($user)) {
			//ログイン済みでユーザー情報が取れていれば表示
			echo '<pre>';
			print_r($user);
			echo '</pre>';

			//ログアウト処理
			$logoutUrl = $facebook->getLogoutUrl();
		    //ユーザ情報を取得
		    $user_info = $facebook->getUser();
		    //ログアウト用のリンクを出力
		    echo "<h1><a href='".$logoutUrl."'>ログアウト</a></h1>";

		} else {
			//未ログインならログイン URL を取得してリンクを出力
			$loginUrl = $facebook->getLoginUrl();
			echo '<h1><a href="' . $loginUrl . '">Login with Facebook</a></h1>';
		}
	?>
</body>
</html>

