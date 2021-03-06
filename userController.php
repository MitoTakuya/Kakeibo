<?php
// オートローダー読み込み
require_once __DIR__ . '/class/AutoLoader.php';
$loader = AutoLoader::registerDirectory(__DIR__);
$loader = AutoLoader::register();
Config::is_ie();
session_start();
UserLogin::notLogin();

try {
    if (!empty($_POST) && isset($_POST['new_user'])) {
        DbConnector::connectDB();
        $new_user = new UserRegistory();
        $user_errors = $new_user->inputConfirmation();
        if ($user_errors == "ok") {
            // ログインに飛ばす
            header('Location: http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']).'/login.php');
            exit();
        }
    } elseif (!empty($_POST) && isset($_POST['login_user'])) {
        DbConnector::connectDB();
        // フォームとtokenが同じか確認
        Config::check_token();
        $login_user = new UserLogin();
        $user_errors = $login_user->loginConfirmation();
        if ($user_errors == "login_ok") {
            // トップページに飛ばす
            header('Location: http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']).'/dashboard.php');
            exit();
        }
    }
} catch (Exception $e) {
    // 接続失敗時にエラー画面を読み込む
    $error_code = $e->getCode();
    $error_message = Config::getErrorMessage($error_code);

    // echo $error_code;
    // echo $e->getMessage();

    include(__DIR__.'/view/error.php');
    die();
}
