<?php

session_start();

require_once('config.php');
require_once('function.php');

//ヘッダー設定
$me = Head($_SESSION['me']['name']);

//データベースに接続
$dbh = connectDb();

//universityの情報を取得
$universities = array();
$sql = "select * from universities";
foreach($dbh->query($sql) as $row){
	array_push($universities,$row);
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<title>Top</title>
</head>
<body>
	<h1>Connect Top</h1>
	<p>Select University</p>
	<ul>
	<?php foreach ($universities as $university) :?>
	<li>
	<a href="university.php?id=<?php echo $university['id'];?>">
	<?php echo $university['universityname']; ?></a>
	</li>
	<?php endforeach; ?>
</ul>
</body>
</html>

