<?php
require_once __DIR__.'/class/DB_Connector_main.php';
require_once __DIR__.'/class/DB_Connector_category.php';

if (DB_Connector::connectDB()) {
    //registory.phpからデータがPOSTされた時の処理
    if(!empty($_POST)) {
        //★仮置き
        $user_id = 1;  // = $_SESSION['user_id'];
        $group_id = 1; // = $_SESSION['group_id'];
        $title = $_POST["title"];
        $payment = $_POST["payment"];
        //金額のカンマ区切りを除去し、int型に変更
        $payment = str_replace(",","", $_POST['payment']);
        $payment_at = $_POST["payment_at"];
        $type_id = $_GET['type_id'];
        $category_id = $_POST["category_id"];
        $memo = $_POST["content"];
        //db_mainインスタンス作成
        $db_main = new DB_Connector_main();
        //DB接続 & DBにデータ挿入
        $db_main->insertRecord($title, $payment, $payment_at, $user_id, $type_id, $category_id, $group_id, $memo);
        //記帳画面へリダイレクト
        header('Location: http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']).'/view/registory.php');
    }else {

    //★仮置き
    $group_id = 1; // = $_SESSION['group_id'];

    //インスタンス作成
    $db_main = new DB_Connector_main;
    $db_categories = new DB_Connector_category;

    //メインTBLより特定グループのレコード取得する
    $records = $db_main->fetchGroupRecords($group_id);

    //カテゴリTBLよりカテゴリ名を取得する
    $categories = $db_categories->fetchAll();
    //取得不可の時、エラー画面を表示
    if(!$categories) {
        $error_message = DB_Connector::$transaction_error;
        var_dump($error_message);
        require(__DIR__.'/view/error.php');
        die();
    }
    
    //収支別カテゴリに分ける
    $category_outgoes = $categories[1];
    $category_incomes = $categories[2];

    }
} else {
    //DB接続エラーの時、エラー画面を表示
    $error_message = DB_Connector::$connect_error;
    require_once __DIR__.'/view/error.php';
    die();

}