<?php

session_start();

require_once('config.php');
require_once('function.php');

//CSFR対策
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
	$dbh = connectDb();

	$id = $_GET['id'];
	$user = $_GET['user'];

	//post情報の取得
	$sql1 = "select * from posts where id = :id limit 1";
	$stmt1 = $dbh->prepare($sql1);
	$params = array(":id" => $id);
	$stmt1->execute($params);
	$post = $stmt1->fetch(PDO::FETCH_ASSOC);

	//画像情報の取得
	$sql2 = "select * from images where post_id = :id limit 1";
	$stmt2 = $dbh->prepare($sql2);
	$stmt2->execute($params);
	$image = $stmt2->fetch(PDO::FETCH_ASSOC);

	$title = $post['title'];
	$body = $post['body'];
	$imagename = $image['filename'];

	//画像情報があれば取得
	switch ($image) {
		case '':
			$imgtext = "";
			break;
		
		default:
			$imagepath = "images/".$image['filename'];
			$imgtext = '<p><img src="'.$imagepath.'"></p>';
			break;
	}

	setToken();

} else {
	checkToken();

	$id = $_POST['id'];
	$user = $_POST['user'];
	$title = $_POST['title'];
	$body = $_POST['body'];
	$imagename = $_POST['imagename'];

	//postsテーブルの情報を更新
	$dbh = connectDb();
	$sql = "update posts set title = :title, body = :body where id = :id";
	$stmt = $dbh->prepare($sql);
	$params = array(
		":title" => $title,
		":body" => $body,
		":id" => $id
	);
	$stmt->execute($params);

	//画像情報があれば取得
	switch ($_POST['imagename']) {
		case '':
			$imgtext = "";
			break;
		
		default:
			$imagepath = "images/".$imagename;
			$imgtext = '<p><img src="'.$imagepath.'"></p>';
			break;
	}
	header("Location: mypage.php?id=".$id."&user=".$user);
}

//ヘッダー設定
Head($_SESSION['me']['username']);

?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<title>記事の編集</title>
</head>
<body>

	<h1>記事の編集</h1>
	<form action="" method="POST">
		<p>タイトル：<input type="text" name="title" value="<?php echo h($title); ?>"></p>
		<p>本文：<input type="textarea" name="body" value="<?php echo h($body); ?>"></p>
		<input type="hidden" name="token" value="<?php echo h($_SESSION['token']); ?>">
		<input type="hidden" name="id" value="<?php echo h($id); ?>">
		<input type="hidden" name="user" value="<?php echo h($user); ?>">
		<input type="hidden" name="imagename" value="<?php echo h($imagename); ?>">
		<p><input type="submit" value='変更を保存'></p>
		<?php echo $imgtext; ?>
	</form>
</body>
</html>