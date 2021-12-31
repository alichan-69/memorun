<!-- メモページ -->
<?php
require("./function.php");

// ログイン認証
login_auth();

$user_id = $_SESSION["user_id"];

// メモの登録
if(isset($_POST["register"])){
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

// メモの更新
require("./update_memo.php");

// メモの削除
require("./delete_memo.php");

// ページネーション処理
// urlから現在のページを取得
$current_page_num = (!empty($_GET["p"])) ? $_GET["p"] : 1;
// 表示件数
$list_span = 6;
// 現在の表示メモの先頭を算出
$current_min_num = (($current_page_num - 1) * $list_span);
// DBからページの総数と各ページで表示するメモを取得
try{
  $dbh = db_connect();

  // ページの総数を取得
  // 表示するメモの総数を取得
  $sql = "SELECT id,memo,create_date FROM memos WHERE user_id = :user_id AND delete_flg = :delete_flg";
  // 検索が使用されていた時は検索ワードをセッションに格納する
  if(isset($_POST["search_word"])) $_SESSION["search_word"] = $_POST["search_word"];
  // 全て表示が使用されていた場合は検索ワードのセッションを空にする
  if(isset($_POST["display_all"])) $_SESSION["search"] = false; 
  // 検索が使用されていた時はlike文を加えてメモを絞る
  if(isset($_SESSION["search_word"]) && $_SESSION["search_word"]){
    $sql .= " AND memo LIKE :search_word";
    $data = [":user_id" => $user_id,":delete_flg" => false,":search_word" => "%" . $_SESSION["search_word"] . "%"];
  }else{
    $data = [":user_id" => $user_id,":delete_flg" => false];
  }
  $sql .= " ORDER BY create_date DESC";

  $stmt = query_post($dbh,$sql,$data);
  $total_num = $stmt->rowCount();

  // 表示するメモの総数からページの総数を取得;
  $total_page = ceil($total_num / $list_span); 

  // 各ページで表示するメモの取得
  $sql .= " LIMIT " . $list_span . " OFFSET " . $current_min_num;

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
      <div class="modal"><p><?= get_session_flash("register_memo");?><?= get_session_flash("update_memo");?><?= get_session_flash("delete_memo");?></p></div>
      <div class="site-memo-width">
        <div class="search_container">
          <h2>メモを検索</h2>
          <form method="post" action="./memo.php">
            <input name="search_word" type="text" spellcheck="false">
            <div  class="search_submit_container">
              <input type="submit" name="search" value="検索">
              <input type="submit" name="display_all" value="全て表示">
            </div>
          </form>
        </div>
        <div class="textarea_container">
          <h1>メモ</h1>
          <form method="post" action="./memo.php" >
            <!-- テキストエリアはタグ内の文字列をそのまま表示するため改行はいれないこと -->
            <textarea name="memo" spellcheck="false" cols="50" rows="10"></textarea>
            <p class="counter"><span class="counter_num">0</span>/255</p>
            <!-- メモが更新された時にもエラーメッセージが出るのでpostされたsubmitのnameのよってエラーメッセージを出すか場合分けする -->
            <?php if(!empty($err["memo"]) && isset($_POST["register"])){echo "<p class='error_message'>" . $err["memo"] . "</p>";}?>
            <?php if(!empty($err["other"])  && isset($_POST["register"])){echo "<p class='error_message'>" . $err["other"] . "</p>";}?>
            <input type="submit" class="important_submit register_memo" name="register" value="メモる">
          </form>
        </div>
        <ul class="memos">
          <div class="memos_container">
            <?php foreach($results as $result):?>
            <li>
              <form method="post" action="./memo.php?id=<?=$result['id']; ?>&p=<?= $current_page_num;?>">
                <div class="header_memo">
                  <input type="submit" name="update" value="更新" class="update_memo">
                  <input type="submit" name="delete" value="削除" class="delete_memo">
                  <span><?=$result["create_date"] ?></span>
                </div>
                <textarea class="memo_main" name="update_memo" spellcheck="false"><?=sanitize($result["memo"]); ?></textarea>
                <p class="counter"><span class="counter_num">0</span>/255</p>
              </form>
              <!-- メモが登録された時、他のメモが更新された時にもエラーメッセージが出るのでpostされたsubmitのnameとurlで送信されたメモのidによって
              各エラーメッセージを出すか場合分けする -->
              <?php if(!empty($err["memo"]) && $_GET["id"] === $result['id']){echo "<p class='error_message'>" . $err["memo"] . "</p>";}?>
              <?php if(!empty($err["other"])  && $_GET["id"] === $result['id']){echo "<p class='error_message'>" . $err["other"] . "</p>";}?>
            </li>
            <?php endforeach?>
          </div>
        </ul>
        <?php pagenation($current_page_num,$total_page)?>
      </div>
    </main>

    <!-- フッター -->
    <?php require("./footer.php"); ?>
    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="js/main.js"></script>
  </body>
</html>