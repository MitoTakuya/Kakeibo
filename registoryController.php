<?php
require_once __DIR__.'/init.php';

try{
    DbConnector::connectDB();
    //記帳画面のオープン処理
    $group_id = $_SESSION['group_id'];
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
    // orderby句の基準にするカラムと、並び順（ascかdescか）を指定するメソッド
    DbConnector::makeOrderClause(desc: true, column:'payment_at');
    DbConnector::adOrderClause(desc: true, column:'updated_at');
    //メインTBLよりページ毎のレコード取得
    $records = DbConnectorFullRecords::fetchLimitedRecords(group_id: $group_id, limit: $limit, offset: $offset);

    //カテゴリTBLよりカテゴリ名を取得する
    $categories = DbConnectorCategories::fetchCategories();
    //戻り値が空の配列の場合はエラー画面を表示(カテゴリ名が取得できない事象はエラーとする)
    if(empty($categories)) {
        $error_message = DbConnector::TRANSACTION_ERROR;
        require __DIR__.'/view/error.php';
        die();
    }

    //収支別カテゴリに分ける
    $category_outgoes = $categories[1];
    $category_incomes = $categories[2];

    //支出タブをアクティブとするときの条件
    $is_outgo = !isset($_POST['type_id']) || $_POST['type_id'] == 1;

    //データPOSTされたとき
    if(!empty($_POST)) {
        Config::check_token();

        $error_messages = array();
        $user_id = $_SESSION['id'];
        $group_id = $_SESSION['group_id'];
        $title = Config::delete_space($_POST["title"]);
        $payment = str_replace(",","", $_POST['payment']);
        $payment_at = $_POST["payment_at"];
        $type_id = $_POST['type_id'];
        $category_id = $_POST["category_id"];
        $memo = Config::delete_space($_POST["content"]);

        //新規データの登録
        if(empty($_POST["record_id"])) {
         
            if(empty($title)) {
                $error_messages["title"] = '※タイトルを入力してください';
            } elseif (mb_strlen($title ) > 30) {
                $error_messages["title"] = '※30文字以内で入力してください';
            }
    
            if($payment < 1) {
                $error_messages["payment"] = '※１円以上で入力してください  ';
            }
    
            if(mb_strlen($memo) > 200) {
                $error_messages["memo"] = '※200文字以内で入力してください';
            }
    
            //値チェックがOKだったらDB登録
            if(empty($error_messages)) {
                DbConnectorMain::insertRecord($title, $payment, $payment_at, $user_id, $type_id, $category_id, $group_id, $memo);
                //POST元(登録した)ページにリダイレクトする。
                $uri = filter_input(INPUT_SERVER,"HTTP_REFERER");
                header("Location: ".$uri);
                exit;
            }

        }else {

            //既存データの更新
            $id = (int)$_POST["record_id"];
            //更新する入力値チェック
            if(empty($title) || mb_strlen($title ) > 30 || $payment < 1 || mb_strlen($memo) > 200) {
                $error_messages["update"] = '※更新に失敗しました。編集した内容に誤りがあります。';
            }elseif($memo == '') {
                $memo = ' ';
            }
    
            //更新前に対象レコードの有無チェック
            $confirm = DbConnectorMain::fetchOne($id);

            if ($confirm) {
                if (empty($error_messages)) {
                    //レコードを更新する
                    DbConnectorMain::updateRecord($id, $title, $payment, $payment_at, $user_id, $type_id, $category_id, $group_id, $memo);
                    //POST元(編集していた)ページにリダイレクトする。
                    $uri = filter_input(INPUT_SERVER,"HTTP_REFERER");
                    header("Location: ".$uri);
                    exit;
                }
            }else {
                $error_messages["update"] = '※更新したデータはすでに他ユーザによって削除されております。';
            }
        }
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