<?php

require_once __DIR__.'/class/DB_Controller_main.php';
$error_messages = array();

//registory.phpからデータがPOSTされた時の処理
if(!empty($_POST)) {
    var_dump($_POST);
    
    $title = $_POST["title"];
    $payment = $_POST["payment"];
    //金額のカンマ区切りを除去し、int型に変更
    $payment = str_replace(",","", $_POST['payment']);
    // var_dump($payment);
    // exit;
    $payment_at = $_POST["payment_at"];
    //★★★sessionIDを使用する予定
    $user_id = 1;
    $group_id = 1;
    $type_id = $_GET['type_id'];
    $category_id = $_POST["category_id"];
    $memo = $_POST["content"];

    //db_mainインスタンス作成
    $db_main = new DB_Controller_main();

    //DB接続 & DBにデータ挿入
    $db_main->insert_a_record($title, $payment, $payment_at, $user_id, $type_id, $category_id, $group_id, $memo);
    //記帳画面へ遷移（リダイレクト）
    header('Location: http://localhost/kakeibo/view/registory.php');
}else {
    exit;
}
