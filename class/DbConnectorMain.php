<?php
require_once __DIR__ . '/DbConnector.php';
class DbConnectorMain extends DbConnector {

    protected static $target_table = 'main';

    /**************************************************************************
     * mainテーブル操作用のメソッド
     **********************************************************************/
    // mainテーブルのレコードを1つ追加する
    public static function insertRecord(
        $title,
        int $payment, 
        string $payment_at,
        int $user_id,
        int $type_id,
        int $category_id,
        int $group_id,
        string $memo = null
    ) {
        try {
            // トランザクション開始
            self::$pdo->beginTransaction();

            // 受け取った値に対応するset句を生成する
            self::$temp_inputs['set'] = get_defined_vars();
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

    // mainテーブルのレコードを1つ更新する
    public static function updateRecord(
        int $id,
        string $title = null,
        int $payment = null,
        string $payment_at = null,
        int $user_id = null,
        int $type_id = null,
        int $category_id = null,
        int $group_id = null,
        string $memo = null
    ) {
        try {
            // トランザクション開始
            self::$pdo->beginTransaction();

            // 受け取った値に対応するset句を生成する
            self::$temp_inputs['set'] = get_defined_vars();
            self::makeSetClause();

            // SQL文を実行する
            self::updateOne();

            // トランザクション終了
            self::$pdo->commit();

        } catch (PDOException $e) {
            self::$pdo->rollBack();
            return self::TRANSACTION_ERROR;
        }
    }

    // // あるグループのレコードを一定数取り出す（画面に収まる数など *別クラスに移したので削除予定
    // // DbConnector::makeOrderClauserder()で事前にorderby句の設定が必要
    // public static function fetchGroupLimitedRecords(
    //     int $group_id,
    //     int $limit,
    //     int $offset = 0
    // ){
    //     // 受け取った値に対応する一時変数に格納する
    //     self::$temp_inputs['temp'] = get_defined_vars();

    //     // limitoffset句付きのorderby句
    //     self::addLimit();
    //     $orderby_clause = self::$temp_orderby_clause;

    //     // SQL文をセットする
    //     self::$temp_where_clause = "WHERE `group_id`=:group_id";
    //     self::$temp_sql ="SELECT * FROM `full_records`
    //                         WHERE `group_id`=:group_id
    //                         {$orderby_clause};";
        
    //     self::$temp_stmt = self::$pdo->prepare(self::$temp_sql);
    //     self::bind();
    //     self::$temp_stmt->execute();

    //     $results = self::$temp_stmt->fetchAll(PDO::FETCH_ASSOC);

    //     // クエリ結果が0件の場合、空の配列を返す
    //     return $results;
    // }

/**********************************************************
 * ダッシュボードで集計を表示するための関数
 **********************************************************/
    // 今までの合計収支を返す ダッシュボードに表示する
    public static function fetchBalance(int $group_id)
    {
        try {
            self::$pdo->beginTransaction();
            $outgo = self::fetchOutgo($group_id);
            $income = self::fetchIncome($group_id);
            self::$pdo->commit();

            if (!is_numeric($outgo) || !is_numeric($income)) {
                return self::CONNECT_ERROR;    // DB接続失敗時の処理
            }
            
            // 接続に失敗してエラーメッセージが格納されている場合は0を代入する
            if ($outgo == false) {
                $outgo = 0;
            }
            if ($income == false) {
                $income = 0;
            }

            $result = $income - $outgo;
            return $result;
            
        } catch (PDOException $e) {
            self::$pdo->rollBack();
            return self::TRANSACTION_ERROR;
        }
    }

    // 今までの合計支出を返す ダッシュボードに表示する
    public static function fetchOutgo(int $group_id)
    {
        // where句をつくる
        $type_id = self::$outgo_type_id;
        self::$temp_inputs['where'] = get_defined_vars();
        self::makeWhereClause();

        // SELECTする対象を一時変数に格納する
        self::$temp_selected_col = "`type_id`, IFNULL(SUM(`payment`), 0) AS `outgo`";

        // 親クラスのメソッドで結果を取り出す
        $result = self::fetchSome();
        return $result[0]['outgo'];
    }

    // 今までの合計収入を返す ダッシュボードに表示する
    public static function fetchIncome(int $group_id)
    {
        // where句をつくる
        $type_id = self::$income_type_id;
        self::$temp_inputs['where'] = get_defined_vars();
        self::makeWhereClause();

        // SELECTする対象を一時変数に格納する
        self::$temp_selected_col = "`type_id`, IFNULL(SUM(`payment`), 0) AS `income`";

        // 親クラスのメソッドで結果を取り出す
        $result = self::fetchSome();
        return $result[0]['income'];
    }


    // あるグループの月別の特定カテゴリにおける支出合計を出力する
    // カテゴリを指定しない場合は月別の支出合計を出力する
    /*
        使用例：get_filtered_outgo(group_id:1, target_date:'20220301')
        (グループid1番の「2022年3月1日」の合計支出を出力)
    */
    public static function fetchFilteredOutgo(
        int $group_id,
        int $category_id = null,
        ?string $target_date = null
    ) {
        // 受け取った値に対応するwhere句を生成する
        $type_id = self::$outgo_type_id;
        self::$temp_inputs['where'] = get_defined_vars();
        unset(self::$temp_inputs['where']['target_date']);// target_date はwhere句に含めないためunset
        self::makeWhereClause();
        static::addPeriodFilter($target_date); // where句に日時指定を追加

        // select対象を決定する
        self::$temp_selected_col = "IFNULL(SUM(`payment`), 0) AS `sum`";

        // SQL文を実行し、結果を格納する
        $results = self::fetchSome();

        print_r($results);

        return $results[0]['sum']; //格納されていなければ false を返す
    }

    public static function fetchFilteredOutgoList(
        int $group_id,
        ?string $target_date = null,
    ){
        // 受け取った値に対応するwhere句を生成する
        $type_id = self::$outgo_type_id;
        self::$temp_inputs['where'] = get_defined_vars();
        unset(self::$temp_inputs['where']['target_date']);// target_date はwhere句に含めないためunset
        self::makeWhereClause();
        self::addPeriodFilter($target_date);

        // SQL文の句を作る
        self::$temp_where_clause = str_replace('`type_id`', '`main`.`type_id`', self::$temp_where_clause);
        self::$temp_selected_col = "main.`category_id`, categories.category_name,  IFNULL(SUM(`payment`), 0) AS `payment`";
        self::$temp_groupby_clause = "GROUP BY `category_id`";
        self::$temp_join_clause = "JOIN `categories` on `categories`.`id` = `main`.`category_id`";

        // SQL文を実行する
        $results = self::fetchSome();

        return $results;
    }

/**********************************************************
 * 詳細画面で表示するためのレコードを取り出すメソッド
 **********************************************************/
// あるグループの月別、週別の、特定カテゴリにおける支出合計を出力する *要order切り替え
/*
    使用例 : fetch_filtered_records(group_id:1, target_date:'20220301')
    (グループid1番の「2022年3月1日」の月の全レコードを出力)
*/
    public static function fetchFilteredRecords(
        int $group_id,
        int $type_id = null,
        ?string $target_date = null,
        ?string $category_id = null
    ) {
        // 受け取った値に対応するwhere句を生成する
        self::$temp_inputs['where'] = get_defined_vars();
        unset(self::$temp_inputs['where']['target_date']);// target_date はwhere句に含めないためunset
        self::makeWhereClause();
        self::addPeriodFilter($target_date);

        // SQL文をセットする
        $orderby_clause = self::$temp_orderby_clause;
        $where_clause = self::$temp_where_clause;
        self::$temp_sql = "SELECT *
                            FROM `full_records`
                            {$where_clause}
                            {$orderby_clause}";           //$target_date には関数も入るためバインドしない
        
        // バインド後にSQL文を実行し、結果を取得する
        self::$temp_stmt = self::$pdo->prepare(self::$temp_sql);
        self::bind();
        self::$temp_stmt->execute();
        $results = self::$temp_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // クエリ結果が0件で空の配列が返ってきた場合はfalseを返す
        if (count($results) == 0) {
            return false;
        } else {
            return $results;
        }
    }
}
