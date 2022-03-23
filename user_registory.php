<?php
require('class/DB_Connector_users.php');
require('class/UserController.php');

if (DB_Connector::connectDB()) {
	if (!empty($_POST) && isset($_POST['new_user'])) {
        $new_user = new UserController();
        $user_errors = $new_user->inputConfirmation();
        if($user_errors == "ok") {
        // ログインに飛ばす
        header('Location: ../view/login.php');
        }
    }
} else {
	// include('エラー画面');
}
