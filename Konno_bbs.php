<?php
//sql接続
$dsn = ;
$user = ;
define(PASS,'パスワード');
$pdo = new PDO ($dsn,$user,PASS);


$sql = "CREATE TABLE mybbs"
  ."("
  ."id INT AUTO_INCREMENT NOT NULL PRIMARY KEY,"
  ."name char(43),"
  ."comment TEXT"
  .")";

$stmt = $pdo -> query($sql);

$password = $_POST['pass'];
$table_name = "mybbs";

$name = $_POST['name'];
$comm = $_POST['comment'];
$edi_num = $_POST['edi_num'];
$edi_row = $_POST['edi_row'];
$edi_bool = $_POST['edi_bool'];
if(empty($edi_row)){
  $edi_bool = false;
}

$del_num = $_POST['del_num'];

function check_password($password){
  $bool = false;
  if(empty($password)){
    echo 'パスワードを入力してください<br />';
  }elseif(strpos($password,'パスワード') == true){
     $bool = true;
   }
  return $bool;
}

if(!empty($password)){
    $pdo = new PDO(
      $dsn,$user,$password,array(
         PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
         PDO::ATTR_EMULATE_PREPARES => false));
  }
//edition
   if($edi_bool){
     if(strstr($password,'パスワード')){
       $sql = "UPDATE $table_name SET name = '$name',comment='$comm' WHERE id = $edi_row";
       $result = $pdo -> query($sql);
      }
    }
    if(ctype_digit($edi_num)){
     if(strstr($password,'パスワード')){
       $sql = "SELECT * FROM $table_name";
       $result = $pdo -> query($sql);
       foreach($result as $row){
         if($edi_num == $row['id']){
           $edi_row = $edi_num;
           $edi_bool = true;
           $edi_name = $row['name'];
           $edi_comm = $row['comment'];
         }
       }
      }
    }

//delete
  if(ctype_digit($del_num)){
    if(strstr($password,'パスワード')){
      $sql = "DELETE FROM $table_name WHERE id = $del_num";
      $result = $pdo -> query($sql);
     }
   }

//register
  if(!empty($name) and !empty($comm) and !$edi_bool){
    if(strstr($password,'パスワード')){
      $sql = $pdo -> prepare("INSERT INTO mybbs (id,name,comment) VALUES (:id,:name, :comment)");
      $sql -> bindParam(':name',$name,PDO::PARAM_STR);
      $sql -> bindParam(':comment',$comm,PDO::PARAM_STR);
      $sql -> execute();
      $date = date("Y/m/d H:i");
      echo $name.",".$comm."を受け付けました".$date;
    }
  }


?>

<!DOCTYPE html>
<html>
<body>

<form action = "mission4.php" method = "post">
  名前:<input type = "text" name = "name" value = <?php echo $edi_name; ?>><br/>
  コメント:<input type = "text" name = "comment" value =<?php echo $edi_comm;?>><br/>
  <br/>
  削除対象番号: <input type = "text" name = "del_num" value = ""><br/>
  編集対象番号: <input type = "text" name = "edi_num" value = ""><br/>
  <br/>
 パスワード:<input type = "text" name = "pass" placeholder = "パスワードは必須です"></br>

    <input type = "hidden" name = "edi_row" value = <?php echo $edi_row;?>>
    <input type = "hidden" name = "edi_bool" value = <?php echo $edi_bool;?>>
    <input type = "submit" value = "送信">


</form>

</body>
</html>

<?php
 //データベース削除
  //$sql = 'DELETE FROM '.$table_name;
  //$result = $pdo -> query($sql);
 //データベースの要素追加
 //$sql = "INSERT INTO $table_name VALUES('1','hominyan','kawaii');

 //データベースの要素削除
 //$sql = "DELETE FROM $table_name where id = 3";

 //型を変更，オートインクリメントに
 //$sql = "ALTER TABLE mybbs CHANGE id id INT AUTO_INCREMENT PRIMARY KEY";
 //$result = $pdo -> query($sql);


 //表示
 $sql = "SELECT * FROM $table_name";
 $result = $pdo -> query($sql);
 echo 'データベース内部<br />';
 echo 'id |'.'name |'.'  comment  '.'<br>';
 $db_count = 0;
 foreach($result as $row){
   /*
   print_r($row);
   echo '<br/>';
   */
   echo $row['id'].'|';
   echo $row['name'].'|';
   echo $row['comment'].'<br>';
   $db_count++;
  }

  //投稿番号リセット
  if(!$db_count){
    $sql = "ALTER TABLE $table_name AUTO_INCREMENT = 0";
    $result = $pdo -> query($sql);
  }
?>
 



