<?php
session_start();

//クリックジャッキング対策
header('X-FRAME-OPTIONS: SAMEORIGIN');

$dsn = 'mysql:dbname=さーば;host=localhost';
$user = 'ユーザ名';
$password = 'パスワード';
$dbh = new PDO($dsn,$user,$password);

/*$statement = 'SELECT * FROM member';
$results = $dbh -> query($statement);
foreach($results as $row){
  echo $row['id'];
  echo $row['account'];
  echo $row['mail'];
  echo $row['password'].'<br>';
}//表示された*/

/*$statement = 'SHOW CREATE TABLE member';
$result = $dbh -> query($statement);
foreach($result as $row){
  print_r($row);
}
echo "<hr>";//これはできてる*/


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

//前後にある半角全角スペースを削除する
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
  header("Location:login_form.php");
  exit();
}else{
  //POSTされたデータを各変数に入れる
  $account = $_POST['account'];
  $password = $_POST['password'];


//アカウント入力判定
if($account == ''){
  $errors['account'] = "アカウントが入力されていません。";
}

  //パスワード入力判定
  if($password == ' '){
    $errors['password'] = "パスワードが入力されていません。";
  }else{
    $password_hide = str_repeat('*',strlen($password));
}

//エラーがなければ実行する
if(count($errors) === 0){
  try{
      //例外処理を投げるようにする
      $dbh -> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
      //アカウントで検索
      $statement = $dbh -> prepare("SELECT * FROM member WHERE account = (:account) AND flag = 1");
      $statement -> bindValue(':account',$account,PDO::PARAM_STR);
      $statement -> execute();

      //アカウントが一致
      if($row = $statement -> fetch()){
        $memberPassword = $row['password'];
        //パスワードが一致
        if(strstr($password,$memberPassword) == 0){
          //セッションハイジャック対策
          session_regenerate_id(true);
          $_SESSION['account'] = $account;
          header("Location:date_register.php");
          exit();
         }else{
            $errors['password'] = "アカウントおよびパスワードが一致しません。";
          }
       }else{
            $errors['account'] = "アカウントおよびパスワードが一致しません。";
       }

       //データベース接続切断
       $dbh = null;
   }catch(PDOException $e){
      print('Error:'.$e->getMessage());
      die();
   }
}
}


?>

<!DOCTYPE html>
<html>
<head>
<title>ログイン確認画面</title>
<meta charset = "utf-8">
<link rel="stylesheet" type="text/css" href="http://co-134.99sv-coco.com/login_check_css.css">
</head>

<body>
<h1>ログイン確認画面</h1>

<?php if(count($errors) > 0): ?>

<?php
foreach($errors as $value){
   echo "<p>".$value."</p>";
}
?>

<input type = "button" value = "戻る" onClick = "history.back()" id="btn">

<?php endif; ?>

</body>
</html>
