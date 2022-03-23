<?php
require_once dirname(__FILE__) . '/DB_Connector.php';
session_start();

class DB_Connector_users extends DB_Connector
{
    public static $user_errors = array();
    private $group_id = 0;

    // 対象テーブルを選択
    protected static $target_table = 'main';

    public function setGroupId($group_id)
    {
        $this->group_id = $group_id;
    }
    public function getGroupId()
    {
        return $this->group_id;
    }

    // ユーザー登録入力内容チェック
    public static function inputConfirmation()
    {
        if (!isset($_POST['user_name']) || str_replace(array(" ", "　"), "", $_POST['user_name']) === '') {
            self::$user_errors['user_name'] = '名前を入力してください';
        } elseif (mb_strlen($_POST['user_name']) > 30) {
            self::$user_errors['user_name'] = '名前は30文字以内で入力してください';
        } else {
            $user_name = $_POST['user_name'];
        }

        if (trim($_POST['mail']) === "") {
            self::$user_errors['mail'] = "メールアドレスを入力してください";
        } else {
            $mail = $_POST['mail'];
            self::checkDuplicate($mail);
        }

        if (trim($_POST['password']) === "") {
            self::$user_errors['password'] = "パスワードを入力してください";
        } elseif (strlen($_POST['password']) < 4) {
            self::$user_errors['password'] = 'パスワードを4文字以上で入力してください';
        } else {
            $password = $_POST['password'];
        }

        if (!empty($_FILES['user_image']['name'])) {
            $ext = substr($_FILES['user_image']['name'], -3);
            if ($ext != 'jpg' && $ext != 'gif' && $ext != 'png') {
                self::$user_errors['user_image'] = '画像の形式は[jpg],[gif],[png]のみです。';
            }
        } elseif (empty($_FILES['user_image']['name'])) {
            self::$user_errors['user_image'] = '画像を選択してください';
        }

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
                $error = self::searchGroupId($group_password);
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
                self::createUserWithGroup($group_name, $group_password, $user_name, $hash, $mail, $user_image);
            } else {
                // ユーザー登録
                $group_id = self::getGroupId();
                self::insertUser($user_name, $hash, $mail, $user_image, $group_id);
            }
            return "ok";
        } else {
            return self::$user_errors;
        }
    }
    
    // ログイン入力確認
    public static function loginConfirmation()
    {
        // POSTされていないときは処理を中断する
        if (!filter_input_array(INPUT_POST)) {
            return;
        }
        
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
            $user_password = self::loginUser($mail);
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

    protected function redirect($url)
    {
        header('Location: http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']).$url);
    }

    // ユーザー・新規グループ登録
    public static function createUserWithGroup($group_name, $group_password, $user_name, $hash, $mail, $user_image)
    {
        try {
            self::$pdo->beginTransaction();

            self::insertUserGroup($group_name, $group_password);
            $group_id = self::getGroupId();
            self::insertUser($user_name, $hash, $mail, $user_image, $group_id);

            self::$pdo->commit();
        } catch (PDOException $e) {
            self::$pdo->rollBack();
            return self::$transaction_error;
        }
    }

    // ログイン用メソッド
    public static function loginUser($mail)
    {
        if (isset(self::$pdo) || self::connectDB()) {
            $stmt = self::$pdo->prepare('SELECT `id`, `password` FROM users WHERE mail=:mail');
            //SQL文中の プレース部を 定義しておいた変数に置き換える
            $stmt->bindParam( ':mail', $mail, PDO::PARAM_STR);
            $stmt->execute();
            $user_password = $stmt->fetch();
            // 未登録アドレスならfalse
            return $user_password;;
        } else {
            return self::$connect_error;
        }
    }

    // メールアドレス重複確認
    public static function checkDuplicate($mail)
    {
        if (isset(self::$pdo) || self::connectDB()) {
            $stmt = self::$pdo->prepare('SELECT COUNT(mail) as cnt FROM users WHERE mail=:mail');
            $stmt->bindParam( ':mail', $mail, PDO::PARAM_STR);
            //sqlを 実行
            $stmt->execute();
            // メールアドレスをカウント
            $record = $stmt->fetch();
            var_dump($record);
            if ($record['cnt'] > 0) {
                self::$user_errors['mail'] = "登録済みのメールアドレスです。";
            }
        } else {
            return self::$connect_error;
        }
    }

    // 自分以外のユーザーがアドレスを登録済みかカウント
    public static function checkEditMail($mail, $id)
    {
        if (isset(self::$pdo) || self::connectDB()) {
            $stmt = self::$pdo->prepare('SELECT COUNT(mail) as cnt FROM users WHERE mail=:mail AND id NOT IN (id=:id)');
            
            $stmt->bindParam( ':mail', $mail, PDO::PARAM_STR);
            $stmt->bindParam( ':id', $id, PDO::PARAM_INT);
            //sqlを 実行
            $stmt->execute();
            // メールアドレスをカウント
            $record = $stmt->fetch();
            if ($record['cnt'] > 0) {
                self::$user_errors['mail'] = "登録済みのメールアドレスです。";
            }
        } else {
            return self::$connect_error;
        }
    }

    // ユーザーグループ登録
    public static function insertUserGroup($group_name, $group_password)
    {
        if (isset(self::$pdo) || self::connectDB()) {
            $stmt = self::$pdo->prepare('INSERT INTO `user_groups`(`group_name`, `group_password`) VALUES(:group_name, :group_password);');
            //SQL文中の プレース部を 定義しておいた変数に置き換える
            $stmt->bindParam( ':group_name', $group_name, PDO::PARAM_STR);
            $stmt->bindParam( ':group_password', $group_password, PDO::PARAM_STR);
            //sqlを 実行
            $stmt->execute();
            self::setGroupId(self::$pdo->lastInsertId());
        } else {
            return self::$connect_error;
        }
    }

    // ユーザーグループ検索
    public static function searchGroupId($group_password)
    {
        if (isset(self::$pdo) || self::connectDB()) {
            $stmt = self::$pdo->prepare('SELECT `id`FROM user_groups WHERE group_password=:group_password');
            $stmt->bindParam( ':group_password', $group_password, PDO::PARAM_STR);
            //sqlを 実行
            $stmt->execute();
            $id = $stmt->fetch();
            return $id; //パスワードが違っていればfalseが返る
        } else {
            return self::$connect_error;
        }
    }

    /**************************************************************************
     * userテーブル操作用のメソッド
     **********************************************************************/
    // 
    public static function insertUser($user_name, $password, $mail, $user_image, $group_id)
    {
        if (isset(self::$pdo) || self::connectDB()) {
            $stmt = self::$pdo->prepare('INSERT INTO users(user_name, password, mail, user_image, group_id) 
                VALUES(:user_name, :password, :mail, :user_image, :group_id);');
            //SQL文中の プレース部を 定義しておいた変数に置き換える
            $stmt->bindParam( ':user_name', $user_name, PDO::PARAM_STR);
            $stmt->bindParam( ':password', $password, PDO::PARAM_STR);
            $stmt->bindParam( ':mail', $mail, PDO::PARAM_STR);
            $stmt->bindParam( ':user_image', $user_image, PDO::PARAM_STR); 
            $stmt->bindParam( ':group_id', $group_id, PDO::PARAM_INT); 
            //sqlを 実行
            $stmt->execute();
        } else {
            return self::$connect_error;
        }
    }

    // ユーザー詳細ページ
    public static function fetchUsersFullRecords($user_id)
    {
        if (isset(self::$pdo) || self::connectDB()) {

            $stmt = self::$pdo->prepare(
                'SELECT users.id as user_id, users.user_name, users.mail, users.password, users.user_image, is_deleted, user_groups.* FROM users INNER JOIN user_groups ON users.group_id = user_groups.id WHERE users.group_id in ( SELECT group_id FROM users WHERE id=:id)');

            $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $results;
        } else {
            return self::$connect_error;
        }
    }

    // ユーザー情報取得
    public static function fetchUser(int $id)
    {
        if (isset(self::$pdo) || self::connectDB()) {
            $stmt = self::$pdo->prepare('SELECT *
            FROM `users`
            WHERE `id`=:id;');
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // クエリ結果が0件の場合、空の配列を返す
            return $row;
        } else {
            return self::$connect_error;
        }
    }

    // ユーザー更新
    public static function editUser(string $user_name, string $password, string $mail, string $user_image, int $id)
    {
        if (isset(self::$pdo) || self::connectDB()) {
            try {
                self::$pdo->beginTransaction();
                $stmt = self::$pdo->prepare('UPDATE users SET user_name=:user_name, password=:password, mail=:mail, user_image = :user_image WHERE id=:id');
                $stmt->bindParam('user_name', $user_name, PDO::PARAM_STR);
                $stmt->bindParam('password', $password, PDO::PARAM_STR);
                $stmt->bindParam('mail', $mail, PDO::PARAM_STR);
                $stmt->bindParam('user_image', $user_image, PDO::PARAM_STR);
                $stmt->bindParam('id', $id, PDO::PARAM_INT);
                $stmt->execute();
                self::$pdo->commit();
            } catch (PDOException $e) {
                self::$pdo->rollBack();
                return self::$transaction_error;
            }
        } else {
            return self::$connect_error;
        }
    }

    // 論理的削除を行うメソッド
    public static function disableUser($target_id)
    {
        if (isset(self::$pdo) || self::connectDB()) {
            $stmt = self::$pdo->prepare('UPDATE `users` SET `is_deleted`=true WHERE id=:id');
            //SQL文中の プレース部を 定義しておいた変数に置き換える
            $stmt->bindParam( ':id', $target_id, PDO::PARAM_INT);
            //sqlを 実行
            $stmt->execute();
        } else {
            return self::$connect_error;
        }
    }

    // 論理的削除を取り消すメソッド
    public static function deleteUser($target_id)
    {
        if (isset(self::$pdo) || self::connectDB()) {
            $stmt = self::$pdo->prepare('UPDATE `users` SET `is_deleted`=false WHERE id=:id');
            //SQL文中の プレース部を 定義しておいた変数に置き換える
            $stmt->bindParam( ':id', $target_id, PDO::PARAM_INT);
            //sqlを 実行
            $stmt->execute();
        } else {
            return self::$connect_error;
        }
    }

    public static function fetchImage($target_id)
    {
        if (isset(self::$pdo) || self::connectDB()) {
            $stmt = self::$pdo->prepare('UPDATE `users` SET `is_deleted`=false WHERE id=:id');
            //SQL文中の プレース部を 定義しておいた変数に置き換える
            $stmt->bindParam( ':id', $target_id, PDO::PARAM_INT);
            //sqlを 実行
            $stmt->execute();
        } else {
            return self::$connect_error;
        }
    }
}
