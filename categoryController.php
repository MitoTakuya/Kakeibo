<?php
// initファイルの読込みに差し替え予定
require_once __DIR__.'/class/DbConnectorMain.php';
require_once __DIR__.'/class/DbConnectorCategories.php';
$error_messages = array();

if (DbConnector::connectDB()) {
    //★★★仮置きsessionIDを使用する予定
    $user_id = 1;
    $group_id = 1;
    $category_id = (int)$_GET["id"];

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

    if($category_id <= 100) {
        //支出
        $type_id = 1;
    }else {
        //収入
        $type_id = 2;
    }

    //特定グループのカテゴリ別レコード取得する
    $records = DbConnectorMain::fetchFilteredRecords(group_id: $group_id, category_id: $category_id);

} else {
    $error_message = DbConnector::CONNECT_ERROR;
    require_once __DIR__.'/view/error.php';
    die();
}

?>