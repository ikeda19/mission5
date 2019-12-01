<!--//mission5-->
<?php
//データベースの接続
$dsn='データベース名' ;
$username='ユーザー名';
$password='パスワード';
$pdo=new PDO($dsn,$username,$password,array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_WARNING));
//テーブル作成
$sql="CREATE TABLE IF NOT EXISTS mission_5"
."("
."id INT AUTO_INCREMENT PRIMARY KEY,"
."name char(32),"
."comment TEXT,"
."post_date TEXT,"
."password char(50)"
.");";
$stmt=$pdo->query($sql);

//削除機能
 if(isset($_POST["delete"])){
   $delete=$_POST["delete"];
   $id=$delete;
   $sql="SELECT password FROM mission_5 WHERE id=$_POST[delete]";
   //該当する番号の情報の取り出し
   $stmt=$pdo->query($sql);
   //実行するやつをセット
   $result=$stmt->fetch();
   //実行して取り出す
   if($result['password']==$_POST["del_password"]){
   //パスワード確認
   $sql='delete from mission_5 where id=:id';
   //idとって削除
   $stmt=$pdo->prepare($sql);
   //削除実行をセット
   $stmt->bindParam(':id',$id,PDO::PARAM_INT);
   //バインド（ユーザーからの情報を入力）
   $stmt->execute();
   //実行
 }
}

//編集番号情報取得機能（送信された編集番号と一致するテキスト部分を取り出しブラウザの投稿欄に表示させる）
if(isset($_POST["Edit"])){
  $edit=$_POST["Edit"];
  $sql='SELECT * FROM mission_5';//mission_5から全て取り出す
  $stmt=$pdo->query($sql);
  $results=$stmt->fetchALL();
  foreach($results as $row){
    if($row['id']==$edit && $row['password']==$_POST["edit_password"]){
    $Edit_name=$row['name'];
    $Edit_comment=$row['comment'];
  }
}
}

//編集機能（表示された状態から、hidden欄の番号と一致するテキスト部分を名前・コメント欄に入力された通りに変えて、テキストに書き込む）
  if(isset($_POST["name"])&&isset($_POST["comment"])&& !empty($_POST["Edit_Number"])){
    $Edit_Number=$_POST["Edit_Number"];
    $Edit_Name=$_POST["name"];
    $Edit_Comment=$_POST["comment"];
    $sql='SELECT * FROM mission_5';//mission_5から全て取り出す
    $stmt=$pdo->query($sql);
    $results=$stmt->fetchALL();
    foreach($results as $row){
      if($row['id']==$Edit_Number){
        $sql='update mission_5 set name=:name,comment=:comment where id=:id';//idに入っている番号を編集
        $stmt=$pdo->prepare($sql);
        $stmt->bindParam(':name',$Edit_Name,PDO::PARAM_STR);
        $stmt->bindParam(':comment',$Edit_Comment,PDO::PARAM_STR);
        $stmt->bindParam(':id',$row['id'],PDO::PARAM_INT);
        $stmt->execute();
      }
    }
}

//データベースへの送信機能
if(isset($_POST["name"])&&isset($_POST["comment"])&& empty($_POST["Edit_Number"])){
  //nameとcommentの情報がセットされていて、かつEdit_Numberが空の時
  if($_POST["comment"]!=null){
    $name=$_POST["name"];
    $comment=$_POST["comment"];
    $Post_Date=date("Y/m/d H:i:s");
    $password=$_POST["password"];
    //それぞれのデータを取得
    $sql=$pdo->prepare("INSERT INTO mission_5(id,name,comment,post_date,password) VALUES(:id,:name,:comment,:post_date,:password)");
    $sql->bindParam(':id',$id,PDO::PARAM_INT);
    $sql->bindParam(':name',$name,PDO::PARAM_STR);//nameテーブルに文字列をinsert
    $sql->bindParam(':comment',$comment,PDO::PARAM_STR);//commentテーブルに文字列をinsert
    $sql->bindParam(':post_date',$Post_Date,PDO::PARAM_STR);//post_dateテーブルに文字列をinsert
    $sql->bindParam(':password',$password,PDO::PARAM_STR);//passwordテーブルに文字列をinsert
    $sql->execute();
}
}
?>

<!DOCTYPE html>
<html>
  <meta charset="utf-8">
  
  <h2>入力フォーム</h2>
  <form action="" method="post">
  
    名前：<input type="text" name="name" value="<?php if(isset($Edit_name)){echo $Edit_name;} else{echo "名前";}?>"><br>
    
    コメント：<input type="text" name="comment" value="<?php if(isset($Edit_comment)){echo $Edit_comment;} else{echo "コメント";}?>"><br>
    
    パスワード：<input type="text" name="password" value="パスワード"><br>
    <input type="hidden"  name="Edit_Number" value="<?php if(isset($edit)){echo $edit;} else{echo null;}?>"><br>
    <input type="submit" value="送信">
  </form>
  <h2>削除フォーム</h2>
  <font color ="blue">※半角でお願いします。</font><br>
  <form action="" method="post">
      削除対象番号：<input type="text" name="delete" ><br>
      
      パスワード：<input type="text" name="del_password" ><br>
     <input type="submit" value="削除">
  </form>
  <h2>編集フォーム</h2>
  <font color ="blue">※半角でお願いします。</font><br>
  <form action="" method="post">
     編集対象番号： <input type="text" name="Edit" ><br>
     
     パスワード：<input type="text" name="edit_password" ><br>
     <input type="submit" value="編集">
</form>
</html>

<?php
//表示機能
echo "__________________掲示板欄______________________<br>";
$sql='SELECT * FROM mission_5';//mission_5から全て取り出す
$stmt=$pdo->query($sql);
$results=$stmt->fetchALL();
foreach($results as $row){
  echo $row['id'].',';
  echo $row['name'].',';
  echo $row['comment'].',';
  echo $row['post_date'];
  echo "<hr>";
}

?>
