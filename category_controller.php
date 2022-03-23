<?php
require_once __DIR__.'/class/DB_Connector_main.php';
$error_messages = array();

//★★★仮置きsessionIDを使用する予定
$user_id = 1;
$group_id = 1;
$category_id = (int)$_GET["id"];
var_dump($category_id);

//インスタンス作成
$db_connect = new DB_Connector_main;

//メインTBLより特定グループのレコード取得する
$records = $db_connect->fetchFilteredRecords(group_id: $group_id, category_id: $category_id);

var_dump($records);


//★接続エラーが起きた場合どうするか？ログイン画面にリダイレクトする？
if(!$records) {
    $error_messages = $records;
    // exit;
}
    

?>