<?php
require_once('DB_Connector.php');
class DB_Connector_main extends DB_Connector {

    protected static $target_table = 'main';

    /**************************************************************************
     * mainテーブル操作用のメソッド
     **********************************************************************/
    // $memo は引数を無い場合があるため、デフォルト値として''を設定する。
    // （nullはそのままstringにバインドできないため）
    public static function insertRecord(
        $title,
        int $payment, 
        string $payment_at,
        int $user_id,
        int $type_id,
        int $category_id,
        int $group_id,
        $memo = null
    ) {
        if (isset(self::$pdo) || self::connectDB()) {
            try {
                self::$pdo->beginTransaction();

                $sql = 'INSERT INTO `main`(`title`, `memo`, `payment`, `payment_at`, `user_id`, `type_id`, `category_id`, `group_id`)
                        VALUES(:title, :memo, :payment, :payment_at, :user_id, :type_id, :category_id, :group_id);';
            
                $stmt = self::$pdo->prepare($sql);
                $stmt->bindParam(':title', $title, PDO::PARAM_STR);
                $stmt->bindParam(':memo',$memo, PDO::PARAM_STR);
                $stmt->bindParam(':payment', $payment, PDO::PARAM_STR);
                $stmt->bindParam(':payment_at', $payment_at, PDO::PARAM_STR);
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->bindParam(':type_id', $type_id, PDO::PARAM_INT);
                $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
                $stmt->bindParam(':group_id', $group_id, PDO::PARAM_INT);

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

    // レコードの更新
    public static function updateRecord(
        int $id,
        string $title,
        int $payment,
        string $payment_at,
        int $user_id,
        int $type_id,
        int $category_id,
        int $group_id,
        string $memo = null
    ) {
        if (isset(self::$pdo) || self::connectDB()) {
            try {
                self::$pdo->beginTransaction();
                $sql = 'UPDATE `main`
                        SET `title` =:title, `memo` = :memo, `payment` = :payment, `payment_at` = :payment_at,
                            `user_id` = :user_id, `type_id` = :type_id, `category_id` = :category_id, `group_id` = :group_id
                        WHERE `id`=:id;';
                
                $stmt = self::$pdo->prepare($sql);

                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->bindParam(':title', $title, PDO::PARAM_STR);
                $stmt->bindParam(':memo', $memo, PDO::PARAM_STR);
                $stmt->bindParam(':payment', $payment, PDO::PARAM_STR);
                $stmt->bindParam(':payment_at', $payment_at, PDO::PARAM_STR);
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->bindParam(':type_id', $type_id, PDO::PARAM_INT);
                $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
                $stmt->bindParam(':group_id', $group_id, PDO::PARAM_INT);

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

    // あるグループの全レコードを取り出す * fetch_group_records_to_display に統合予定
    public static function fetchGroupRecords(int $group_id)
    {
        if (isset(self::$pdo) || self::connectDB()) {
            $sql = 'SELECT *
                    FROM `full_records`
                    WHERE `group_id`=:group_id;';

            $stmt = self::$pdo->prepare($sql);
            $stmt->bindParam(':group_id', $group_id, PDO::PARAM_INT);

            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // クエリ結果が0件の場合、空の配列を返す
            return $results;

        } else {
            return self::$connect_error;
        }
    }

    // あるグループのレコードを一定数取り出す（画面に収まる数など
    public static function fetchGroupLimitedRecords(
        int $group_id,
        int $limit,
        int $order = 0,
        int $offset = 0
    ){
        if (isset(self::$pdo) || self::connectDB()) {
            $order_clause = self::selectOrder($order);    // 昇順・降順を選択する

            $sql = "SELECT * FROM `full_records`
                    WHERE `group_id`=:group_id
                    {$order_clause}
                    LIMIT :limit
                    OFFSET :offset;";
            
            $stmt = self::$pdo->prepare($sql);
            $stmt->bindParam(':group_id', $group_id, PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // クエリ結果が0件の場合、空の配列を返す
            return $results;

        } else {
            return self::$connect_error;
        }
    }

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
                return self::$connect_error;    // DB接続失敗時の処理
            }
            
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
            return self::$transaction_error;
        }
    }

    // 今までの合計支出を返す ダッシュボードに表示する
    public static function fetchOutgo(int $group_id)
    {
        if (isset(self::$pdo) || self::connectDB()) {
            $sql = 'SELECT `type_id`, IFNULL(SUM(`payment`), 0) AS `outgo`
                    FROM `main`
                    WHERE `group_id` = :group_id
                    AND `type_id` = :type_id;';
            
            $stmt = self::$pdo->prepare($sql);
            $stmt->bindParam(':group_id', $group_id, PDO::PARAM_INT);
            $stmt->bindParam(':type_id', self::$outgo_type_id, PDO::PARAM_INT);

            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            
            return $result['outgo']; //格納されていなければ false を返す
        } else {
            return self::$connect_error;
        }
    }

    // 今までの合計収入を返す ダッシュボードに表示する
    public static function fetchIncome(int $group_id)
    {
        if (isset(self::$pdo) || self::connectDB()) {
            $sql = 'SELECT `type_id`, IFNULL(SUM(`payment`), 0) AS `income`
                    FROM `main`
                    WHERE `group_id` = :group_id
                    AND `type_id` = :type_id;';
            
            $stmt = self::$pdo->prepare($sql);
            $stmt->bindParam(':group_id', $group_id, PDO::PARAM_INT);
            $stmt->bindParam(':type_id', self::$income_type_id, PDO::PARAM_INT);

            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            
            return $result['income']; //格納されていなければ false を返す
        } else {
            return self::$connect_error;
        }
    }

    // あるグループの月別、週別の、特定カテゴリにおける支出合計を出力する
    /*
        使用例：get_filtered_outgo(group_id:1, target_date:'20220301', period_param:1)
        (グループid1番の「2022年3月1日」の週の合計支出を出力)
    */
    public static function fetchFilteredOutgo(
        int $group_id,
        int $period_param = 0,
        int $category_id = null,
        ?string $target_date = null
    ) {
        if (isset(self::$pdo) || self::connectDB()) {
            // 月別・週別の選択と、その基準日の選択
            $period_filter = self::makePeriodFilter($period_param, $target_date);

            if (is_null($category_id)) {
                // 期間のみでfilterする場合
                $results = self::fetchDateFilteredOutgo(
                                group_id: $group_id,
                                period_filter: $period_filter
                            );
            } else {
                // 期間とカテゴリでfilterする場合
                $results = self::fetchFullyFilteredOutgo(
                                group_id: $group_id,
                                category_id: $category_id,
                                period_filter: $period_filter
                            );
            }

            return $results['sum']; //格納されていなければ false を返す
        } else {
            return self::$connect_error;
        }
    }

    public static function fetchFilteredOutgoList(
        int $group_id,
        int $period_param = 0,
        ?string $target_date = null,
    ){
        if (isset(self::$pdo) || self::connectDB()) {
            // 月別・週別の選択と、その基準日の選択
            $period_filter = self::makePeriodFilter($period_param, $target_date);

            $sql = "SELECT main.`category_id`, categories.category_name,  IFNULL(SUM(`payment`), 0)
                    FROM `main`
                    JOIN `categories` on `categories`.`id` = `main`.`category_id`
                    WHERE `group_id` = :group_id
                    AND {$period_filter}
                    GROUP BY `category_id`";

            $stmt = self::$pdo->prepare($sql);
            
            $stmt->bindParam(':group_id', $group_id, PDO::PARAM_INT);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $results;
        } else {
            return self::$connect_error;
        }
    }

    /**********************************************************
     * 詳細画面で表示するためのレコードを取り出すメソッド
     **********************************************************/
    // あるグループの月別、週別の、特定カテゴリにおける支出合計を出力する *要order切り替え
    /*
        使用例 : fetch_filtered_records(group_id:1, target_date:'20220301', period_param:1)
        (グループid1番の「2022年3月1日」の週の全レコードを出力)
    */
    public static function fetchFilteredRecords(
        int $group_id,
        int $period_param = 0,
        int $order = 0,
        int $type_id = 0,
        ?string $target_date = null,
        ?string $category_id = null
    ) {
        if (isset(self::$pdo) || self::connectDB()) {
            // 昇順・降順を選択する
            $order_clause = self::selectOrder($order);
            // 月別・週別の選択と、その基準日の選択
            $period_filter = self::makePeriodFilter($period_param, $target_date);
            // 支払い区分の指定, 文字列で返す
            $type_filter = self::selectType($type_id);

            if (is_null($category_id)) {
                // 期間のみでfilterする場合
                $results = self::fetchDateFilteredRecords(
                                group_id: $group_id,
                                period_filter: $period_filter,
                                order_clause: $order_clause,
                                type_filter: $type_filter
                            );
            } else {
                // 期間とカテゴリでfilterする場合
                $results = self::fetchFullyFilteredRecords(
                                group_id: $group_id,
                                category_id: $category_id,
                                period_filter: $period_filter,
                                order_clause: $order_clause,
                                type_filter: $type_filter
                            );
            }
            
            return $results; //格納されていなければ false を返す
        } else {
            return self::$connect_error;
        }
    }

    // 1列分の値だけを取り出す 
    public static function fetchCategoryColumns(int $order = 1) {
        if (isset(self::$pdo) || self::connectDB()) {
            // 昇順・降順を選択する
            $order_clause = self::selectOrder($order);

            $sql = "SELECT `type_id`, `category_name`
                    FROM  `categories` " . $order_clause;

            $stmt = self::$pdo->prepare($sql);
            // $stmt->bindParam(':column', $column, PDO::PARAM_STR);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_COLUMN|PDO::FETCH_GROUP);


            // クエリ結果が0件で空の配列が返ってきた場合はfalseを返す
            if (count($results) == 0) {
                return false;
            } else {
                return $results;
            }

        } else {
            // 接続失敗時はstringでエラーメッセージを返す
            return self::$connect_error;
        }
    }

    /*****************************************
     * メソッド内部からのみ呼び出されるメソッド
     * DB切断は呼び出し元メソッドで行う
     *******************************************/
    // あるグループの月別、週別の支出合計を出力する * 直接呼び出さない
    private static function fetchDateFilteredOutgo(
        int $group_id,
        string $period_filter
    ){
        $sql = "SELECT IFNULL(SUM(`payment`), 0) AS `sum`
                FROM `main`
                WHERE `group_id` = :group_id
                AND `type_id` = :type_id
                AND {$period_filter}";
                //$target_date には関数も入るためバインドしない

        $stmt = self::$pdo->prepare($sql);
        $stmt->bindParam(':group_id', $group_id, PDO::PARAM_INT);
        $stmt->bindParam(':type_id', self::$outgo_type_id, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetch(PDO::FETCH_ASSOC);
        return $results;
    }

    // あるグループの月別、週別の、特定カテゴリにおける支出合計を出力する * 直接呼び出さない
    private static function fetchFullyFilteredOutgo(
        int $group_id,
        int $category_id,
        string $period_filter
    ) {
        $sql = "SELECT IFNULL(SUM(`payment`), 0) AS `sum`
                FROM `main`
                WHERE `group_id` = :group_id
                AND `type_id` = :type_id
                AND `category_id` = :category_id
                AND {$period_filter}";
                //$target_date には関数も入るためバインドしない

        $stmt = self::$pdo->prepare($sql);
        $stmt->bindParam(':group_id', $group_id, PDO::PARAM_INT);
        $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        $stmt->bindParam(':type_id', self::$outgo_type_id, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // クエリ結果が0件で空の配列が返ってきた場合はfalseを返す
        return $results;
    }

    // あるグループの月別、週別のレコードを取り出すメソッド * 直接呼び出さない
    private static function fetchDateFilteredRecords(
        int $group_id,
        string $type_filter,
        string $order_clause,
        string $period_filter
    ) {
        $sql = "SELECT *
                FROM `full_records`
                WHERE `group_id` = :group_id
                AND {$type_filter}
                AND {$period_filter}
                {$order_clause}";           //$target_date には関数も入るためバインドしない
        $stmt = self::$pdo->prepare($sql);
        $stmt->bindParam(':group_id', $group_id, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // クエリ結果が0件の場合、空の配列を返す
        return $results;
    }

    // あるグループの月別、週別の、特定カテゴリにおけるレコードを取り出すメソッド * 直接呼び出さない
    private static function fetchFullyFilteredRecords(
        int $group_id,
        int $category_id,
        string $type_filter,
        string $order_clause,
        string $period_filter
    ) {
        $sql = "SELECT *
                FROM `full_records`
                WHERE `group_id` = :group_id
                AND {$type_filter}
                AND `category_id` = :category_id
                AND {$period_filter}
                {$order_clause}";           //$target_date には関数も入るためバインドしない

        $stmt = self::$pdo->prepare($sql);
        $stmt->bindParam(':group_id', $group_id, PDO::PARAM_INT);
        $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        // var_dump($stmt);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        
        // クエリ結果が0件の場合、空の配列を返す
        return $results;
    }

    // 月別か週別か期間を選ぶ * 直接呼び出さない
    private static function selectPeriod(int $period_param = 0)
    {
        switch ($period_param) {
            case 1:
                $period = "WEEK";
                break;
            
            default:
                // 引数で期間を選択しなければ月別。
                $period = "MONTH";
                break;
        }
        return $period;
    }

    // 日付が渡されなければ、実行時点の日付を返す。 * 直接呼び出さない
    private static function selectDate(?string $target_date = null)
    {
        if (is_null($target_date)) {
            $target_date = "NOW()";
        }
        return $target_date;
    }

    // 期間選択のための句を返す
    private static function makePeriodFilter(?int $period_param = 0, ?string $target_date = null)
    {
        $period = self::selectPeriod($period_param);
        $target_date = self::selectDate($target_date);
        return "{$period}(payment_at) = {$period}({$target_date})
                AND YEAR(payment_at) = YEAR({$target_date})";
    }

    // 「`type_id` IN」の文字列を返す
    private static function selectType(int $type_id = null)
    {
        // type_id IN($type) に入れる文字列を返す
        switch ($type_id) {
            case self::$outgo_type_id:
                $type_id = (string) self::$outgo_type_id;
                break;
            
            case self::$income_type_id:
                $type_id = (string) self::$income_type_id;
                break;
            
            default:
                $type_id = self::$outgo_type_id . "," . self::$income_type_id;
                break;
        }
        $type_filter = "`type_id` IN({$type_id})";
        return $type_filter;
    }
}
