<?php
require_once __DIR__ . '/DB_Connector_users.php';
require_once __DIR__ . '/DB_Connector.php';

class UserController
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

    // ユーザー登録入力内容チェック
    public function inputConfirmation()
    {
        // フォームからの値を受け取る
        $user_name = filter_input(INPUT_POST, 'user_name');
        $mail = filter_input(INPUT_POST, 'mail');
        $password = filter_input(INPUT_POST, 'password');

        // メールアドレス重複確認
        $mail_error = DB_Connector_users::checkDuplicate($mail);
        if (isset($mail_error)) {
            self::$user_errors['mail'] = $mail_error;
        }

        self::checkUser();
        // ユーザーグループ
        // 新規グループ選択時
        if ($_POST['user_group'] == "new_group") {
            if (!isset($_POST['group_form']) || str_replace(array(" ", "　"), "", $_POST['group_form']) === '') {
                self::$user_errors['group_form'] = '家計簿名を入力してください';
            } elseif (mb_strlen($_POST['group_form']) > 30) {
                self::$user_errors['group_form'] = '30文字以内で入力してください';
            } else {
                $group_name = $_POST['group_form'];
                // ユニークキー作成方法は検討
                $group_password =  uniqid();
            }
            // 既存グループ選択時
        } elseif ($_POST['user_group'] == "existing_group") {
            if (!isset($_POST['group_form']) || str_replace(array(" ", "　"), "", $_POST['group_form']) === '') {
                self::$user_errors['group_form'] = 'グループパスワードを入力してください';
            } elseif (mb_strlen($_POST['group_form']) > 30) {
                self::$user_errors['group_form'] = '30文字以内で入力してください';
            } else {
                $group_password = $_POST['group_form'];
                // パスワードからuser_groupのidを検索
                $error = DB_Connector_users::searchGroupId($group_password);
                if (!is_array($error)) {
                    self::$user_errors['group_form'] = 'グループパスワードが違います。';
                } else {
                    self::setGroupId($error['id']);
                }
            }
        }

        // エラーがなければユーザー情報登録
        if (count(self::$user_errors) == 0) {
            // パスワードを暗号化
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $user_image = date('YmdHis') . $_FILES['user_image']['name'];
            // 画像をアップロード
            move_uploaded_file($_FILES['user_image']['tmp_name'], '../images/'. $user_image);
            
            if ($_POST['user_group'] ==  "new_group") {
                // ユーザー、新規グループ登録
                $this->createUserWithGroup($group_name, $group_password, $user_name, $hash, $mail, $user_image);
            } else {
                // ユーザー登録
                $group_id = self::getGroupId();
                DB_Connector_users::insertUser($user_name, $hash, $mail, $user_image, $group_id);
            }

            //セッション固定攻撃対策
            session_regenerate_id(true);
            // ユーザー情報をセッションに保存
            $_SESSION['mail'] = $mail;
            $_SESSION['password'] = $password;
            $_SESSION['password'] = $password;

            return "ok";
        } else {
            return self::$user_errors;
        }
    }

    // ユーザー更新情報チェック
    public function checkConfirmation()
    {
        // フォームからの値を受け取る
        $id = filter_input(INPUT_POST, 'id');
        $user_name = filter_input(INPUT_POST, 'user_name');
        $mail = filter_input(INPUT_POST, 'mail');
        $password = filter_input(INPUT_POST, 'password');

        // 登録済みのアドレスか確認
        $mail_error = DB_Connector_users::checkEditMail($mail, $id);
        if (isset($mail_error)) {
            self::$user_errors['mail'] = $mail_error;
        }

        // バリデーションチェック
        self::checkUser();


        // エラーがなければユーザー情報更新
        if (count(self::$user_errors) == 0) {
            // パスワードを暗号化
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $user_image = date('YmdHis') . $_FILES['user_image']['name'];
            // 画像をアップロード
            move_uploaded_file($_FILES['user_image']['tmp_name'], '../images/'. $user_image);
            // 更新処理
            DB_Connector_users::editUser($user_name, $hash, $mail, $user_image, $id);
            return "ok";
        } else {
            return self::$user_errors;
        }
    }

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

    // ユーザー・新規グループ登録
    public function createUserWithGroup($group_name, $group_password, $user_name, $hash, $mail, $user_image)
    {
        $group_id = DB_Connector_users::insertUserGroup($group_name, $group_password);
        DB_Connector_users::insertUser($user_name, $hash, $mail, $user_image, $group_id);
    }
}
