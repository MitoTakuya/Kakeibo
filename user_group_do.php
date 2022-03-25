<?php
require_once __DIR__ . '/init.php';
require_once __DIR__ . '/class/DB_Connector_user_group.php';
require_once __DIR__ . '/class/UserController.php';

if (isset($_SESSION['group_id'])) {
    $user_group = new DB_Connector_user_group();
    // ユーザーグループ情報取得
    $row = $user_group->fetchUserGroup($_SESSION['group_id']);
    // POSTされたら入力チェック
    if (!empty($_POST)) {
        $group_errors = UserController::checkUserGroup();
        // エラーがなければグループ情報更新
        if (count($group_errors) == 0) {
            $user_group->editUserGroup($_POST['group_name'], $_POST['goal'], $_POST['id']);
			// ユーザー詳細ページに飛ばす
            header('Location: http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']).'/user_show.php');
            exit();
        }
    }
}
