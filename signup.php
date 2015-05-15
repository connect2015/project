<?php

require_once('config.php');
require_once('function.php');

session_start();

//国名の選択リストフォームを作成する関数
function list_country($array, $name) {
	$i = 0;
    echo "<select name='". $name ."' onChange='list_university(true);'>";
    echo "<option value='' selected>選択してください</option>";
    while ($i < count($array)){
    	$i++;
        echo "<option value='".$i."' >" . $array[$i-1]["countryname"] . "</option>";
    }
    echo "</select>";
}

//javascriptを読み込む
echo <<<EOM
<script type="text/javascript">
//選択された国名に応じて大学名の選択リストを作成する関数
function list_university(b) {
	document.main.university.length = 1;
    document.main.university.selectedIndex = 0;
    if (document.main.country.selectedIndex !== 0) {
        var university = universities[document.main.country.selectedIndex - 1];
        document.main.university.length = university.length + 1;
        for (var i = 0; i < university.length; i++) {
            document.main.university.options[i + 1].value = i;
            document.main.university.options[i + 1].text = university[i];
        }
    }
}
var universities = new Array();

var data = "<?php echo $data ?>"; //　phpのdataをjavascriptのdataに代入
for ( var i=0; i < data.length ; i++) { 
	var universities[i] = data.split(','); // data（カンマ区切り）から 配列 universities に代入
}

console.log(universities);
</script>
EOM;


if($_SESSION['me']) {
	header("Location: ".SITE_URL);
	exit;
} 

//ヘッダー設定
Head($_SESSION['me']['username']);

if ($_SERVER['REQUEST_METHOD'] != 'POST') {

	//POSTではなかったら
	$name = '';
	$email = '';
	$password = '';
	setToken();
	$dbh = connectDB();

	//countryの情報を取得
	$countries = array();
	$sql = "select * from countries";
	foreach($dbh->query($sql) as $row){
		array_push($countries,$row);
	}

	//universityの情報を取得
	$alluniversity = array();
	$sql = "select * from universities";
	foreach($dbh->query($sql) as $row){
		array_push($alluniversity,$row);
	}
	for ($i=0; $i < count($alluniversity); $i++) { 
		$data[$alluniversity[$i]['country_id']][] =$alluniversity[$i]["universityname"]; 
	}
	ksort($data); //並び替え

	//上記と同じ作業
	/*for ($i=1; $i < count($countries); $i++) {
		for ($j=0; $j < count($alluniversity); $j++) {
			if ($alluniversity[$j]['country_id'] == $i){
				$country[$i][] = $alluniversity[$j]['universityname'];
			}
		}
		var_dump($country[$i]);
	}*/

	//javascript用に配列を文字列に変換
	for ($i=1; $i <= count($data); $i++) {
		$universities[$i] = join(",",$data[$i]); // $universitiesという配列をカンマ区切りで展開して、$country[]に代入
	}


} else {

	//POSTだったら
	checkToken();

	$name = $_POST['name'];
	$email = $_POST['email'];
	$password = $_POST['password'];

	// 選択リストの値を取得
	$country = $_POST['country'];
	//$university = $_POST['university'];

	$dbh = connectDB();

	$err = array();

	//名前が空
	if (!$name){
		$err['name'] = '入力してください';
	}

	//メールアドレスの形式がおかしい
	if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
		$err['email'] = 'メールアドレスが正しくないです';
	}

	//メールアドレスが空
	if (!$email){
		$err['email'] = '入力してください';
	}

	//メールアドレスがすでに登録されてない？
	if(emailExist($email,$dbh)) {
		$err['email'] = 'このメールアドレスは使用されています';
	}

	//パスワードが空
	if (!$password){
		$err['password'] = '入力してください';
	}

	if(empty($err)){
		//登録処理
		<?php

require_once('config.php');
require_once('function.php');

session_start();

//ヘッダー設定
Head($_SESSION['me']['username']);

if($_SERVER['REQUEST_METHOD'] != 'POST'){
	$dbh = connectDb();

	//カテゴリ情報を取得
	$categories = array();
	$sql = "select * from categories";
	foreach($dbh->query($sql) as $new){
		array_push($categories,$new);
	}

	var_dump($categories)

	//レビューを取得
	$sql = "select * from reviews where user_id = ".$_SESSION['me']['id']." and category_id = :category_id";
	$stmt = $dbh->prepare($sql);

	$reviews = array(); 
	foreach($categories as $category){
		$stmt->execute(array(":category_id" => $category['id']));
		$review = $stmt->fetch();
		$review['categoryname'] = $category['categoryname'];
		array_push($reviews, $review);
	}

} else {
	//POSTだったら
	$dbh = connectDb();

	//カテゴリ情報を取得
	$categories = array();
	$sql = "select * from categories";
	foreach($dbh->query($sql) as $new){
		array_push($categories,$new);
	}

//カテゴリー毎で回して、それをUpdateする
foreach ($categories as $category){

$body = $_POST[$category['categoryname']];
$id = $_POST[$category['categoryname']."_id"];

var_dump($category);

if (!$id) {
	$sql = "insert into reviews 
		(user_id, university_id, category_id, body, created, modified)
		values
		(:user_id, :university_id, :category_id, :body now(), now())";
		$stmt = $dbh->prepare($sql);
		$params = array(
			":user_id" => $_SESSION['me']['id'],
			":university_id" => $_SESSION['me']['university_id'],
			":category_id" => $category['id'],
			":body" => $body
			);
		$stmt->execute($params);

		echo ($category['id']);

} else {
	$sql = "update reviews set body = ". $body ." where id = ".$id;
	$stmt = $dbh->query($sql);
}

}


//レビューを取得
	$sql = "select * from reviews where user_id = ".$_SESSION['me']['id']." and category_id = :category_id";
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
	<p>Edit Reviews</p>
	<form action="" method="POST">
	<?php foreach ($reviews as $review) :?>
	<p><?php echo $review['categoryname']; ?></p>
	<p><input type="hidden" name="<?php echo $review['categoryname']; ?>_id" value="<?php echo $review['id'];?>"</p>
	<P><input type="text" name="<?php echo h($review['categoryname'])?>" value="<?php echo h($review['body']); ?>"></P>
	<br>
	<?php endforeach; ?>
	<input type="submit" value="変更を保存">
	</form>
</body>
</html>



		header("Location: ".SITE_URL."login.php");
		exit;
	}
}



?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<title>新規登録</title>
</head>
<body>
	<h1>新規登録</h1>
	<form id="main" action="" method="POST">
		<p>お名前：<input type="text" name="name" value="<?php echo h($name); ?>"><?php echo h($err['name']); ?></p>
		<p>メールアドレス：<input type="text" name="email" value="<?php echo h($email); ?>"><?php echo h($err['email']); ?></p>
		<p>パスワード：<input type="password" name="password" value="<?php echo h($password); ?>"><?php echo h($err['password']); ?></p>
		<p>国名：<?php list_country($countries, 'country'); ?></p>
		<p>大学名：
		<select name="university" onLoad="list_university(false)">
			<option value="">選択してください</option>
			<option value=""></option>
		</select>
		</p>
		<input type="hidden" name="token" value="<?php echo h($_SESSION['token']); ?>">
		<p><input type="submit" value='新規登録'></p>
	</form>
</body>
</html>

