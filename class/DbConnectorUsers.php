<?php
require_once __DIR__ . '/DbConnector.php';

class DbConnectorUsers extends DbConnector
{
    public static $user_errors = array();

    // 対象テーブルを選択
    protected static $target_table = 'users';
    
    // ログイン時メールアドレス検索
    public static function loginUser($mail)
    {
        $stmt = self::$pdo->prepare('SELECT `id`, `password` FROM users WHERE mail=:mail && `is_deleted`!=1');
        //SQL文中の プレース部を 定義しておいた変数に置き換える
        $stmt->bindParam( ':mail', $mail, PDO::PARAM_STR);
        $stmt->execute();
        $user_password = $stmt->fetch();
        // 未登録アドレスならfalse
        return $user_password;;
    }

    // メールアドレス重複確認
    public static function checkDuplicate($mail)
    {
        $stmt = self::$pdo->prepare('SELECT COUNT(mail) as cnt FROM users WHERE mail=:mail && `is_deleted`!=1');
        $stmt->bindParam( ':mail', $mail, PDO::PARAM_STR);
        //sqlを 実行
        $stmt->execute();
        // メールアドレスをカウント
        $record = $stmt->fetch();
        if ($record['cnt'] > 0) {
            return "登録済みのメールアドレスです。";
        }
    }

    // 自分以外のユーザーがアドレスを登録済みかカウント
    public static function checkEditMail($mail, $id)
    {
        $stmt = self::$pdo->prepare('SELECT COUNT(mail) as cnt FROM users WHERE mail=:mail && id != (:id) && `is_deleted`!=1');
        
        $stmt->bindParam( ':mail', $mail, PDO::PARAM_STR);
        $stmt->bindParam( ':id', $id, PDO::PARAM_INT);
        //sqlを 実行
        $stmt->execute();
        // メールアドレスをカウント
        $record = $stmt->fetch();
        var_dump($record );
        if ($record['cnt'] > 0) {
            return "登録済みのメールアドレスです。";
        }
    }

    // ユーザーグループ登録
    public static function insertUserGroup($group_name, $group_password)
    {
        $stmt = self::$pdo->prepare('INSERT INTO `user_groups`(`group_name`, `group_password`) VALUES(:group_name, :group_password);');
        //SQL文中の プレース部を 定義しておいた変数に置き換える
        $stmt->bindParam( ':group_name', $group_name, PDO::PARAM_STR);
        $stmt->bindParam( ':group_password', $group_password, PDO::PARAM_STR);
        //sqlを 実行
        $stmt->execute();
        return self::$pdo->lastInsertId();
    }

    // ユーザーグループ検索
    public static function searchGroupId($group_password)
    {
        $stmt = self::$pdo->prepare('SELECT `id`FROM user_groups WHERE group_password=:group_password');
        $stmt->bindParam( ':group_password', $group_password, PDO::PARAM_STR);
        //sqlを 実行
        $stmt->execute();
        $id = $stmt->fetch();
        return $id; //パスワードが違っていればfalseが返る
    }

    /**************************************************************************
     * userテーブル操作用のメソッド
     **********************************************************************/
    // 
    public static function insertUser($user_name, $password, $mail, $user_image, $group_id)
    {
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
    }

    // ユーザー詳細ページ
    public static function fetchUsersFullRecords($group_id)
    {
        $stmt = self::$pdo->prepare(
            'SELECT users.id as user_id, users.user_name, users.mail, users.password, users.user_image, is_deleted, user_groups.*
            FROM users INNER JOIN user_groups ON users.group_id = user_groups.id where users.group_id = :group_id &&  `is_deleted`!=1');
        $stmt->bindParam(':group_id', $group_id, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }

    // ユーザー情報取得
    public static function fetchUser(int $id)
    {
        $stmt = self::$pdo->prepare('SELECT *
        FROM `users`
        WHERE `id`=:id;');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // クエリ結果が0件の場合、空の配列を返す
        return $row;
    }

    // ユーザー更新
    public static function editUser(string $user_name, string $password, string $mail, string $user_image, int $id)
    {
        try {
            self::$pdo->beginTransaction();
            $stmt = self::$pdo->prepare(
                'UPDATE users SET user_name=:user_name, password=:password, mail=:mail, user_image = :user_image WHERE id=:id');
            $stmt->bindParam('user_name', $user_name, PDO::PARAM_STR);
            $stmt->bindParam('password', $password, PDO::PARAM_STR);
            $stmt->bindParam('mail', $mail, PDO::PARAM_STR);
            $stmt->bindParam('user_image', $user_image, PDO::PARAM_STR);
            $stmt->bindParam('id', $id, PDO::PARAM_INT);
            $stmt->execute();
            self::$pdo->commit();
        } catch (PDOException $e) {
            self::$pdo->rollBack();
            return self::TRANSACTION_ERROR;
        }
    }

    // 論理的削除を行うメソッド
    public static function disableUser(int $target_id)
    {
        $stmt = self::$pdo->prepare('UPDATE `users` SET `is_deleted`=true WHERE id=:id');
        //SQL文中の プレース部を 定義しておいた変数に置き換える
        $stmt->bindParam( ':id', $target_id, PDO::PARAM_INT);
        //sqlを 実行
        $stmt->execute();
    }

    // 論理的削除を取り消すメソッド
    public static function deleteUser($target_id)
    {
        $stmt = self::$pdo->prepare('UPDATE `users` SET `is_deleted`=false WHERE id=:id');
        //SQL文中の プレース部を 定義しておいた変数に置き換える
        $stmt->bindParam( ':id', $target_id, PDO::PARAM_INT);
        //sqlを 実行
        $stmt->execute();
    }

    public static function fetchImage($target_id)
    {
        $stmt = self::$pdo->prepare('UPDATE `users` SET `is_deleted`=false WHERE id=:id');
        //SQL文中の プレース部を 定義しておいた変数に置き換える
        $stmt->bindParam( ':id', $target_id, PDO::PARAM_INT);
        //sqlを 実行
        $stmt->execute();
    }
}
