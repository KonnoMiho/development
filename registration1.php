﻿<?php
session_start();

header("Content-type:text/html;charset=utf-8");

//クリックジャッキング対策
header('X-FRAME-OPTIONS:SAMEORIGIN');

//前後にある半角全角スペースを削除する関数
function spaceTrim($str){
  //行頭
  $str = preg_replace('/^[  ]+/u','',$str);
  //末尾
  $str = preg_replace('/[  ]+$/u','',$str);
  return $str;
}

//エラーメッセージの初期化
$errors = array();

if(empty($_POST)){
  header("Location:registration_form.php");
  exit();
}else{
  //POSTされたデータを各変数に入れる
  $account = $_POST['account'];
  $password = isset($_POST['password']) ? $_POST['password'] :NULL;

  //前後にある半角全角スペースを削除
  $account = spaceTrim($account);
  $password = spaceTrim($password);

  //アカウント入力判定
  if($account == ''){
    $errors['account'] = "アカウントが入力されていません。";
   }elseif(mb_strlen($account)>10){
    $errors['account_length'] = "アカウントは10文字以内で入力してください";
   }
    
  //パスワード入力判定
  if($password == ''){
    $errors['password'] = "パスワードが入力されていません。";
}elseif(!preg_match('/^[0-9a-zA-Z]{5,30}$/',$_POST["password"])){
    $errors['password_length'] = "パスワードは半角英数字の5文字以上30文字以下で入力してください。";
}else{
    $password_hide = str_repeat('*',strlen($password));
}


//エラーがなければセッションに登録
if(count($errors) === 0){
  $_SESSION['account'] = $account;
  $_SESSION['password'] = $password;
}
}
?>

<!DOCTYPE html>
<html>

<head>
<title>会員登録確認画面</title>
<meta charset = "utf-8">
<link rel="stylesheet" type="text/css" href="http://さーば/registration_check_css.css">
</head>

<body>

<center><h1>会員登録確認画面</h1></center>

<?php if(count($errors) === 0): ?>

<form action = "registration_insert.php" method = "post">
<center>
<p>メールアドレス: <?=htmlspecialchars($_SESSION['mail'],ENT_QUOTES)?></p>
<p>アカウント名:<?=htmlspecialchars($account,ENT_QUOTES)?></p>
<p>パスワード:<?=$password_hide?></p>

<input type = "button" value = "戻る" onClick = "history.back()" class = "btn">
<input type = "hidden" name = "token" value = "<?=$_POST['token']?>">
<input type = "submit" value = "登録する" class = "btn" name = "register">
</center>
</form>

<?php elseif(count($errors) > 0): ?>

<?php
foreach($errors as $value){
  echo "<p>".$value."</p>";
}
?>

<?php endif; ?>

</body>
</html>
