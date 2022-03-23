<?php
require('class/DB_Connector_users.php');
require('class/UserController.php');
require('class/LoginController.php');

if (DB_Connector::connectDB()) {
	if (!empty($_POST) && isset($_POST['new_user'])) {
        $new_user = new UserController();
        $user_errors = $new_user->inputConfirmation();
        if($user_errors == "ok") {
        // ログインに飛ばす
        header('Location: ../view/login.php');
        }
    } elseif (!empty($_POST) && isset($_POST['login_user'])) {
        $login_user = new LoginController();
        $user_errors = $login_user->loginConfirmation();
        if($user_errors == "login_ok") {
        // トップページに飛ばす
        header('Location: ../view/index.php');
        }
    }
} else {
	// include('エラー画面');
}
