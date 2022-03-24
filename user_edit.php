<?php
require_once __DIR__ . '/class/DB_Connector_users.php';
require_once __DIR__ . '/class/UserController.php';
session_start();

if (DB_Connector::connectDB()) {
    // ユーザー情報更新の際
    if (isset($_POST['user_update'])) {
        // バリデーションチェック
        $edit_user = new UserController();
        $user_errors = $edit_user->checkConfirmation();
        if($user_errors == "ok") {
            // ユーザー詳細に飛ばす
            header('Location: http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']).'/user_show.php?id='.$_SESSION['id']);
            exit();
            }
    }
    // ユーザー詳細ページにアクセスした時
	if (isset($_GET['id']) && is_numeric(($_GET['id']))) {
		$user_id = $_GET['id'];
		$user_show = DB_Connector_users::fetchUsersFullRecords($user_id);
		foreach ($user_show as $row) {
			// ログイン中ユーザーを抽出
			if ($row['user_id'] == $user_id) {
				$current_user = $row;
			} else {
				// ログインユーザー以外
				$other_users[] = $row;
			}
		}
    }
} else {
	// include('エラー画面');
}
