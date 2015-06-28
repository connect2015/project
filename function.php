<?php

session_start();

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

function Head($user){

	if($user) {

		//ログイン状態のユーザー用
		echo
		 "<a href='index.php'>トップ</a>　
		<a href='add_post.php'>投稿</a>　
		<a href='mypage.php'>マイページ</a>　
		<strong>"
		.$user.
		"</strong> でログインしています <a href='logout.php'>ログアウト</a>";

		return $_SESSION['me'];

	} else {

		//ログアウト状態のユーザー用
		echo
		 "<a href='index.php'>トップ</a>　
		<a href='login.php'>ログイン</a>　
		<a href='signup.php'>新規登録</a>　
		<a href='add_post.php'>投稿</a>";
	}
}

