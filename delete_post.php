<?php

session_start();

require_once('config.php');
require_once('function.php');

//データベースに接続
$dbh = connectDb();

//post_idとuser_idを取得
$id = $_GET['id'];
$user = $_GET['user'];

////大学情報の取得
$sql = "delete from posts where id = ".$id;
$stmt = $dbh->query($sql);

//mypageへリダイレクト
header("Location:".SITE_URL."mypage.php");
exit;
