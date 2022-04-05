<?php
require_once __DIR__.'/init.php';

try {
    DbConnector::connectDB();
    //registory.phpからデータがPOSTされた時の処理
    if(!empty($_POST)) {
        Config::check_token();
        $user_id = $_SESSION['id'];
        $group_id = $_SESSION['group_id'];
        $id = (int)$_POST["record_id"];
        //空白の場合titleの更新はしない
        $title = Config::delete_space($_POST["title"]);
        //金額のカンマ区切りを除去
        $payment = str_replace(",","", $_POST['payment']);
        $payment_at = $_POST["payment_at"];
        $type_id = $_POST['type_id'];
        $category_id = $_POST["category_id"];
        $memo = Config::delete_space($_POST["content"]);

        //200文字以上の場合はmemoの更新はしない
        if(mb_strlen($memo) > 200) {
            $memo = null;
        }

        //更新前に対象レコードがDBに存在するか確認
        $confirm = DbConnectorMain::fetchOne($id);

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
        exit;
    }

} catch (Exception $e) {

    switch ($e->getCode()) {
        case 2002:
            $error_message = DbConnector::CONNECT_ERROR;
            break;
        case 2006:
            $error_message = DbConnector::TRANSACTION_ERROR;
            break;
        default:
        $error_message = "予期せぬエラーが発生しました。";
            break;
    }
    require_once __DIR__.'/view/error.php';
    die();
}