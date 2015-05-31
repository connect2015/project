<?php

require_once('config.php');
require_once('function.php');

session_start();

//ヘッダー設定
Head($_SESSION['me']['username']);

//大学のidを取得
$id = $_GET['id'];

//データベースに接続
$dbh = connectDb();

//大学情報の取得
$sql['university'] = "select * from universities where id = :id limit 1";
$stmt = $dbh->prepare($sql['university']);
$stmt->execute(array(":id" => $id));
$university = $stmt->fetch();

//ユーザー一覧の取得
$users = array();
$sql = "select * from users where university_id = $id";
foreach($dbh->query($sql) as $row){
	array_push($users,$row);
}
if(!$users){
	$users = "No users in this university";
}

//写真の取得
$images = array();
$sql = "select * from images where university_id = ".$id;
foreach($dbh->query($sql) as $row){
	array_push($images, $row);
}

//ユーザーのpostsを取得(新しい順)
$posts = array();
$sql = "select * from posts where university_id = $id order by modified desc limit 10";
foreach($dbh->query($sql) as $row){
	array_push($posts,$row);
}

//ユーザーのreviewsを取得
$reviews = array();
$sql = "select * from reviews where university_id = $id";
foreach($dbh->query($sql) as $row){
	array_push($reviews,$row);
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


//平均点の算出
$averages = array();
foreach ($categories as $category) :
$sum = 0;
$scorenumber = 0;
foreach ($reviews as $review):
if ($review['category_id'] == $category['id']){
$sum = $sum + $review['score'];
$scorenumber = $scorenumber + 1;
}
endforeach;
$average = $sum / $scorenumber;
$newaverage = array($category['categoryname']=>$average);

$averages = array_merge($averages,$newaverage);
endforeach;	

?>






<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<title><?php echo $university['universityname']; ?></title>

	<script src="https://www.google.com/jsapi"></script>
<script>
    google.load('visualization', '1.0', {'packages':['corechart']});
    google.setOnLoadCallback(drawChart);
    
    function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'カテゴリー');
        data.addColumn('number', 'スコア');
        
        <?php foreach ($categories as $category) :?>
		 data.addRows([
            ['<?php echo $category['categoryname'];?>', <?php echo $averages[$category['categoryname']];?>]
        ]);
		
        <?php endforeach;?>
        // グラフのオプションを指定する
        var options = {
            title: 'ゲント大学スコア',
            width: 500,
            height: 500
        };

        // 描画する
        var chart = new google.visualization.ColumnChart(document.getElementById('chart'));
        chart.draw(data, options);
    }

</script>




</head> 
<body>
	<h1><?php echo $university['universityname']; ?></h1>

	<div id="chart"></div>

	<?php foreach ($images as $image) :?>
	<img src ="images/<?php echo $image['filename']; ?>" >
	<br>
	<?php endforeach; ?>

	<!--reviewの一覧 -->
	<p>Reviews</p>
	<?php foreach ($categories as $category) :?>
		<p>
			<h2><?php echo $category['categoryname']; ?></h2>
			<!--Scoreの平均点表示 -->
			<?php echo "  Average score of this category is ".$averages[$category['categoryname']]; ?>
		</p>
	<ul>
	<?php foreach ($reviews as $review) :?>
	<?php if ($review['category_id'] == $category['id']) {
	echo "<li>";
	echo $review['body'];
	echo "<br>";
	echo "Review Score: ".$review['score']; 
	echo " by ";
	echo "<a href="."user.php?id=".$review['user_id'].">";
	echo $review['username']; 
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
	<li><a href="edit_post.php?id=<?php echo $post['id'];?>&user=<?php echo $post['user_id'];?>"><?php echo $post['title']; ?></a></li>
	<?php endforeach; ?>
	</ul>

	<!--ユーザー一覧-->
	<p>Users</p>
	<ul>
	<?php foreach ($users as $user) :?>
	<li><a href="user.php?id=<?php echo h($user['id']);?>"><?php echo $user['username']; ?></a></li>
	<?php endforeach; ?>
	</ul>


</body>
</html>
