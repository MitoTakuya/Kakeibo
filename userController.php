<?php
// オートローダー読み込み
require_once __DIR__ . '/class/AutoLoader.php';
$loader = AutoLoader::registerDirectory(__DIR__);
$loader = AutoLoader::register();
session_start();
UserLogin::notLogin();

try {
    DbConnector::connectDB();
	if (!empty($_POST) && isset($_POST['new_user'])) {
        $new_user = new UserRegistory();
        $user_errors = $new_user->inputConfirmation();
        if($user_errors == "ok") {
        // ログインに飛ばす
        header('Location: http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']).'/login.php');
        exit();
        }
    } elseif (!empty($_POST) && isset($_POST['login_user'])) {
        // フォームとtokenが同じか確認
        Config::check_token();
        $login_user = new UserLogin();
        $user_errors = $login_user->loginConfirmation();
        if($user_errors == "login_ok") {
        // トップページに飛ばす
        header('Location: http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']).'/dashboard.php');
        exit();
        }
    }
} catch (Exception $e) {
    $error_message = DbConnector::CONNECT_ERROR;
    require_once __DIR__.'/view/error.php';
    exit();
}
