<?php
require_once __DIR__.'/class/DbConnectorMain.php';

if (DbConnector::connectDB()) {
    //registory.phpからデータがPOSTされた時の処理
    if(!empty($_POST)) {
        
        //★★★sessionIDを使用する予定
        $user_id = 1;
        $group_id = 1;
        $id = (int)$_POST["record_id"];
        $title = $_POST["title"];
        $payment = $_POST["payment"];
        //金額のカンマ区切りを除去
        $payment = str_replace(",","", $_POST['payment']);
        $payment_at = $_POST["payment_at"];
        $type_id = $_POST['type_id'];
        $category_id = $_POST["category_id"];
        $memo = $_POST["content"];

        //更新前に対象レコードがDBに存在するか確認
        $confirm = DbConnectorMain::fetchOne($id);
        
        if (is_array($confirm)) {
            //レコードを更新する
            DbConnectorMain::updateRecord($id, $title, $payment, $payment_at, $user_id, $type_id, $category_id, $group_id, $memo);
        }else {
            $error_message = "すでにデータが削除されております。";
            echo $error_messages;
        }

        //POST元のページにリダイレクトする。
        $uri = $_SERVER['HTTP_REFERER'];
        var_dump($uri);
        header("Location: ".$uri);

    }else {

        //★ログイン画面にリダイレクト処理を追記する予定
        echo "不正なアクセスです。";
    }
} else {
    //DB接続エラーの時、エラー画面を表示
    $error_message = DbConnector::$connect_error;
    require_once(__DIR__.'/view/error.php');
    die();

}