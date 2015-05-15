<?php

session_start();

require_once('config.php');
require_once('function.php');

//ヘッダー設定
$me = Head($_SESSION['me']['username']);

//ユーザーのidを取得
$id = $_GET['id'];

//データベースに接続
$dbh = connectDb();

//ユーザー一覧の取得
$sql= "select * from users where id = :id limit 1";
$stmt = $dbh->prepare($sql);
$stmt->execute(array(":id" => $id));
$user = $stmt->fetch();

//所属大学情報の取得
$sql = "select * from universities where id = :id limit 1";
$stmt = $dbh->prepare($sql);
$stmt->execute(array(":id" => $user['university_id']));
$university = $stmt->fetch();

//そのユーザーのpostsを取得(新しい順)
$posts = array();
$sql = "select * from posts where user_id = $id order by modified desc limit 10";
foreach($dbh->query($sql) as $row){
	array_push($posts,$row);
}

//そのユーザーのreviewsを取得
$reviews = array();
$sql = "select * from reviews where user_id = $id";
foreach($dbh->query($sql) as $row){
	array_push($reviews,$row);
}

//カテゴリーの情報読み込み
$categories = array();
$sql = "select * from categories";
foreach ($dbh->query($sql) as $row) {
	array_push($categories, $row);
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<title><?php echo $user['username']; ?></title>
</head> 
<body>
	<h1><?php echo $user['username']; ?></h1>
	<h2>Study in <?php echo $university['universityname']; ?></h2>
	
	
	<!--reviewの一覧 -->
	<p>Reviews</p>
	<?php foreach ($categories as $category) :?>
		<p><?php echo $category['categoryname']; ?></p>
	<ul>
	<?php foreach ($reviews as $review) :?>
	<?php if ($review['category_id'] == $category['id']) {
	echo "<li>";
	echo $review['body']; 
	}; 
	echo "</a>";
	echo "</li>"; ?>
	<?php endforeach; ?>
</ul>
	<?php endforeach; ?>


	<!--ユーザーによる投稿-->
	<p>Posts</p>
	<ul>
	<?php foreach ($posts as $post) :?>
	<li><a href="edit_post.php?id=<?php echo $post['id'];?>&user=<?php echo $id;?>"><?php echo $post['title']; ?></a></li>
	<?php endforeach; ?>
	
</ul>
</body>
</html>
