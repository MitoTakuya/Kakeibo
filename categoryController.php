<?php
require_once __DIR__.'/init.php';

try {
    DbConnector::connectDB();
    if(!empty($_GET)) {
        Config::check_token();
        $user_id = $_SESSION['id'];
        $group_id = $_SESSION['group_id'];
        $category_id = (int)$_GET["id"];

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

        if($category_id <= 100) {
            $type_id = 1; //支出
        }else {
            $type_id = 2; //収入
        }

        //特定グループのカテゴリ別レコード取得する
        $records = DbConnectorFullRecords::fetchFilteredRecords(group_id: $group_id, category_id: $category_id);
        //データ取得失敗時、エラー画面を表示
        if(!$records) {
            $error_message = DbConnector::TRANSACTION_ERROR;
            require __DIR__.'/view/error.php';
            die();
        }

        // 特定グループのカテゴリー一覧を取得する
        $category_records = DbConnectorMain::fetchCategories($group_id);
        foreach ($category_records as $category ) {
			// 選択中のカテゴリーを抽出
			if ($category['category_id'] == $category_id) {
				$current_cattegory = $category;
			} else {
				// それ以外のカテゴリー
				$other_cattegory[] = $category;
            }
		}
        // 支出と収入に分類
        if (isset($other_cattegory)) {
            foreach ($other_cattegory as $category_name) {
                if ($category_name['category_id'] < 100) {
                    // 支出
                    $payment[] = $category_name;
                } else {
                    // 収入
                    $income[] = $category_name;
                }
            }
        }
        
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
    }else {
        $error_message = "不正な通信です。";
        require_once __DIR__.'/view/error.php';
        die();
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
