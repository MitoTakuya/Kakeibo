<?php
require('class/DB_Connector_users.php');
require('class/Config.php');
if (DB_Connector::connectDB()) {
	$new_user = new DB_Connector_users();

	if (!empty($_POST)) {
		// ユーザー情報更新の際
		if (isset($_POST['user_update'])) {
			// $row = $user_group->fetchUserGroup($_GET['id']);
			// バリデーションチェック
			$user_errors = Config::checkUser();
			// $user_errors = $new_user->checkEditMail($_POST['mail'], $_POST['id']);
			// アドレスチェックを行う
			// エラーがなければユーザー情報更新
			if (count($user_errors) == 0) {
				// ユーザー詳細ページに飛ばす
            	// パスワードを暗号化
				$hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
				$user_image = date('YmdHis') . $_FILES['user_image']['name'];
				// 画像をアップロード
				move_uploaded_file($_FILES['user_image']['tmp_name'], '../images/'. $user_image);
				$new_user->editUser($_POST['user_name'], $_POST['password'], $_POST['mail'], $user_image, $_POST['id']);
				header('Location: ../view/user_show.php?id='.$_SESSION['id']);
				exit();
			}
		}

		// ユーザー登録の際
		if (isset($_POST['new_user'])) {
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
