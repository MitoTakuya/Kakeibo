<?php
require_once __DIR__ . '/class/DB_Connector_user_group.php';
require_once __DIR__ . '/class/UserController.php';
session_start();

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $user_group = new DB_Connector_user_group();
    // ユーザーグループ情報取得
    $row = $user_group->fetchUserGroup($_GET['id']);
    // POSTされたら入力チェック
    if (!empty($_POST)) {
        $group_errors = UserController::checkUserGroup();
        // エラーがなければグループ情報更新
        if (count($group_errors) == 0) {
            $user_group->editUserGroup($_POST['group_name'], $_POST['goal'], $_POST['id']);
			// ユーザー詳細ページに飛ばす
			header('Location: ../view/user_show.php?id='.$_SESSION['id']);
            exit();
        }
    }
}
