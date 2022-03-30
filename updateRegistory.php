<?php
require_once __DIR__.'/class/DbConnectorMain.php';

if (DbConnector::connectDB()) {
    //registory.phpからデータがPOSTされた時の処理
    if(!empty($_POST)) {
        Config::check_token();
        $user_id = $_SESSION['id'];
        $group_id = $_SESSION['group_id'];
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
        $confirm = DbConnector::fetchOne($id);
        
        if (is_array($confirm)) {
            //レコードを更新する
            DbConnectorMain::updateRecord($id, $title, $payment, $payment_at, $user_id, $type_id, $category_id, $group_id, $memo);
        }else {
            $error_message = "すでにデータが削除されております。";
            echo $error_messages;
        }

        //POST元(編集していた)ページにリダイレクトする。
        $uri = filter_input(INPUT_SERVER,"HTTP_REFERER");
        header("Location: ".$uri);
    }

} else {
    //DB接続エラーの時、エラー画面を表示
    $error_message = DbConnector::$connect_error;
    require_once(__DIR__.'/view/error.php');
    die();

}