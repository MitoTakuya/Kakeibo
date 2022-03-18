<?php
require_once dirname(__FILE__) . '/DB_Connector.php';
session_start();

class DB_Connector_users extends DB_Connector
{
    public static $user_errors = array();
    private $group_id = 0;

    // 対象テーブルを選択
    function __construct()
    {
        parent::__construct('user_groups');
    }

    // ユーザーグループ編集
    public static function editUserGroup(
        string $group_name,
        string $group_password,
        int $goal,
        int $group_id
    )
    {
        if (isset(self::$pdo) || self::connectDB()) {
            try {
                self::$pdo->beginTransaction();
                $stmt = self::$pdo->prepare('UPDATE user_groups SET group_name=:group_name, group_password=:group_password, goal=:goal WHERE group_id=:group_id');
                $stmt->bindParam('group_name', $group_name, PDO::PARAM_STR);
                $stmt->bindParam('group_password', $group_password, PDO::PARAM_STR);
                $stmt->bindParam('goal', $goal, PDO::PARAM_INT);
                $stmt->bindParam('group_id', $group_id, PDO::PARAM_INT);
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


    // ログイン用メソッド
    public function loginUser($mail)
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
    public function checkDuplicate($mail)
    {
        if (isset(self::$pdo) || self::connectDB()) {
            $stmt = self::$pdo->prepare('SELECT COUNT(mail) as cnt FROM users WHERE mail=:mail');
            $stmt->bindParam( ':mail', $mail, PDO::PARAM_STR);
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
    public function insertUserGroup($group_name, $group_password)
    {
        if (isset(self::$pdo) || self::connectDB()) {
            $stmt = self::$pdo->prepare('INSERT INTO `user_groups`(`group_name`, `group_password`) VALUES(:group_name, :group_password);');
            //SQL文中の プレース部を 定義しておいた変数に置き換える
            $stmt->bindParam( ':group_name', $group_name, PDO::PARAM_STR);
            $stmt->bindParam( ':group_password', $group_password, PDO::PARAM_STR);
            //sqlを 実行
            $stmt->execute();
            $this->setGroupId(self::$pdo->lastInsertId());
        } else {
            return self::$connect_error;
        }
    }


    /**************************************************************************
     * userテーブル操作用のメソッド
     **********************************************************************/
    // 
    public function insertUser($user_name, $password, $mail, $user_image, $group_id)
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
    public function fetchUsersFullRecords($user_id) {
        if (isset(self::$pdo) || self::connectDB()) {

            $stmt = self::$pdo->prepare(
                'SELECT users.id as user_id, users.user_name, users.password, users.user_image, is_deleted, user_groups.* FROM users INNER JOIN user_groups ON users.group_id = user_groups.id WHERE users.group_id in ( SELECT group_id FROM users WHERE id=:id)');

            $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $results;
        }
    }



    // 論理的削除を行うメソッド
    public function disableUser($target_id)
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
    public function deleteUser($target_id)
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

    public function fetchImage($target_id)
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
