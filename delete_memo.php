<!-- メモ削除ページ -->
<?php
if(isset($_POST["delete"])){
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



    try{
        // メモを削除
        $dbh = db_connect();

        $sql = "UPDATE memos SET delete_flg = :delete_flg WHERE id = :memo_id";
        $data = [":delete_flg" => true,":memo_id" => $memo_id];

        $stmt = query_post($dbh,$sql,$data);

        // メモの削除成功時にでるモーダルのメッセージを発行
        if($stmt) $_SESSION["delete_memo"] = SUC_MESSAGE_3;
    }catch(Exception $e){
        $err["other"] =  ERR_MESSAGE_7;
    }
}
?>