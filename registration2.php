<?php

session_start();

header("Content-type: text/html; charser = utf-8");

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

//クリックジャッキング対策
header('X-FRAME-OPTIONS: SAMEORIGIN');

//データベース接続
$dbh = db_connect();

//テーブル作成
$statement = "CREATE TABLE pre_member"
."("
."id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,"
."urltoken VARCHAR(128) NOT NULL,"
."mail VARCHAR(50) NOT NULL,"
."date DATETIME NOT NULL,"
."flag TINYINT(1) NOT NULL DEFAULT 0"
.")";
$dbh -> exec($statement);

//エラーメッセージの初期化
$errors = array();

if(empty($_POST)){
  header("Location: registration_mail_form.php");
  exit();
}else{
  //POSTされたデータを変数に入れる
  $mail = $_POST['mail'];

  //メール入力判定
  if($mail == ''){
    $errors['mail'] = "メールアドレスが入力されていません。";
  }else{
    if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $mail)){
      $errors['mail_check'] = "メールアドレスの形式が正しくありません。";
    }
  }
}

if(count($errors) == 0){
  $urltoken = hash('sha256',uniqid(rand(),1));
  $url = "http://さーば/registration_form.php"."?urltoken=".$urltoken;

//データベースに登録
try{
  //例外処理を投げるようにする
  $dbh -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $statement = $dbh -> prepare("INSERT INTO pre_member (urltoken,mail,date) VALUES(:urltoken,:mail,:date)");
  
  //プレースホルダへ実際の値を設定
  date_default_timezone_set('UTC');
  $statement -> bindValue(':urltoken',$urltoken,PDO::PARAM_STR);
  $statement -> bindValue(':mail',$mail,PDO::PARAM_STR);
  $statement -> bindValue(':date',date("Y/m/t G:i:s"),PDO::PARAM_STR);
  $statement -> execute();

  //データベース接続切断
  $dbh = null;

}catch(PDOException $e){
  print('Error:'.$e->getMessage());
  die();
 }

  //メールの宛先
  $mailTo = $mail;

  //Return-Pathに指定するメールアドレス
  $returnMail = 'for_intern@yahoo.co.jp';

  $name = "家庭教師派遣会社";
  $mail = 'for_intern@yahoo.co.jp';
  $subject = "【家庭教師派遣会社】会員登録用URLのお知らせ";

$body = <<< EOM
24時間以内に下記のURLからご登録ください。
{$url}
EOM;

  mb_language('ja');
  mb_internal_encoding('UTF-8');

  //Fromヘッダーを作成
  $header = 'From: ' . mb_encode_mimeheader($name). '<' .$mail. '>';
  
  if(mb_send_mail($mailTo,$subject,$body,$header,'-f'.$returnMail)){
    //セッション変数をすべて解除
    $_SESSION = array();
    
    //クッキーの削除
    if(isset($_COOKIE["PHPSESSID"])){
      setcookie("PHPSESSID",'',time() - 1800, '/');
     }

    //セッションを破棄する
    session_destroy();

    $message = "メールをお送りしました。24時間以内にメールに記載されたURLからご登録ください。";

  }else{
     $errors['mail_error'] = "メールの送信に失敗しました";
   }
}
?>

<!DOCTYPE html>
<html>

<head>
<title>メール認証画面</title>
<meta charset = "utf-8">
<link rel="stylesheet" type="text/css" href="http://さーば/registration_mail_check_css.css">

</head>

<body>

<h1>メール確認画面</h1>

<?php if (count($errors) === 0): ?>

<p><?=$message?></p>

<?php elseif(count($errors) > 0): ?>

<?php
foreach($errors as $value){
  echo "<p>".$value."</p>";
}
?>

<?php endif; ?>

</body>
</html>

