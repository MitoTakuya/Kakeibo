<?php
require_once __DIR__ . '/init.php';

try {
    DbConnector::connectDB();
    if (isset($_SESSION['group_id'])) {
        $user_group = new DbConnectorUserGroups();
        // ユーザーグループ情報取得
        $row = $user_group->fetchUserGroup($_SESSION['group_id']);
        // POSTされたら入力チェック
        if (!empty($_POST)) {
            // フォームとtokenが同じか確認
            Config::check_token();
            $group_errors = UserRegistory::checkUserGroup();
            // エラーがなければグループ情報更新
            if (count($group_errors) == 0) {
                $user_group->editUserGroup($_POST['group_name'], $_POST['goal'], $_POST['id']);
                // ユーザー詳細ページに飛ばす
                header('Location: http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']).'/userShow.php');
                exit();
            }
        }
    }
} catch (Exception $e) {
    $error_message = DbConnector::CONNECT_ERROR;
    require_once __DIR__.'/view/error.php';
    exit();
}
