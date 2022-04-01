<?php

class DbConnectorUserGroups extends DbConnector
{
    public static $user_errors = array();

    // 対象テーブルを選択
    protected static $target_table = 'user_groups';

    // ユーザーグループ取得
    public static function fetchUserGroup(int $group_id)
    {
        if (isset(self::$pdo) || self::connectDB()) {
            $stmt = self::$pdo->prepare('SELECT *
            FROM `user_groups`
            WHERE `id`=:id;');
            $stmt->bindParam(':id', $group_id, PDO::PARAM_INT);

            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // クエリ結果が0件の場合、空の配列を返す
            return $row;

        } else {
            return self::CONNECT_ERROR;
        }
    }

    // ユーザーグループ編集
    public static function editUserGroup(string $group_name, int $goal, int $id)
    {
        if (isset(self::$pdo) || self::connectDB()) {
            try {
                self::$pdo->beginTransaction();
                $stmt = self::$pdo->prepare('UPDATE user_groups SET group_name=:group_name, goal=:goal WHERE id=:id');
                $stmt->bindParam('group_name', $group_name, PDO::PARAM_STR);
                $stmt->bindParam('goal', $goal, PDO::PARAM_INT);
                $stmt->bindParam('id', $id, PDO::PARAM_INT);
                $stmt->execute();
                self::$pdo->commit();
            } catch (PDOException $e) {
                self::$pdo->rollBack();
                return self::TRANSACTION_ERROR;
            }
        } else {
            return self::CONNECT_ERROR;
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
