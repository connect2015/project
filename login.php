<?php

session_start();

require_once('config.php');
require_once('function.php');

//require_once("facebook.php");

//facebookログイン用
/*$config = array(
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
*/

function getUser($email, $password, $dbh){
	$sql = "select * from users where email = :email and password = :password limit 1";
	$stmt = $dbh->prepare($sql);
	$params = array(
		":email" => $email,
		":password" => getSha1Password($password)
		);
	$stmt->execute($params);
	$user = $stmt->fetch();
	return $user ? $user : false;
}

//ログインしていたら元に戻る
if($_SESSION['me']) {
	header("Location:".SITE_URL);
	exit;
} 

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
	//POSTじゃなかったら
	setToken();

} else {
	//POSTだったら
	checkToken();

	$email = $_POST['email'];
	$password = $_POST['password'];

	$dbh = connectDB();

	$err = array();

	//メールアドレスの形式がおかしい
	if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
		$err['email'] = 'メールアドレスが正しくないです';
	}

	//メールアドレスが空
	if (!$email){
		$err['email'] = '入力してください';
	}

	//パスワードが空
	if (!$password){
		$err['password'] = '入力してください';
	}

	//メールアドレスが存在しない
	if(!emailExist($email,$dbh)) {
		$err['email'] = 'このメールアドレスは登録されていません';
	}

	//パスワードが正しくない
	if (!$me = getUser($email, $password,$dbh)) {
		$err['password'] = 'パスワードが一致しません';
	}

	if(empty($err)){
		//セッションハイジャック対策
		session_regenerate_id();

		//ログイン処理
		$_SESSION['me'] = $me;
		header('Location:'.SITE_URL);

		/*
		if($_SESSION['guest']){
			header('Location:'.SITE_URL.$_SESSION['guest']['lastpage']);
		} else {
			header('Location:'.SITE_URL);
		}
		exit; */
	}
}

//ヘッダー設定
Head($_SESSION['me']['username']);

/*
if($_SESSION['guest']['add']){
	echo "<p>".h($_SESSION['guest']['add']['message'])."</p>";
}
*/
?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<title>ログイン</title>
</head>
<body>

	<h1>ログイン</h1>
	<form action="" method="POST">
		<p>メールアドレス：<input type="text" name="email" value="<?php echo h($email); ?>"><?php echo h($err['email']); ?></p>
		<p>パスワード：<input type="password" name="password" value="<?php echo h($password); ?>"><?php echo h($err['password']); ?></p>
		<input type="hidden" name="token" value="<?php echo h($_SESSION['token']); ?>">
		<p><input type="submit" value='ログイン'></p>
	</form>

<?php
		/*if (isset($user)) {
			//ログイン済みでユーザー情報が取れていれば表示
			echo '<pre>';
			print_r($user);
			echo '</pre>';

			//ログアウト処理
			$logoutUrl = $facebook->getLogoutUrl();
		    //ユーザ情報を取得
		    $user_info = $facebook->getUser();
		    //ログアウト用のリンクを出力
		    echo "<a href='".$logoutUrl."'>ログアウト</a>";

		} else {
			//未ログインならログイン URL を取得してリンクを出力
			$loginUrl = $facebook->getLoginUrl();
			echo '<a href="' . $loginUrl . '">Login with Facebook</a>';
		}*/
	?>
</body>
</html>

