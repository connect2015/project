<?php

require_once('config.php');
require_once('function.php');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {

	//POSTではなかったら
	$name = '';
	$email = '';
	$password = '';
	setToken();
	$dbh = connectDB();

	//countryの情報を取得
	$countries = array();
	$sql = "select * from countries";
	foreach($dbh->query($sql) as $row){
		array_push($countries,$row);
	}

	//universityの情報を取得
	$alluniversity = array();
	$sql = "select * from universities";
	foreach($dbh->query($sql) as $row){
		array_push($alluniversity,$row);
	}

} else {

	//POSTだったら
	checkToken();

	$name = $_POST['name'];
	$email = $_POST['email'];
	$password = $_POST['password'];

	$dbh = connectDB();
	$err = array();

	//名前が空
	if (!$name){
		$err['name'] = '入力してください';
	}

	//メールアドレスの形式がおかしい
	if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
		$err['email'] = 'メールアドレスが正しくないです';
	}

	//メールアドレスが空
	if (!$email){
		$err['email'] = '入力してください';
	}

	//メールアドレスがすでに登録されてない？
	if(emailExist($email,$dbh)) {
		$err['email'] = 'このメールアドレスは使用されています';
	}

	//パスワードが空
	if (!$password){
		$err['password'] = '入力してください';
	}

	if(empty($err)){
		//登録処理
		
		header("Location: ".SITE_URL."login.php");
		exit;
	}
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<title>新規登録</title>
</head>
<body>
	<h1>新規登録</h1>
	<form id="main" action="" method="POST">
		<p>お名前：<input type="text" name="name" value="<?php echo h($name); ?>"><?php echo h($err['name']); ?></p>
		<p>メールアドレス：<input type="text" name="email" value="<?php echo h($email); ?>"><?php echo h($err['email']); ?></p>
		<p>パスワード：<input type="password" name="password" value="<?php echo h($password); ?>"><?php echo h($err['password']); ?></p>
		<p>国名：
		<p>大学名：
		</p>
		<input type="hidden" name="token" value="<?php echo h($_SESSION['token']); ?>">
		<p><input type="submit" value='新規登録'></p>
	</form>
</body>
</html>

