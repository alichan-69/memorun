<!-- メモページ -->
<?php
require("./function.php");

// ログイン認証
login_auth();

$user_id = $_SESSION["user_id"];
// メモの生成
if(!empty($_POST)){
  $memo = $_POST["memo"];

  // バリデーションチェック
  // メモの文字数チェック
  valid_string_length($memo,"memo");

  if(empty($err)){
    try{
      // メモをデータベースに登録
      $dbh = db_connect();
  
      $sql = "INSERT INTO memos (user_id,memo,create_date,update_date) VALUE (:user_id,:memo,:create_date,:update_date)";
      $data = [":user_id" => $user_id,":memo" => $memo,":create_date" => date("Y-m-d H-i-s"),"update_date" => date("Y-m-d H-i-s")];

      $stmt = query_post($dbh,$sql,$data);

      // メモの登録成功時にでるモーダルのメッセージを発行
      if($stmt) $_SESSION["register_memo"] = SUC_MESSAGE_1;
    }catch(Exception $e){
      $err["other"] =  ERR_MESSAGE_7;
    }
  }
}

// ページネーション処理
// urlから現在のページを取得
$current_page_num = (!empty($_GET["p"])) ? $_GET["p"] : 1;
// 表示件数
$list_span = 6;
// 現在の表示メモの先頭を算出
$current_min_num = (($current_page_num - 1) * $list_span);
// DBからメモの総数と現在表示するメモを取得
try{
  $dbh = db_connect();

  // メモの総数を取得
  $sql = "SELECT id,memo,update_date FROM memos WHERE user_id = :user_id AND delete_flg = :delete_flg ORDER BY update_date DESC";
  $data = [":user_id" => $user_id,":delete_flg" => false];

  $stmt = query_post($dbh,$sql,$data);
  $total_num = $stmt->rowCount();
  // メモの総数からページの総数を取得;
  $total_page = ceil($total_num / $list_span); 

  // 現在表示するメモを取得
  $sql .= " LIMIT " . $list_span . " OFFSET " . $current_min_num;
  $data = [":user_id" => $user_id,":delete_flg" => false];

  $stmt = query_post($dbh,$sql,$data);

  $i = 0;
  $results = [];

  while($record = $stmt->fetch(PDO::FETCH_ASSOC)){
    $results[$i] = $record;
    $i++; 
  }
}catch(Exception $e){
  $err["other"] =  ERR_MESSAGE_7;
}
?>

<!DOCTYPE html>
<html lang="ja">
  <!-- ヘッド   -->
  <?php head("メモ");?>

  <body>
    <!-- ヘッダー -->
    <?php require("./header.php"); ?>

    <!-- メイン -->
    <main>
      <div class="modal"><p><?= get_session_flash("register_memo");?><?= get_session_flash("delete_memo");?></p></div>
      <div class="site-width">
        <div class="textarea_container">
          <h1>メモ</h1>
          <form action="" method="post">
            <?php if(!empty($err["other"])){echo "<p class='error_message'>" . $err["other"] . "</p>";}?>
            <textarea name="memo" cols="50" rows="10"></textarea>
            <p class="counter"><span class="counter_num">0</span>/255</p>
            <?php if(!empty($err["memo"])){echo "<p class='error_message'>" . $err["memo"] . "</p>";}?>
            <input type="submit" class="register_memo" name="submit" value="登録">
          </form>
        </div>
        <ul class="memos">
          <div class="memos_container">
            <?php foreach($results as $result):?>
            <li>
              <div class="header_memo">
                <a class="update_memo" href="./update_memo.php?id=<?=$result['id']; ?>">更新</a>
                <a class="delete_memo" href="./delete_memo.php?id=<?=$result['id']; ?>">削除</a>                  
                <span><?=$result["update_date"] ?></span>
              </div>
              <div class="memo_main">
                <p><?=sanitize($result["memo"]); ?></p>
              </div>
            </li>
            <?php endforeach?>
          </div>
        </ul>
        <?php pagenation($current_page_num,$total_page)?>
        <a class="logout" href="./logout.php">ログアウト</a>
      </div>
    </main>

    <!-- フッター -->
    <?php require("./footer.php"); ?>
    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="js/main.js"></script>
  </body>
</html>