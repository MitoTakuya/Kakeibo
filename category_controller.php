<?php
require_once __DIR__.'/class/DB_Connector_main.php';
$error_messages = array();

if (DB_Connector::connectDB()) {
    //★★★仮置きsessionIDを使用する予定
    $user_id = 1;
    $group_id = 1;
    $category_id = (int)$_GET["id"];
    var_dump($category_id);

    //インスタンス作成
    $db_connect = new DB_Connector_main;

    //特定グループのカテゴリ別レコード取得する
    $records = $db_connect->fetchFilteredRecords(group_id: $group_id, category_id: $category_id);
    var_dump($records);

    //データ取得不可の場合エラー取得し、エラー画面を出力する
    if(!$records) {
        $error_message = $records;
        // include('error.php');
        die();
    }
} else {
    $error_message = $result;
    // include('error.php');
    echo $error_message;
    die();
}

?>