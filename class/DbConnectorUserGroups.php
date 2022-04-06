<?php

class DbConnectorUserGroups extends DbConnector
{
    public static $user_errors = array();

    // 対象テーブルを選択
    protected static $target_table = 'user_groups';

    // ユーザーグループ取得
    public static function fetchUserGroup(int $id)
    {
        try {
            // バインドするカラム名をstatic変数に代入する
            self::$temp_to_bind['temp'] = get_defined_vars();
            // where句をつくる
            self::$temp_where_clause = 'WHERE `id`=:id';

            // PDOメソッドの指定
            $pdo_method = 'pdoFetchAssoc';

            // 親クラスのメソッドで結果を取り出す
            self::fetch($pdo_method);
            return self::$temp_result;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    // ユーザーグループ検索
    public static function searchGroupId(string $group_password)
    {
        try {
            // バインドするカラム名をstatic変数に代入する
            self::$temp_to_bind['temp'] = get_defined_vars();
            // where句をつくる
            self::$temp_where_clause = 'WHERE `group_password`=:group_password';

            // SELECTする対象を一時変数に格納する
            self::$temp_selected_col = "`id`";

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

    // ユーザーグループ編集
    public static function editUserGroup(
        string $group_name,
        int $goal,
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


    // ユーザーグループ登録
    public static function insertUserGroup(
        string $group_name,
        string $group_password
    ) {
        try {
            // 受け取った値に対応するset句を生成する
            self::$temp_to_bind['set'] = get_defined_vars();
            self::makeSetClause();

            // SQL文を実行する
            self::insertOne();

            // ユーザーグループIDを返す
            return self::$pdo->lastInsertId();
        } catch (PDOException $e) {
            self::$pdo->rollBack();
            return self::TRANSACTION_ERROR;
        }
    }

    // ユーザーグループの目標貯金額を返す
    public static function fetchGoal($id)
    {
        // バインド対象を一時変数に格納に格納する
        self::$temp_to_bind['temp']['id'] = $id;

        // where句とselect対象を指定する
        self::$temp_where_clause = "WHERE id = :id";
        self::$temp_selected_col = "`goal` ";

        // PDOメソッドの指定
        $pdo_method = 'pdoFetchAssoc';

        // SQL文を実行し、結果を得る
        self::fetch($pdo_method);
        return self::$temp_result['goal'];
    }
}
