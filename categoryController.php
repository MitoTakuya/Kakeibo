<?php
require_once __DIR__.'/init.php';

try {
    DbConnector::connectDB();
    //初期化
    $error_messages = array();

    if (!empty($_POST["target_date"])) {
        Config::check_token();
        $target_date = $_POST["target_date"];
        $_SESSION['target_date'] = $target_date;
    } else {
        $target_date = $_SESSION['target_date'];
    }
    
    $user_id = $_SESSION['id'];
    $group_id = $_SESSION['group_id'];
    $category_id = (int)$_GET["id"];
    
    if ($category_id <= 100) {
        $type_id = 1; //支出
    } else {
        $type_id = 2; //収入
    }

    // 特定グループの存在するカテゴリ一覧を取得する
    $category_records = DbConnectorMain::fetchCategories($group_id, $target_date);
    foreach ($category_records as $category) {
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
            if ($category_name['category_id'] <= 100) {
                // 支出
                $outgoes[] = $category_name;
            } else {
                // 収入
                $incomes[] = $category_name;
            }
        }
    }
    
    //カテゴリ別レコード数
    $total_record = DbConnectorMain::countRecords($group_id, $target_date, $category_id);
    // 1ページに表示するレコード数
    $limit = 10;
    //URLに渡された現在のページ数
    if (!isset($_GET['page_id'])) {
        $now = 1;
    } else {
        $now = $_GET['page_id'];
    }

    //取得したデータの何番目から表示するか
    $offset = ($now - 1) * $limit;
    //全ページ数を決める
    $max_page = ceil($total_record / $limit);
    //「前へ」のページ数
    $previous =  $now -1;
    //「次へ」ページ数
    $next =  $now + 1;
    //日付と更新時間を降順で表示
    DbConnector::makeOrderClause(desc: true, column:'payment_at');
    DbConnector::adOrderClause(desc: true, column:'updated_at');
    //ページ単位ごとのカテゴリ別レコードを取得
    $records = DbConnectorFullRecords::fetchLimitedRecords(group_id: $group_id, limit: $limit, category_id: $category_id, target_date: $target_date, offset: $offset);

    //モーダルウィンドウからのPOST処理
    if (!empty($_POST["record_id"])) {
        Config::check_token();
        //初期化
        $error_messages = array();
        $id = (int)$_POST["record_id"];
        $title = Config::delete_space($_POST["title"]);
        $payment = str_replace(",", "", $_POST['payment']);
        $payment_at = $_POST["payment_at"];
        $type_id = $_POST['type_id'];
        $memo = Config::delete_space($_POST["content"]);

        //更新する入力値チェック
        if (empty($title) || mb_strlen($title) > 30 || $payment < 1 || mb_strlen($memo) > 200) {
            $error_messages["update"] = '※更新に失敗しました。編集した内容に誤りがあります。';
        } elseif ($memo == '') {
            $memo = ' ';
        }

        //更新前に対象レコードの有無チェック
        $confirm = DbConnectorMain::fetchOne($id);

        if ($confirm) {
            if (empty($error_messages)) {
                //レコードを更新する
                DbConnectorMain::updateRecord($id, $title, $payment, $payment_at, $user_id, $type_id, $category_id, $group_id, $memo);
                //POST元(編集していた)ページにリダイレクトする。
                $uri = filter_input(INPUT_SERVER, "HTTP_REFERER");
                header("Location: ".$uri);
                exit;
            }
        } else {
            $error_messages["update"] = '※更新したデータはすでに削除されております。';
        }
    }
} catch (Exception $e) {
    // 接続失敗時にエラー画面を読み込む
    $error_code = $e->getCode();
    $error_message = Config::getErrorMessage($error_code);
    require_once __DIR__.'/view/error.php';
    die();
}
