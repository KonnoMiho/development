<!DOCTYPE html>
<h1 class = "title">指導日確認ページ</h1>

</html>
<head>
<link rel="stylesheet" type="text/css" href="http://さーば/style.css">
</head>
<?php
session_start();

header("Content-type: text/html; charset=utf-8");

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

$statement = "CREATE TABLE instructionDate"
  ."("
  ."id INT AUTO_INCREMENT NOT NULL PRIMARY KEY,"
  ."date varchar (80)"
  .")";

$dbh -> exec($statement);

$table_name = "instructionDate";
$date = $_POST["date"];
$id = NULL;

/*$statement = 'SHOW TABLES';
$result = $dbh -> query($statement);
foreach($result as $row){
  echo $row[0];
  echo '<br>';
}
echo "<hr>";*/

/*$sql = 'SHOW CREATE TABLE member';
$result = $pdo -> query($sql);
foreach($result as $row){
  print_r($row);
}
echo "<hr>";*/

if(isset($date) && !empty($date)){
  $statement = $dbh -> prepare("INSERT INTO instructionDate (id,date) VALUES (:id,:date)");
  $statement -> bindParam(':id',$id,PDO::PARAM_STR);
  $statement -> bindParam(':date',$date,PDO::PARAM_STR);
  $statement -> execute();


//表示
 function make_html() {
  echo '<div><input type = "checkbox"></div>';
}

  $statement = "SELECT * FROM $table_name";
  $result = $dbh -> query($statement);
  $db_count = 0;

  foreach($result as $row){
    echo $row['id'].'|';
    echo $row['date'];
    make_html().'<br>';
    $db_count++;
  }
}else{
  echo "指導日程を入力してください";
}

?>
