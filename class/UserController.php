<?php

class UserController
{
    public static $user_errors = array();

    // ユーザーグループバリデーション
    public static function checkUserGroup() {
        if (!isset($_POST['group_name']) || str_replace(array(" ", "　"), "", $_POST['group_name']) === '') {
            self::$user_errors['group_name'] = 'グループ名を入力してください';
        } elseif (mb_strlen($_POST['group_name']) > 30) {
            self::$user_errors['group_name'] = 'グループ名は30文字以内で入力してください';
        }
        if (!isset($_POST['goal']) || str_replace(array(" ", "　"), "", $_POST['goal']) === '') {
            self::$user_errors['goal'] = '目標貯金額を入力してください';
        } 
        return self::$user_errors;
    }

    // ユーザー情報バリデーション
    public static function checkUser() {
        if (!isset($_POST['user_name']) || str_replace(array(" ", "　"), "", $_POST['user_name']) === '') {
            self::$user_errors['user_name'] = '名前を入力してください';
        } elseif (mb_strlen($_POST['user_name']) > 30) {
            self::$user_errors['user_name'] = '名前は30文字以内で入力してください';
        }

        if (trim($_POST['mail']) === "") {
            self::$user_errors['mail'] = "メールアドレスを入力してください";
        }

        if (trim($_POST['password']) === "") {
            self::$user_errors['password'] = "パスワードを入力してください";
        } elseif (strlen($_POST['password']) < 4) {
            self::$user_errors['password'] = 'パスワードを4文字以上で入力してください';
        }

        if (!empty($_FILES['user_image']['name'])) {
            $ext = substr($_FILES['user_image']['name'], -3);
            if ($ext != 'jpg' && $ext != 'gif' && $ext != 'png') {
                self::$user_errors['user_image'] = '画像の形式は[jpg],[gif],[png]のみです。';
            }
        } elseif (empty($_FILES['user_image']['name'])) {
            self::$user_errors['user_image'] = '画像を選択してください';
        }
        return self::$user_errors;
    }
}
