<?php
// initファイルの読込みに差し替え予定
require_once __DIR__.'/class/DbConnectorMain.php';
require_once __DIR__.'/class/DbConnectorCategories.php';

if (DbConnector::connectDB()) {
    
    if(empty($_POST)) {
        //registory.phpオープン時の処理
        $group_id = 1; // = $_SESSION['group_id'];
        //メインTBLより特定グループのレコード取得する
        $records = DbConnectorMain::fetchGroupRecords($group_id);
        //カテゴリTBLよりカテゴリ名を取得する
        $categories = DbConnectorCategories::fetchAll();
        //取得失敗時、エラー画面を表示
        if(!$categories) {
            $error_message = DbConnector::TRANSACTION_ERROR;
            require __DIR__.'/view/error.php';
            die();
        }
        //収支別カテゴリに分ける
        $category_outgoes = $categories[1];
        $category_incomes = $categories[2];

    }else {
        //registory.phpからデータがPOSTされた時の処理
        $user_id = 1;  // = $_SESSION['user_id'];
        $group_id = 1; // = $_SESSION['group_id'];
        $title = $_POST["title"];
        $payment = $_POST["payment"];
        //金額のカンマ区切りを除去
        $payment = str_replace(",","", $_POST['payment']);
        $payment_at = $_POST["payment_at"];
        $type_id = $_GET['type_id'];
        $category_id = $_POST["category_id"];
        $memo = $_POST["content"];
        //DB接続 & DBにデータ挿入
        DbConnectorMain::insertRecord($title, $payment, $payment_at, $user_id, $type_id, $category_id, $group_id, $memo);
        //記帳画面へリダイレクト
        header('Location: http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']).'/view/registory.php');
    }
} else {
    //DB接続エラーの時、エラー画面を表示
    $error_message = DbConnector::CONNECT_ERROR;
    require_once __DIR__.'/view/error.php';
    die();

}