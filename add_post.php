<?php	

session_start();

require_once('config.php');
require_once('function.php');

if(!$_SESSION['me']) {
	//$_SESSION['guest'] = array('add' =>array('message'=>"ログインしてください"),'lastpage'=> "add.php");
	header("Location:".SITE_URL."login.php");
	exit;
}

//CSFR対策
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
	$title = '';
	$body = '';
	setToken();

} else {
	checkToken();

	$title = $_POST['title'];
	$body = $_POST['body'];
	$image = $_FILES['image'];

	if($image['error'] != 0) {
		echo 'エラーが発生しました。投稿は完了していません！　エラーコード'.$image['error'];
		exit;
	}


	if(!filesize($image['tmp_name']) || filesize($image['tmp_name']) > MAX_SIZE){
		echo 'ファイルサイズが大きすぎです。投稿は完了していません！';
		exit;
	}

	//拡張子の抽出
	$imagesize = getimagesize($image['tmp_name']);
	switch ($imagesize['mime']) {
		case "image/png":
			$ext = 'png';
			break;

		case 'image/jpeg':
			$ext = 'jpg';
			break;

		case 'image/gif':
			$ext = 'gif';
			break;

		default:
			echo 'このファイルはアップロードできません!';
			exit;
	}

	//保存場所・保存名を作成
	$imageFileName = sha1(time().mt_rand()).$ext;
	$imageFIlePath = IMAGES_DIR."/".$imageFileName;

	//写真の保存
	$rs = move_uploaded_file($image['tmp_name'], $imageFIlePath);
	if(!$rs){
		echo 'アップロードできませんでした…';
		exit;
	}
	echo '投稿が完了しました！';

	//postsテーブルに情報を保存
	$dbh = connectDb();
	$sql = "insert into posts 
			(user_id, university_id, title, body, created, modified)
			values
			(:user_id, :university_id, :title, :body, now(), now())";
	$stmt = $dbh->prepare($sql);
	$params = array(
		":user_id" => $_SESSION['me']['id'],
		":university_id" => $_SESSION['me']['university_id'],
		":title" => $title,
		":body" => $body
		);
	$stmt->execute($params);

	//保存されているか確認
	if(!$id = $dbh->lastInsertId()){
		echo '投稿に失敗しました!';
		exit;
	}

	$sql = "insert into images 
			(post_id, filename, filepath, uploaded)
			values
			(:post_id, :filename, :filepath, now())";
	$stmt = $dbh->prepare($sql);
	$params = array(
		":post_id" => $id,
		":filename" => $imageFileName,
		":filepath" => $imageFIlePath
		);
	$stmt->execute($params);

	echo '<p>投稿が完了しました！</p>';
}
//ヘッダー設定
Head($_SESSION['me']['username']);

?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<title>投稿</title>
</head>
<body>

	<h1>投稿</h1>
	<form action="" method="POST" enctype="multipart/form-data">
		<p>タイトル：<input type="text" name="title" value="<?php echo h($title); ?>"><?php echo h($err['title']); ?></p>
		<p>本文：<input type="textarea" name="body" value="<?php echo h($body); ?>"><?php echo h($err['body']); ?></p>
		<p>ファイル：<input type="file" name="image"></p>
		<input type="hidden" name="token" value="<?php echo h($_SESSION['token']); ?>">
		<p><input type="submit" value='投稿'></p>
	</form>
</body>
</html>


