<!-- メモ更新ページ -->
<?php
if(isset($_POST["update"])){
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
    
    $memo = $_POST["update_memo"];

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
?>