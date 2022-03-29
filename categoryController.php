<?php
require_once __DIR__.'/class/DbConnectorMain.php';
$error_messages = array();

if (DbConnector::connectDB()) {
    //★★★仮置きsessionIDを使用する予定
    $user_id = 1;
    $group_id = 1;
    $category_id = (int)$_GET["id"];

    //カテゴリTBLよりカテゴリ名を取得する
    $categories = DbConnectorMain::fetchCategoryColumns();

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
    $error_message = $result;
    require_once(__DIR__.'/view/error.php');
    die();
}

?>