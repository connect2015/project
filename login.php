<?php

session_start();

require_once('config.php');
require_once('function.php');
require_once('facebook.php');

//facebookログイン用
$config = array(
    'appId'  => APP_ID,
    'secret' => APP_SECRET
);
$facebook = new Facebook($config);

if ($facebook->getUser()) {
	try {
		$user = $facebook->api('/me','GET');
		$_SESSION['me'] = $user;

		header("Location: mypage.php");
		exit;


	} catch(FacebookApiException $e) {
		//取得に失敗したら例外をキャッチしてエラーログに出力
		error_log($e->getType());
		error_log($e->getMessage());
	}
}

//ヘッダー設定
Head($_SESSION['me']['name']);

?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<title>ログイン</title>
</head>
<body>
<?php
		if (isset($user)) {
			//ログイン済みでユーザー情報が取れていれば表示

			echo '<pre>';
			print_r($user);
			echo '</pre>';

			//ログアウト処理
			$logoutUrl = $facebook->getLogoutUrl( array('next' => SITE_URL.'logout.php'));
		    //ログアウト用のリンクを出力
		    echo '<h1><a href="'.$logoutUrl.'">ログアウト</a></h1>';

		} else {
			//未ログインならログイン URL を取得してリンクを出力
			$loginUrl = $facebook->getLoginUrl();
			echo '<h1><a href="' . $loginUrl . '">ログイン</a></h1>';
		}
	?>
</body>
</html>
