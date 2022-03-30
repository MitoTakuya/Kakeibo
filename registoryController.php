<?php
require_once __DIR__.'/init.php';

try{
    DbConnector::connectDB();
    if(empty($_POST)) {
        //registory.phpオープン時の処理
        $group_id = $_SESSION['group_id'];
        // orderby句の基準にするカラムと、並び順（ascかdescか）を指定するメソッド
        DbConnector::makeOrderClause(desc: true);
        //★仮置き　レコード数を取得する処理に書き換える
        $record_num = 30;
        // トータルレコード件数
        $total_records = $record_num;
        // 1ページに表示するレコード数
        $limit = 10;
        //URLに渡された現在のページ数
        if(!isset($_GET['page_id'])){ 
            $now = 1;
        }else{
            $now = $_GET['page_id'];
        }

        // 取得したデータの何番目から表示するか
        $offset = ($now - 1) * $limit;
        //全ページ数を決める 
        $max_page = ceil($total_records / $limit); 
        //「前へ」のページ数
        $previous =  $now -1;
        //「次へ」ページ数
        $next =  $now + 1;
        //メインTBLよりページ毎のレコード取得
        $records = DbConnectorFullRecords::fetchLimitedRecords(group_id: $group_id, limit: $limit, offset: $offset);
        //データ取得失敗時、エラー画面を表示
        if(!$records) {
            $error_message = DbConnector::TRANSACTION_ERROR;
            require __DIR__.'/view/error.php';
            die();
        }

        //カテゴリTBLよりカテゴリ名を取得する
        $categories = DbConnectorCategories::fetchCategories();
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
        Config::check_token();
        $user_id = $_SESSION['id'];
        $group_id = $_SESSION['group_id'];
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
        //POST元(登録した)ページにリダイレクトする。
        $uri = filter_input(INPUT_SERVER,"HTTP_REFERER");
        header("Location: ".$uri);
        exit;
    }

} catch (Exception $e) {
    switch ($e) {
        case 2002:
            $error_message = DbConnector::CONNECT_ERROR;
            break;
        case 1:
            $error_message = DbConnector::CONNECT_ERROR;
            break;
        default:
        $error_message = "予期せぬエラーが発生しました。";
            break;
    }
    require_once __DIR__.'/view/error.php';
    die();

}