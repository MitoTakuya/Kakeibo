<?php
require_once __DIR__ . '/init.php';
require_once __DIR__ . '/class/DbConnectorUsers.php';
require_once __DIR__ . '/class/UserRegistory.php';

if (DbConnector::connectDB()) {
    // ユーザー情報更新の際
    if (isset($_POST['user_update'])) {
        // バリデーションチェック
        $edit_user = new UserRegistory();
        $user_errors = $edit_user->checkConfirmation();
        if($user_errors == "ok") {
            // ユーザー詳細に飛ばす
            header('Location: http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']).'/userShow.php');
            exit();
            }
    }
    // ユーザー詳細ページにアクセスした時
	if (isset($_SESSION['group_id'])) {
		$group_id = $_SESSION['group_id'];
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
    if(array_key_exists($_SESSION['id'],$_POST)){
        // 退会、ログアウト
        DbConnectorUsers::disableUser($_SESSION['id']);
        UserLogin::logout();
        header('Location: http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']).'/userDelete.php');
        exit();
    }
} else {
	// include('エラー画面');
}
