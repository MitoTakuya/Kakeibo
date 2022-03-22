<?php
require('class/DB_Connector_users.php');
// if (DB_Connector::connectDB()) {
	$new_user = new DB_Connector_users();

	if (!empty($_POST)) {	

		// ユーザー登録の際
		if(isset($_POST['new_user'])) {
			$user_errors = $new_user->inputConfirmation();
			if($user_errors == "ok") {
			// ログインに飛ばす
			header('Location: ../view/login.php');
			}
		// ログインの際
		} elseif (isset($_POST['login_user'])) {
			$user_errors = $new_user->loginConfirmation();
			if($user_errors == "login_ok") {
			// トップページに飛ばす
			header('Location: ../view/index.php');
			}
		}
		// ヘッダーからユーザー詳細ページの際
	} elseif (isset($_GET['id']) && is_numeric(($_GET['id']))) {
		$user_id = $_GET['id'];
		$user_show = $new_user->fetchUsersFullRecords($user_id);
		// var_dump($user_show);
		// var_dump($user_id);
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
// } else {
// 	include('エラー画面');
// }
