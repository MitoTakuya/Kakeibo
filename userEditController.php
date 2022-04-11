<?php
require_once __DIR__ . '/init.php';
try {
    DbConnector::connectDB();
    // ユーザー情報更新の際
    if (isset($_POST['user_update'])) {
        // フォームとtokenが同じか確認
        Config::check_token();
        // バリデーションチェック
        $edit_user = new UserRegistory();
        $user_errors = $edit_user->checkConfirmation();
        if ($user_errors == "ok") {
            // ユーザー詳細に飛ばす
            header('Location: http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']).'/userShow.php');
            exit();
        }
    }
    // ユーザー詳細ページにアクセスした時
    if (isset($_SESSION['group_id'])) {
        $group_id = $_SESSION['group_id'];
        // ログインユーザーの今月の支出を取得
        $user_payment = DbConnectorMain::userPayment($_SESSION['id']);
        // グループの今月の支出を取得
        $group_payment = DbConnectorMain::groupPayment($_SESSION['group_id']);
        // ユーザー一覧レコード取得
        $user_show = DbConnectorUsers::fetchUsersFullRecords($group_id);
        foreach ($user_show as $row) {
            // ログイン中ユーザーを抽出
            if ($row['user_id'] == $_SESSION['id']) {
                $current_user = $row;
            } else {
                // ログインユーザー以外
                $other_users[] = $row;
            }
        }
    }
    if (array_key_exists($_SESSION['id'], $_POST)) {
        // 退会、ログアウト
        DbConnectorUsers::disableUser($_SESSION['id']);
        UserLogin::logout();
        header('Location: http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']).'/userDelete.php');
        exit();
    }
} catch (Exception $e) {
    // 接続失敗時にエラー画面を読み込む
    $error_code = $e->getCode();
    $error_message = Config::getErrorMessage($error_code);

    // echo $error_code;
    // echo $e->getMessage();

    include(__DIR__.'/view/error.php');
    die();
}
