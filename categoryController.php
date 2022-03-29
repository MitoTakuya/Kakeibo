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
        $type_id = 1; //支出
    }else {
        $type_id = 2; //収入
    }

    //特定グループのカテゴリ別レコード取得する
    $records = DbConnectorMain::fetchFilteredRecords(group_id: $group_id, category_id: $category_id);

    /***************************************
    *ページネーション処理 
    ***************************************/
    // 1ページに表示するレコード数
    define('MAX','5'); 
    // トータルレコード件数
    $total_records = count($records); 
    // トータルページ数
    $max_page = ceil($total_records / MAX); 
    //URLに渡された現在のページを取得
    if(!isset($_GET['page_id'])){ 
        $now = 1;
    }else{
        $now = $_GET['page_id'];
    }
    // 取得したデータの何番目から表示するか
    $start_no = ($now - 1) * MAX; 
    // 1ページに表示するレコードを切り取る
    $records = array_slice($records, $start_no, MAX, true);
    //「前へ」のページ数
    $previous =  $now -1;
    //「次へ」ページ数
    $next =  $now + 1;
    
} else {
    $error_message = DbConnector::CONNECT_ERROR;
    require_once __DIR__.'/view/error.php';
    die();
}

?>