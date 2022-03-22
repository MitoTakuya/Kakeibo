<?php
require('class/DB_Connector_user_group.php');
require('class/Config.php');

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $user_group = new DB_Connector_user_group();

    // ユーザーグループ情報取得
    $row = $user_group->fetchUserGroup($_GET['id']);

    // POSTされたら入力チェック
    if (!empty($_POST)) {
        $group_errors = Config::checkUserGroup();
        // エラーがなければグループ情報更新
        if (count($group_errors) == 0) {
            var_dump($_POST['group_name'], $_POST['goal'], $_POST['id']);
            $user_group->editUserGroup($_POST['group_name'], $_POST['goal'], $_POST['id']);
			// ユーザー詳細ページに飛ばす
			header('Location: ../view/user_show.php?id='.$_SESSION['id']);
            exit();
        }
    }
}
