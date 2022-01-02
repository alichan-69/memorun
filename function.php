<?php
session_start();

const SUC_MESSAGE_1 = "メモを登録しました。";
const SUC_MESSAGE_2 = "メモを更新しました。";
const SUC_MESSAGE_3 = "メモを削除しました。";
const ERR_MESSAGE_1 = "必ず入力してください。";
const ERR_MESSAGE_2 = "255文字以下で入力してください。";
const ERR_MESSAGE_3 = "8文字以上255文字以下で入力してください。";
const ERR_MESSAGE_4 = "半角英数字のみで入力してください。";
const ERR_MESSAGE_5 = "E-mailの形式で入力してください。";
const ERR_MESSAGE_6 = "再入力されたパスワードが異なっています。";
const ERR_MESSAGE_7 = "障害発生によりアカウント登録が出来ませんでした。";
const ERR_MESSAGE_8 = "メールアドレスまたはパスワードが異なっています。";
const REG_EMAIL = "/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+/";
const REG_HALf_NUMBER = "/^[0-9a-zA-Z]*$/";

// 未入力チェック
function valid_empty($post,$err_key){
    if($post === ""){
        global $err;
        $err[$err_key] = ERR_MESSAGE_1;
    }
}

// 文字数チェック
function valid_string_length($post,$err_key){
    if(mb_strlen($post) > 255){
        global $err;
        $err[$err_key] = ERR_MESSAGE_2;
    }
}

// email形式チェック
function valid_email_match($post,$err_key){
    if(!preg_match(REG_EMAIL, $post)){
        global $err;
        $err[$err_key] = ERR_MESSAGE_5;
    }
}

// pass文字数チェック
function valid_pass_string_length($post,$err_key){
    if(mb_strlen($post) > 255 || mb_strlen($post) < 8){
        global $err;
        $err[$err_key] = ERR_MESSAGE_3;
    }
}

// 半角数字チェック
function valid_half_number_match($post,$err_key){
    if(!preg_match(REG_HALf_NUMBER, $post)){
        global $err;
        $err[$err_key] = ERR_MESSAGE_4;
    }
}

// 同値チェック
function valid_equal_value($post1,$post2,$err_key){
    if($post1 !== $post2){
        global $err;
        $err[$err_key] = ERR_MESSAGE_6;
    }
}

// db接続
function db_connect(){
    $dsn = "mysql:dbname=memo;host=localhost;charset=utf8";
    $user = 'root';
    $pass = '';
    $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
    ];
    
    $dbh = new PDO($dsn,$user,$pass,$options);
    return $dbh;
}

// sqlの実行
function query_post($dbh,$sql,$data){
    $stmt = $dbh->prepare($sql);
    $stmt->execute($data);
    return $stmt;
}

// ログイン認証
function login_auth(){
    // 既にログインしているユーザーかチェック
    if(isset($_SESSION["user_id"])){
        // ログイン時刻を更新
        $_SESSION["login_date"] = time();
        
        // ログイン可能時刻を超えていないか判断
        if($_SESSION["login_date"] < $_SESSION["login_date_limit"]){
            // ログイン可能時刻を更新
            $_SESSION["login_date_limit"] = $_SESSION["login_date"] + 30 * 24 * 60 * 60;
        }else{
            session_destroy();
            
            header("Location: index.php");
        }
    }else{
        header("Location: index.php");
    }
}

// 文字列の無害化
function sanitize($str){
    return htmlspecialchars($str);
}

// ページネーション処理
function pagenation($current_page_num,$total_page,$page_col_num = 5){
    // 現在のページが総ページ数と同じかつ総ページ数が表示項目以上なら左にリンク４個出す
    if($current_page_num == $total_page && $total_page >= $page_col_num){
        $min_page_num = $current_page_num - 4;
        $max_page_num = $current_page_num;
    // 現在のページが、総ページ数の１ページ前なら、左にリンク３個、右に１個出す
    }elseif($current_page_num == ($total_page - 1) && $total_page >= $page_col_num){
        $min_page_num = $current_page_num - 3;
        $max_page_num = $current_page_num + 1;
    // 現在のページが2の場合は左にリンク１個、右にリンク３個だす。
    }elseif($current_page_num == 2 && $total_page >= $page_col_num){
        $min_page_num = $current_page_num - 1;
        $max_page_num = $current_page_num + 3;
    // 現在のページが1の場合は右にリンク4個だす。    
    }elseif($current_page_num == 1 && $total_page >= $page_col_num){
        $min_page_num = $current_page_num;
        $max_page_num = $current_page_num + 4;
    // 総ページ数が表示項目より少ない場合は、総ページ数をループのMax、ループのMinを１にする
    }elseif($total_page < $page_col_num){
        $min_page_num = 1;
        $max_page_num = $total_page;
    // それ以外は左に２個出す。
    }else{
        $min_page_num = $current_page_num - 2;
        $max_page_num = $current_page_num + 2;
    }

    echo "<ul class='pagenation'><div class='pagenation_flex_container'>";
    if($current_page_num != 1){
        echo "<li><a href='./memo.php?p=1'>&lt;</a></li>";
    }
    for($i = $min_page_num;$i <= $max_page_num;$i++){
        echo "<li";
        if($i == $current_page_num){
            echo " class='current_page'";
        }
        echo "><a href='./memo.php?p=" . $i . "'>" . $i . "</a></li>";
    }
    if($current_page_num != $total_page && $total_page >= $page_col_num){
        echo "<li><a href='./memo.php?p=" . $max_page_num . "'>&gt;</a></li>";
    }
    echo "</div></ul>";
}

// セッションを一度だけ発行
function get_session_flash($key){
    if(!empty($_SESSION[$key])){
        $data = $_SESSION[$key];
        $_SESSION[$key] = "";
        return $data;
    }
}

// ヘッドを描画
function head($title){
    echo "<head>";
        echo "<meta charset='utf-8'>";
        echo "<meta name='viewport' content='width=device-width,initial-scale=1'>";
        echo "<title>" . $title ."</title>";
        echo "<link rel='icon' href='./favicon.ico'>";
        echo "<link rel='stylesheet' href='css/styles.css'>";
        echo "<link href='https://fonts.googleapis.com/css2?family=Courier+Prime&display=swap' rel='stylesheet'>";
    echo "</head>";
}
?>