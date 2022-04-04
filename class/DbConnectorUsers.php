<?php

class DbConnectorUsers extends DbConnector
{
    public static $user_errors = array();

    // 対象テーブルを選択
    protected static $target_table = 'users';
    
    // ログイン時メールアドレス検索
    public static function loginUser(string $mail)
    {
        try {
            // バインドするカラム名をstatic変数に代入する
            self::$temp_to_bind['temp'] = get_defined_vars();
            // where句をつくる
            self::$temp_where_clause = 'WHERE `mail`=:mail AND `is_deleted` = 0';

            // SELECTする対象を一時変数に格納する
            self::$temp_selected_col = "`id`, `password`";

            // PDOメソッドの指定
            $pdo_method = 'pdoFetchAssoc';
            
            // 親クラスのメソッドで結果を取り出す
            self::fetch($pdo_method);
            return self::$temp_result;

        } catch (PDOException $e) {
            throw $e;
        }
    }

    // メールアドレス重複確認
    public static function checkDuplicate(string $mail)
    {
        try {
            // バインドするカラム名をstatic変数に代入する
            self::$temp_to_bind['temp'] = get_defined_vars();
            // where句をつくる
            self::$temp_where_clause = 'WHERE `mail`=:mail AND `is_deleted` = 0';
            // where句とselect対象を指定する
            self::$temp_selected_col = "COUNT(mail) AS cnt";

            // PDOメソッドの指定（一番上のレコードだけを取り出す）
            $pdo_method = 'pdoFetchAssoc';

            // SQL文を実行し、結果を得る
            self::fetch($pdo_method);
            self::$temp_result;
                if (self::$temp_result['cnt'] > 0) {
            return "登録済みのメールアドレスです。";
                }
        } catch (PDOException $e) {
        throw $e;
        }
    }

    // 自分以外のユーザーがアドレスを登録済みかカウント
    public static function checkEditMail(string $mail, int $id)
    {
        try {
            // バインドするカラム名をstatic変数に代入する
            self::$temp_to_bind['temp'] = get_defined_vars();
            // where句をつくる
            self::$temp_where_clause = 'WHERE `mail`=:mail AND `is_deleted` = 0 AND `id` != (:id)';
            // where句とselect対象を指定する
            self::$temp_selected_col = "COUNT(mail) AS cnt";
    
            // PDOメソッドの指定（一番上のレコードだけを取り出す）
            $pdo_method = 'pdoFetchAssoc';
    
            // SQL文を実行し、結果を得る
            self::fetch($pdo_method);
            self::$temp_result;
                if (self::$temp_result['cnt'] > 0) {
            return "登録済みのメールアドレスです。";
            }
    
        } catch (PDOException $e) {
            throw $e;
        }
    }

    // userテーブルのレコードを1つ追加する
    public static function insertUser(
        string $user_name,
        string $password,
        string $mail,
        string $user_image, 
        int $group_id
    ) {
        try {
            // トランザクション開始
            self::$pdo->beginTransaction();

            // 受け取った値に対応するset句を生成する
            self::$temp_to_bind['set'] = get_defined_vars();
            self::makeSetClause();

            // SQL文を実行する
            self::insertOne();

            // トランザクション終了
            self::$pdo->commit();

        } catch (PDOException $e) {
            self::$pdo->rollBack();
            return self::TRANSACTION_ERROR;
        }
    }


    // ユーザー詳細ページ
    public static function fetchUsersFullRecords(int $group_id)
    {
        try {
            // バインドするカラム名をstatic変数に代入する
            self::$temp_to_bind['temp'] = get_defined_vars();
            // where句をつくる
            self::$temp_where_clause = 'WHERE `group_id`=:group_id';

            // SQL文の句を作る
            self::$temp_selected_col = "users.id as user_id, users.user_name, users.mail, users.password, users.user_image, is_deleted, user_groups.*";
            self::$temp_join_clause = 'INNER JOIN user_groups ON users.group_id = user_groups.id';

            // SQL文を実行する
            self::fetch();
            
            // クエリ結果が0件の場合、空の配列を返す
            return self::$temp_result;
        } catch (PDOException $e) {
            // print('Error:'.$e->getMessage());
            throw $e;
        }
    }

    // ユーザー情報取得
    public static function fetchUser(int $id)
    {
        try {
            // バインドするカラム名をstatic変数に代入する
            self::$temp_to_bind['temp'] = get_defined_vars();
            // where句をつくる
            self::$temp_where_clause = 'WHERE `id`=:id';

            // SELECTする対象を一時変数に格納する
            self::$temp_selected_col = "*";

            // PDOメソッドの指定
            $pdo_method = 'pdoFetchAssoc';

            // 親クラスのメソッドで結果を取り出す
            self::fetch($pdo_method);
            return self::$temp_result;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    // ユーザーテーブルのレコードを1つ更新する
    public static function editUser(
        string $user_name,
        string $password,
        string $mail,
        string $user_image,
        int $id
    ) {
        try {
            // トランザクション開始
            self::$pdo->beginTransaction();

            // 受け取った値から不要な値を取り除き、set句を生成する
            self::$temp_to_bind['set'] = self::validateInputs(get_defined_vars());
            self::makeSetClause();

            // SQL文を実行する *エラーが起来た際はrollback()も行う
            self::updateOne();

            // トランザクション終了
            self::$pdo->commit();
        } catch (PDOException $e) {
            self::$pdo->rollBack();
            return self::TRANSACTION_ERROR;
        }
    }

    // 論理的削除を行うメソッド
    public static function disableUser(int $id)
    {
        try {
            // トランザクション開始
            self::$pdo->beginTransaction();

            // バインドするカラム名をstatic変数に代入する
            self::$temp_to_bind['temp'] = get_defined_vars();
            // where句をつくる
            self::$temp_where_clause = 'WHERE `id`=:id';
            // set句をつくる
            self::$temp_set_clause = 'SET `is_deleted`=true';

            // SQL文を実行する *エラーが起来た際はrollback()も行う
            self::updateOne();

            // トランザクション終了
            self::$pdo->commit();
        } catch (PDOException $e) {
            self::$pdo->rollBack();
            return self::TRANSACTION_ERROR;
        }
    }
}
