<?php
require_once dirname(__FILE__) . '/DB_Connector_users.php';

class LoginController
{
    public static $user_errors = array();

    private $group_id = 0;

    public function setGroupId($group_id)
    {
        $this->group_id = $group_id;
    }
    public function getGroupId()
    {
        return $this->group_id;
    }

    // ログイン入力確認
    public static function loginConfirmation()
    {        
        // メールアドレスが入力されているか確認
        if (trim($_POST['mail']) === "") {
            self::$user_errors['login_mail'] = "メールアドレスを入力してください";
        } else {
            $mail = $_POST['mail'];
        }

        // パスワードが入力されているか確認
        if (trim($_POST['password']) === "") {
            self::$user_errors['login_password'] = "パスワードを入力してください";
        } else {
            $password = $_POST['password'];
        }

        // パスワード、メールアドレスが入力されていたらチェック
        if (!empty($mail) && !empty($password)) {
            $user_password = DB_Connector_users::loginUser($mail);
            if (!is_array($user_password)) {
                self::$user_errors['login_mail'] = 'メールアドレスが見つかりません';
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
            $_SESSION['id'] = $user_password['id'];
            return "login_ok";
        } else {
            return self::$user_errors;
        }
    }
}
