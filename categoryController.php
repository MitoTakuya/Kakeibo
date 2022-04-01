<?php
require_once __DIR__.'/init.php';

try {
    DbConnector::connectDB();
    if(!empty($_GET)) {
        
        $user_id = $_SESSION['id'];
        $group_id = $_SESSION['group_id'];
        $category_id = (int)$_GET["id"];

        //カテゴリTBLよりカテゴリ名を取得する
        $categories = DbConnectorCategories::fetchCategories();
        //カテゴリ取得に関しては戻り値が空の配列の場合はエラーとする
        if(empty($categories)) {
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
        
        // orderby句の基準にするカラムと、並び順（ascかdescか）を指定するメソッド
        DbConnector::makeOrderClause(desc: true);
        // トータルレコード件数
        $total_record = DbConnectorMain::countRecords($group_id);
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
        $max_page = ceil($total_record / $limit);
        //「前へ」のページ数
        $previous =  $now -1;
        //「次へ」ページ数
        $next =  $now + 1;
        //★差し替え予定　メインTBLよりページ毎のカテゴリ別レコードを取得
        $records = DbConnectorFullRecords::fetchLimitedRecords(group_id: $group_id, limit: $limit, offset: $offset);
        
        if(!empty($_POST)) {
            Config::check_token();
            header('Location: http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
            exit;
        }

    }else {
        $error_message = "不正な通信です。";
        require_once __DIR__.'/view/error.php';
        die();
    }

} catch (Exception $e) {

    switch ($e->getCode()) {
        case 2002:
            $error_message = DbConnector::CONNECT_ERROR;
            break;
        case 2006:
            $error_message = DbConnector::TRANSACTION_ERROR;
            break;
        default:
        $error_message = "予期せぬエラーが発生しました。";
            break;
    }
    require_once __DIR__.'/view/error.php';
    die();

}
