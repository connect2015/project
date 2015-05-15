<?php

session_start();

require_once('config.php');
require_once('function.php');

//ヘッダー設定
$me = Head($_SESSION['me']['username']);

//大学のidを取得
$id = $_GET['id'];

//データベースに接続
$dbh = connectDb();

//大学情報の取得
$sql = "select * from universities where id = :id limit 1";
$stmt = $dbh->prepare($sql);
$stmt->execute(array(":id" => $id));
$university = $stmt->fetch();

//ユーザー一覧の取得
$users = array();
$sql = "select * from users where university_id = $id ";
foreach($dbh->query($sql) as $row){
	array_push($users,$row);
}
if(!$users){
	$users = "No users in this university";
}

//ユーザーのpostsを取得(新しい順)
$posts = array();
$sql = "select * from posts where university_id = $id order by modified desc limit 10";
foreach($dbh->query($sql) as $row){
	array_push($posts,$row);
}

//ユーザーのreviewsを取得
$reviews = array();
$sql = "select * from reviews where university_id = $id ";
foreach($dbh->query($sql) as $row){
	array_push($reviews, $row);
}

//ユーザーとreviewの情報をひもづける
$a = array();
foreach($reviews as $review){
	$sql = "select * from users where id =".$review['user_id'];
	$stmt = $dbh->query($sql);
	$b = $stmt->fetch(); //ユーザーの情報のarray
	$row = array_merge($review, $b);
	array_push($a, $row);
}
$reviews = $a;

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
	<title><?php echo $university['universityname']; ?></title>
</head> 
<body>
	<h1><?php echo $university['universityname']; ?></h1>
	
	<!--reviewの一覧 -->
	<p>Reviews</p>
	<?php foreach ($categories as $category) :?>
		<p><?php echo $category['categoryname']; ?></p>
		<ul>
		<?php foreach ($reviews as $review) :?>
			<?php if ($review['category_id'] == $category['id']) {
			echo "<li>";
			echo $review['body']; 
			echo " by ";
			echo "<a href="."user.php?id=".$review['user_id'].">";
			echo $review['username']; 
			echo "</a>";
			echo "</li>"; 
			} ?>
		<?php endforeach; ?>
		</ul>
	<?php endforeach; ?>

	<!--ユーザーによる投稿-->
	<p>Posts</p>
	<ul>
	<?php foreach ($posts as $post) :?>
	<li><a href="<?php echo h(SITE_URL);?>edit_post.php?a=<?php echo h($post['id']);?>&user=<?php echo h($post['user_id']);?>"><?php echo $post['title']; ?></a></li>
	<?php endforeach; ?>
	</ul>

	<!--ユーザー一覧-->
	<p>Users</p>
	<ul>
	<?php foreach ($users as $user) :?>
	<li><a href="<?php echo h(SITE_URL);?>user.php?a=<?php echo h($user['id']);?>"><?php echo $user['username']; ?></a></li>
	<?php endforeach; ?>
	</ul>
</body>
</html>