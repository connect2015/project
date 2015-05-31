<?php

require_once('config.php');
require_once('function.php');

session_start();



if($_SERVER['REQUEST_METHOD'] != 'POST'){
// CSRF対策
	setToken();
}else{
	checkToken();

	$name = $_POST['name'];
	$email = $_POST['email'];
	$password = $_POST['password'];
	$university_id = $_POST['university_id'];

	$dbh = connectDb();

	$err = array();

	//名前が空か？
	if($name ==''){
		$err['name'] = 'insert your name';
	}

	//メールアドレスが正しいのか
	if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
		$err['email'] = 'this is not validate form of email';
	}

	if(emailExist($email,$dbh)){
		$err['email'] = 'this email address is already used';
	}



	//メールアドレスが空か？
	if($email ==''){
		$err['email'] = 'insert your email';
	}


	//パスワードが空か？
	if($password ==''){
		$err['password'] = 'insert your password';
	}


	if(empty($err)){
		//エラーがからだった場合にのみ、登録処理をする
	$sql = "insert into users
		(username, email, password, university_id, created, modified) 
		values
		(:name, :email, :password, :university_id, now(),now())";


	$stmt = $dbh->prepare($sql);

	$params = array(
		":name"=> $name,
		":email"=>$email,
		":password"=> getSha1Password($password),
		":university_id"=>$university_id
		);

	$stmt->execute($params);

	header('Location:'.SITE_URL.'login.php');
	exit;
	}

}


?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<title>new user signup</title>
</head>

<body>
	<a href="index.php">Top</a>

<h1>new user signup</h1>
<form action="" method="POST">
	<p>
		Name<input type="text" name="name" value="<?php echo h($name); ?>">
		<?php echo h($err['name']); ?>
	</p>
	<p>
		Email Address<input type="text" name="email" value="<?php echo h($email); ?>">
		<?php echo h($err['email']); ?>
	</p>

<?php
//university data inserting
$dbh = connectDb();

$universities = array();

$sql = "select * from universities";
foreach ($dbh->query($sql) as $row) {
	array_push($universities, $row);
}

?>

<?php
//country data inserting
$dbh = connectDb();

$countries = array();

$sql = "select * from countries";
foreach ($dbh->query($sql) as $row) {
	array_push($countries, $row);
}
?>


	<p>
		Country:
		<select name="countryname">
		<?php foreach ($countries as $country) : ?>
		<option value=""><?php echo h($country['countryname']); ?></option>
		<?php endforeach; ?>
		</select>
	</p>

	<p>
		University:
		<select name="university_id">
		<?php foreach ($universities as $university) : ?>
		<option value="<?php echo h($university['id']); ?>"><?php echo h($university['universityname']); ?></option>
		<?php endforeach; ?>
		</select>
	</p>

	

	<p>
		password<input type="password" name="password" value="">
		<?php echo h($err['password']); ?>
	</p>
	<input type="hidden" name="token" value="<?php echo h($_SESSION['token']); ?>">
	<P><input type="submit" value="new sign up"><a href="index.php">back to home</a></P>

</body>
</html>