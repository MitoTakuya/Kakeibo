<?php
require('class/DB_Connector_users.php');

$new_user = new DB_Connector_users();

if(!empty($_POST)){	

	// ユーザー登録の際
	if(isset($_POST['new_user'])) {
		$user_errors = $new_user->inputConfirmation();
		if($user_errors == "ok") {
		// ログインに飛ばす
		header('Location: ../view/login.php');
		}
	// ログインの際
	} else if(isset($_POST['login_user'])) {
		$user_errors = $new_user->loginConfirmation();
		if($user_errors == "login_ok") {
		// トップページに飛ばす
		header('Location: ../view/index.php');
		}
	}
} elseif (isset($_GET['id']) && is_numeric(($_GET['id']))) {
	// $user_id = $_GET['id'];
	$user_id = 100;
	// var_dump($user_id);
	$user_show = $new_user->fetchUsersFullRecords($user_id);
	var_dump($user_show);
}

