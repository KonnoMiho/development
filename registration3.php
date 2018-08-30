<?php
session_start();

header("Content-type:text/html;charset=utf-8");

$dsn = 'mysql:dbname=さーば;host=localhost';
$user = 'ユーザ名';
$password = 'パスワード';

//クリックジャッキング対策
header('X-FRAME-OPTIONS:SAMEORIGIN');

//データベース接続
function db_connect(){
  $dsn = 'mysql:dbname=さーば;host=localhost';
  $user = 'ユーザ名';
  $password = 'パスワード';
   try{
     $dbh = new PDO($dsn,$user,$password);
     return $dbh;
  }catch(PDOException $e){
     print('Error:'.$e->getMessage());
     die();
   }
}

$dbh = db_connect();

// signupがPOSTされたときに下記を実行
if(isset($_POST['register'])){
  $account = $_SESSION['account'];
  $mail = $_SESSION['mail'];
  $password = $_SESSION['password'];

  // POSTされた情報をDBに格納する 
  $query = $dbh -> prepare("INSERT INTO member(account,mail,password) VALUES(:account,:mail,:password)");
  $query -> bindValue(':account', $account, PDO::PARAM_STR);
  $query -> bindValue(':mail', $mail, PDO::PARAM_STR); 
  $query -> bindValue(':password', $password, PDO::PARAM_STR);
  $query -> execute();
}
?>

<!DOCTYPE html>
<html>

<head>
<link rel="stylesheet" type="text/css" href="http://さーば/registration_insert_css.css">
</head>

<body>
<h3>会員登録が完了しました。ログイン画面からどうぞ。</h3>
</body>

</html>
