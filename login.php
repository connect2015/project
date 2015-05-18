<?php

require_once('config.php');
require_once('function.php');

session_start();

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
	if (!$me = getUser($email, $password, $dbh)){
		$err['password'] = 'パスワードが一致しません';
	}

	if(empty($err)){
		//セッションハイジャック対策
		session_regenerate_id();

		//ログイン処理
		$_SESSION['me'] = $me;
		header('Location:'.SITE_URL);
		exit;
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
	<form action="" method="POST">
		<p>メールアドレス：<input type="text" name="email" value="<?php echo h($email); ?>"><?php echo h($err['email']); ?></p>
		<p>パスワード：<input type="password" name="password" value="<?php echo h($password); ?>"><?php echo h($err['password']); ?></p>
		<input type="hidden" name="token" value="<?php echo h($_SESSION['token']); ?>">
		<p><input type="submit" value='ログイン'></p>
	</form>

</body>
</html>



