<?php
require_once dirname(__FILE__) . '/DB_Connector.php';
session_start();

class DB_Connector_users extends DB_Connector
{
    public static $user_errors = array();


    // 対象テーブルを選択
    protected static $target_table = 'users';
    
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
            if ($record['cnt'] > 0) {
                return "登録済みのメールアドレスです。";
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
            // self::setGroupId(self::$pdo->lastInsertId());
            return $group_id = self::$pdo->lastInsertId();
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
