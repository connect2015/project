<?php

session_start();

require_once('config.php');
require_once('function.php');

//ヘッダー設定////
$me = Head($_SESSION['me']['username']);

$dbh = connectDb();
$id = $me['id'];

//カテゴリ情報を取得
$categories = array();
$sql = "select * from categories";
foreach($dbh->query($sql) as $new){
	array_push($categories,$new);
}

//Post情報を取得
$posts = array();
$sql = "select * from posts where user_id = ".$id." order by modified desc limit 10";
foreach($dbh->query($sql) as $row){
	array_push($posts,$row);
}


//POSTじゃなかったら（つまり最初に開いたとき）
if($_SERVER['REQUEST_METHOD'] != 'POST'){
	//レビューを取得
	$sql = "select * from reviews where user_id = ".$id." and category_id = :category_id";
	$stmt = $dbh->prepare($sql);

	//レビューにカテゴリーの名前をくっつける
	$reviews = array(); 
	foreach($categories as $category){
		$stmt->execute(array(":category_id" => $category['id']));
		$review = $stmt->fetch();
		$review['categoryname'] = $category['categoryname'];
		array_push($reviews, $review);
	}

} else {
	//POSTだったら
//カテゴリー毎で回して、データベースをUpdateする
foreach ($categories as $category){

$body = $_POST[$category['categoryname']];
$id = $_POST[$category['categoryname']."_id"];
$score = $_POST[$category['categoryname']."_score"];

if ($id=="") {
	$sql = "insert into reviews 
		(user_id, university_id, category_id, body, score, created, modified)
		values
		(:user_id, :university_id, :category_id, :body, :score, now(), now())";
		$stmt = $dbh->prepare($sql);
		$params = array(
			":user_id" => $me['id'],
			":university_id" => $me['university_id'],
			":category_id" => $category['id'],
			":body" => $body,
			":score" => $score
			);
		$stmt->execute($params);
} else {
	$sql = "update reviews set body = :body, score =:score where id = :id";
	$stmt = $dbh->prepare($sql);
	$params = array(
		":body" => $body,
		":id" => $id,
		":score"=> $score
		);

	$stmt->execute($params);
}

}

//レビューを取得
	$sql = "select * from reviews where user_id = ".$me['id']." and category_id = :category_id";
	$stmt = $dbh->prepare($sql);

	$reviews = array(); 
	foreach($categories as $category){
		$stmt->execute(array(":category_id" => $category['id']));
		$review = $stmt->fetch();
		$review['categoryname'] = $category['categoryname'];
		array_push($reviews, $review);
}
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<title>Mypage</title>
</head>
<body>
<h1>Mypage</h1>

	<!--Reviews-->
	<p>Edit Reviews</p>
	<form action="" method="POST">
	<?php foreach ($reviews as $review) :?>
	<p><?php echo $review['categoryname']; ?></p>
	<p><input type="hidden" name="<?php echo $review['categoryname']; ?>_id" value="<?php echo $review['id'];?>"></p>
	<P><input type="text" name="<?php echo h($review['categoryname']); ?>" value="<?php echo h($review['body']); ?>"></P>
	<p>Score about this category
	<select name="<?php echo $review['categoryname']; ?>_score">
		<?php for ($i=1; $i<6 ; $i++) { 
		if($i == $review['score']){
			echo "<option value=".$i. " selected ".">".$i."</option>";
		}else{
			echo "<option value=".$i.">".$i."</option>";
		}
		} ;?>
	</select>	
	</P>
	<br>


	<?php endforeach; ?>
	<input type="submit" value="変更を保存">
	</form>

	<!--ユーザーによる投稿-->
	<p>Posts</p>
	
	<?php foreach ($posts as $post) :?>

<h2>Title:<?php echo $post['title']; ?></h2>
<p>
Body:<?php echo $post['body']; ?>
<br>
<a href="<?php echo h(SITE_URL);?>edit_post.php?a=<?php echo h($post['id']);?>&user=<?php echo h($post['user_id']);?>">
edit
</a>　
<a href="<?php echo h(SITE_URL);?>delete_post.php?a=<?php echo h($post['id']);?>&user=<?php echo h($post['user_id']);?>">
delete
</a>
</p>	
	<?php endforeach; ?>
	
	</body>
</html>





