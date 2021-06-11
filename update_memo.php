<!-- メモ更新ページ -->
<?php
require("./function.php");

// ログイン認証
login_auth();

$user_id = $_SESSION["user_id"];
$memo_id = $_GET["id"];

// データベースのメモのuser_idを取得
try{
    $dbh = db_connect();

    $sql = "SELECT user_id FROM memos WHERE id = :memo_id AND delete_flg = :delete_flg";
    $data = [":memo_id" => $memo_id,":delete_flg" => false];

    $stmt = query_post($dbh,$sql,$data);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
}catch(Exception $e){
    $err["other"] =  ERR_MESSAGE_7;
}

// データベースのメモのuser_idがセッションのuser_idと一致するか確認する
if($result["user_id"] != $_SESSION["user_id"]){
    session_destroy();

    header("Location: ./index.php");
}

if(!empty($_POST)){
    $memo = $_POST["memo"];
  
    // バリデーションチェック
    // メモの文字数チェック
    valid_string_length($memo,"memo");
  
    if(empty($err)){
        try{
            // メモをデータベースで更新
            $dbh = db_connect();

            $sql = "UPDATE memos SET memo = :memo,update_date = :update_date WHERE id = :memo_id AND delete_flg = :delete_flg";
            $data = [":memo" => $memo,"update_date" => date("Y-m-d H-i-s"),":memo_id" => $memo_id,":delete_flg" => false];

            $stmt = query_post($dbh,$sql,$data);

            // メモの更新成功時にでるモーダルのメッセージを発行
            if($stmt) $_SESSION["update_memo"] = SUC_MESSAGE_2;
        }catch(Exception $e){
            $err["other"] =  ERR_MESSAGE_7;
        }
    }
}

// データベースのメモ取得
try{
    $dbh = db_connect();

    $sql = "SELECT memo FROM memos WHERE id = :memo_id AND delete_flg = :delete_flg";
    $data = [":memo_id" => $memo_id,":delete_flg" => false];

    $stmt = query_post($dbh,$sql,$data);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
}catch(Exception $e){
    $err["other"] =  ERR_MESSAGE_7;
}
?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <title>メモ更新</title>
    <link rel="icon" href="favicon.ico">
    <link rel="stylesheet" href="css/styles.css">
  </head>
  <body>
    <!-- ヘッダー -->
    <?php require("./header.php"); ?>

    <!-- メイン -->
    <main>
        <div class="modal"><p><?= get_session_flash("update_memo");?></p></div>
        <div class="site-width">
            <div class="textarea_container">
                <h1>更新</h1>
                <form action="" method="post">
                    <p><?php if(!empty($err["other"])){echo "<p class='error_message'>" . $err["other"] . "</p>";}?></p>
                    <textarea name="memo" cols="50" rows="10"><?= sanitize($result['memo'])?></textarea><br>
                    <p class="counter"><span class="counter_num">0</span>/255</p>
                    <p><?php if(!empty($err["memo"])){echo "<p class='error_message'>" . $err["memo"] . "</p>";}?></p>
                    <input class="update" type="submit" name="submit" value="更新"><br>
                </form>
                <a class="return_memo" href="./memo.php">戻る</a>
            </div>
        </div>
    </main>
    
    <!-- フッター -->
    <?php require("./footer.php"); ?>
    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="js/main.js"></script>
  </body>
</html>