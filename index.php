<!-- ログインページ -->
<?php
require("./function.php");

// 自動ログイン
if(isset($_SESSION["login_preservation"]) && $_SESSION["login_preservation"]){
  // ログイン時刻のセッションを更新
  $_SESSION["login_date"] = time();

  if($_SESSION["login_date"] < $_SESSION["login_date_limit"]){
    // ログイン可能時刻のセッションを更新
    $_SESSION["login_date_limit"] = $_SESSION["login_date"] + 30 * 24 * 60 * 60;

      header("Location: ./memo.php");
  }else{
    session_destroy();
  }
}

if(!empty($_POST)){
  $email = $_POST["email"];
  $pass = $_POST["pass"];
  if(isset($_POST["login_preservation"])) $login_preservation = $_POST["login_preservation"];

  // バリデーションチェック
  // emailチェック
  // email未入力チェック
  valid_empty($email,"email");
  // email文字数チェック
  if(empty($err["email"])){
    valid_string_length($email,"email");
  }
  // email形式チェック
  if(empty($err["email"])){
    valid_email_match($email,"email");
  }

  // passチェック
  // pass未入力チェック
  valid_empty($pass,"pass");
  // pass文字数チェック
  if(empty($err["pass"])){
    valid_pass_string_length($pass,"pass");
  }
  // pass半角数字チェック
  if(empty($err["pass"])){
    valid_half_number_match($pass,"pass");
  }

  if(empty($err)){
    try{
      // データベースからidとパスワード取得
      $dbh = db_connect();

      $sql = "SELECT id,pass FROM users WHERE email = :email";
      $data = [":email" => $email];

      $stmt = query_post($dbh,$sql,$data);
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
    }catch(Exception $e){
      $err["other"] =  ERR_MESSAGE_7;
    }

    // パスワード照合
    if(!empty($result) && password_verify($pass,$result["pass"]) === true){
      // ログイン時刻のセッションを発行
      $_SESSION["login_date"] = time();
      // ログイン可能時刻のセッションを発行
      $_SESSION["login_date_limit"] = $_SESSION["login_date"] + 30 * 24 * 60 * 60;
      // 自動ログインをするか判断するセッションを発行
      $_SESSION["login_preservation"] = $login_preservation;
      // ユーザーIDのセッションを発行
      $_SESSION["user_id"] = $result["id"];
      
      header("Location: memo.php");
    }else{
      $err["other"] = ERR_MESSAGE_8;
    }
  }
}

?>

<!DOCTYPE html>
<html lang="ja">
  <!-- ヘッド   -->
  <?php head("ログイン");?>
  
  <body>
    <!-- ヘッダー -->
    <?php require("./header.php");?>

    <!-- メイン -->
    <main>
      <div class="site-width">
        <div class="form_container">
          <h1>ログイン</h1>
          <form action="" method="post">
            <?php if(!empty($err["other"])){echo "<p class='error_message'>" . $err["other"] . "</p>";}?>
            <p>メールアドレス</p>
            <input type="text" spellcheck="false" name="email" value="<?php if(isset($email)){echo $email;}?>">
            <?php if(!empty($err["email"])){echo "<p class='error_message'>" . $err["email"] . "</p>";}?>
            <p class="pass">パスワード</p>
            <input type="text" spellcheck="false" name="pass" value="<?php if(isset($pass)){echo $pass;}?>">
            <?php if(!empty($err["pass"])){echo "<p class='error_message'>" . $err["pass"] . "</p>";}?>
            <div class="login_preservation_check">
              <input type="checkbox" id="login_preservation" name="login_preservation" checked="checked">
              <label for="login_preservation" class="login_preservation">自動でログイン</label>
            </div>
            <input type="submit" class="important_submit" name="submit" value="ログイン">
          </form>
          <p class="no_account">アカウントを作成されていない方は<a href="./account_registration.php">こちら</a></p>
        </div>
      </div>
    </main>

    <!-- フッター -->
    <?php require("./footer.php");?>
  </body>
</html>