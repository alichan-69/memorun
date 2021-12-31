<!-- アカウント登録ページ -->
<?php
require("function.php");

if(!empty($_POST)){
  $email = $_POST["email"];
  $pass = $_POST["pass"];
  $pass_re = $_POST["pass_re"];

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

  // 再入力パスワードチェック
  // pass_reがpassと同値かチェック
  valid_equal_value($pass,$pass_re,"pass_re");

  
  if(empty($err)){
    try{
      // データベースにアカウント情報を挿入
      $dbh = db_connect();

      $sql = "INSERT INTO users (email,pass,create_date) VALUE (:email,:pass,:create_date)";
      $data = [":email" => $email,":pass" => password_hash($pass, PASSWORD_DEFAULT),":create_date" => date("Y-m-d H-i-s")];

      query_post($dbh,$sql,$data);

      // アカウント情報挿入成功時の処理
      // データベースからアカウントのidを取得
      $sql = "SELECT id FROM users WHERE email = :email";
      $data = [":email" => $email];

      $stmt = query_post($dbh,$sql,$data);
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      
      // ログイン時刻のセッションを発行
      $_SESSION["login_date"] = time();
      // ログイン可能時刻のセッションを発行
      $_SESSION["login_date_limit"] = $_SESSION["login_date"] + 30 * 24 * 60 * 60;
      // ユーザーIDのセッションを発行
      $_SESSION["user_id"] = $result["id"];

      header("Location: memo.php");
    }catch(Exception $e){
      $err["other"] =  ERR_MESSAGE_7;
    }
  }
}
?>

<!DOCTYPE html>
<html lang="ja">
  <!-- ヘッド   -->
  <?php head("アカウント登録");?>

  <body>
    <!-- ヘッダー -->
    <?php require("./header.php");?>

    <!-- メイン -->
    <main>
      <div class="site-width">
        <div class="form_container">
          <h1>アカウント登録</h1>
          <form action="" method="post">
            <?php if(!empty($err["other"])){echo "<p class='error_message'>" . $err["other"] . "</p>";}?>
            <p>メールアドレス</p>
            <input type="text" spellcheck="false" name="email" value="<?php if(isset($email)){echo $email;}?>">
            <?php if(!empty($err["email"])){echo "<p class='error_message'>" . $err["email"] . "</p>";}?>
            <p class="pass">パスワード</p>
            <input type="text" spellcheck="false" name="pass" value="<?php if(isset($pass)){echo $pass;}?>">
            <?php if(!empty($err["pass"])){echo "<p class='error_message'>" . $err["pass"] . "</p>";}?>
            <p class="pass_re">パスワード再入力</p>
            <input type="text" spellcheck="false" name="pass_re" value="<?php if(isset($pass_re)){echo $pass_re;}?>">
            <?php if(!empty($err["pass_re"])){echo "<p class='error_message'>" . $err["pass_re"] . "</p>";}?>
            <input type="submit" class="important_submit account_registration_submit" name="submit" value="登録" class="account_registration_submit">
          </form>
          <a href="index.php">戻る</a>
        </div>
      </div>
    </main>

    <!-- フッター -->
    <?php require("./footer.php");?>
  </body>
</html>