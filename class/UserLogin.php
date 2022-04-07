<?php

class UserLogin
{
    public static $user_errors = array();

    // ログイン入力確認
    public static function loginConfirmation()
    {
        // メールアドレスが入力されているか確認
        if (trim($_POST['mail']) === "") {
            self::$user_errors['login_mail'] = "メールアドレスを入力してください。";
        } else {
            $mail = $_POST['mail'];
        }

        // パスワードが入力されているか確認
        if (trim($_POST['password']) === "") {
            self::$user_errors['login_password'] = "パスワードを入力してください。";
        } else {
            $password = $_POST['password'];
        }

        // パスワード、メールアドレスが入力されていたらチェック
        if (!empty($mail) && !empty($password)) {
            $user_password = DbConnectorUsers::loginUser($mail);
            if (!is_array($user_password)) {
                self::$user_errors['login_mail'] = 'メールアドレスが見つかりません。';
            } else {
                // 指定したハッシュがパスワードにマッチしているか
                if (!password_verify($password, $user_password['password'])) {
                    // ユーザー情報をセッションに保存
                    self::$user_errors['login_password'] = "パスワードが違います。";
                }
            }
        }
        // エラーがなければ保存
        if (count(self::$user_errors) == 0) {
            $login_user = DbConnectorUsers::fetchUser($user_password['id']);

            //セッション固定攻撃対策
            session_regenerate_id(true);
            $_SESSION['id'] = $login_user['id'];
            $_SESSION['group_id'] = $login_user['group_id'];
            $_SESSION['user_image'] = $login_user['user_image'];

            return "login_ok";
        } else {
            return self::$user_errors;
        }
    }

    // 未ログインならログインページに
    public static function checkLogin()
    {
        if (!isset($_SESSION['id'])) {
            header('Location: http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']).'/login.php');
            exit();
        }
    }

    // ログイン済みならトップページに
    public static function notLogin()
    {
        if (isset($_SESSION['id'])) {
            header('Location: http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']).'/dashboard.php');
            exit();
        }
    }

    // ログアウト
    public static function logout()
    {
        session_start();
        $_SESSION = array();
        session_destroy();
    }
}
