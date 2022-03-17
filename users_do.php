<?php
require('class/DB_Connector_users.php');

if(!empty($_POST)){
	$new_user = new DB_Connector_users();
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
		if($user_errors == "login") {
		// トップページに飛ばす
		header('Location: ../view/index.php');
		}
	}
}
