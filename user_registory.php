<?php
require_once __DIR__ . '/class/DB_Connector_users.php';
require_once __DIR__ . '/class/UserController.php';
require_once __DIR__ . '/class/LoginController.php';
session_start();
LoginController::notLogin();

if (DB_Connector::connectDB()) {
	if (!empty($_POST) && isset($_POST['new_user'])) {
        // フォームとtokenが同じか確認
        Config::check_token();
        $new_user = new UserController();
        $user_errors = $new_user->inputConfirmation();
        if($user_errors == "ok") {
        // ログインに飛ばす
        header('Location: http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']).'/login.php');
        }
    } elseif (!empty($_POST) && isset($_POST['login_user'])) {
        $login_user = new LoginController();
        $user_errors = $login_user->loginConfirmation();
        if($user_errors == "login_ok") {
        // トップページに飛ばす
        header('Location: http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']).'/dashboard.php');
        }
    }
} else {
	// include('エラー画面');
}
