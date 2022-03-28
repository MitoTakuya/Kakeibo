<?php
require_once __DIR__ . '/class/DbConnectorUsers.php';
require_once __DIR__ . '/class/UserRegistory.php';
require_once __DIR__ . '/class/UserLogin.php';
require_once __DIR__ . '/class/Config.php';
session_start();
UserLogin::notLogin();

if (DbConnector::connectDB()) {
	if (!empty($_POST) && isset($_POST['new_user'])) {
        $new_user = new UserRegistory();
        $user_errors = $new_user->inputConfirmation();
        if($user_errors == "ok") {
        // ログインに飛ばす
        header('Location: http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']).'/login.php');
        }
    } elseif (!empty($_POST) && isset($_POST['login_user'])) {
        // フォームとtokenが同じか確認
        Config::check_token();
        $login_user = new UserLogin();
        $user_errors = $login_user->loginConfirmation();
        if($user_errors == "login_ok") {
        // トップページに飛ばす
        header('Location: http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']).'/dashboard.php');
        }
    }
} else {
	// include('エラー画面');
}
