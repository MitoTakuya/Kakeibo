<?php
require_once('DB_Connector.php');
class DB_Connector_main extends DB_Connector {
    protected static int $outgo_type_id = 1;
    protected static int $income_type_id = 2;

    // 対象テーブルを選択
    function __construct() {
        parent::__construct('main');
    }

    /**************************************************************************
     * mainテーブル操作用のメソッド
     **********************************************************************/
    // $memo は引数を無い場合があるため、デフォルト値として''を設定する。
    // （nullはそのままstringにバインドできないため）
    public function insertRecord($title, $payment, $payment_at, $user_id, $type_id, $category_id, $group_id, $memo = null) {
        if(isset(self::$pdo) || self::connectDB()) {
            try {
                self::$pdo->beginTransaction();

                $sql = 'INSERT INTO `main`(`title`, `memo`, `payment`, `payment_at`, `user_id`, `type_id`, `category_id`, `group_id`)
                        VALUES(:title, :memo, :payment, :payment_at, :user_id, :type_id, :category_id, :group_id);';
            
                $stmt = self::$pdo->prepare($sql);
                $stmt->bindParam( ':title',         $title,         PDO::PARAM_STR);
                $stmt->bindParam( ':memo',          $memo,          PDO::PARAM_STR);
                $stmt->bindParam( ':payment',       $payment,       PDO::PARAM_STR);
                $stmt->bindParam( ':payment_at',    $payment_at,    PDO::PARAM_STR);
                $stmt->bindParam( ':user_id',       $user_id,       PDO::PARAM_INT);
                $stmt->bindParam( ':type_id',       $type_id,       PDO::PARAM_INT);
                $stmt->bindParam( ':category_id',   $category_id,   PDO::PARAM_INT);
                $stmt->bindParam( ':group_id',      $group_id,      PDO::PARAM_INT);

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
    public function updateRecord($id, $title, $payment, $payment_at, $user_id, $type_id, $category_id, $group_id, $memo = null) {
        if(isset(self::$pdo) || self::connectDB()) {
            try {
                self::$pdo->beginTransaction();
                $sql = 'UPDATE `main` SET `title` =:title, `memo` = :memo, `payment` = :payment, `payment_at` = :payment_at, `user_id` = :user_id, `type_id` = :type_id, `category_id` = :category_id, `group_id` = :group_id
                        WHERE `id`=:id;';
                
                $stmt = self::$pdo->prepare($sql);

                $stmt->bindParam( ':id',            $id,            PDO::PARAM_INT);
                $stmt->bindParam( ':title',         $title,         PDO::PARAM_STR);
                $stmt->bindParam( ':memo',          $memo,          PDO::PARAM_STR);
                $stmt->bindParam( ':payment',       $payment,       PDO::PARAM_STR);
                $stmt->bindParam( ':payment_at',    $payment_at,    PDO::PARAM_STR);
                $stmt->bindParam( ':user_id',       $user_id,       PDO::PARAM_INT);
                $stmt->bindParam( ':type_id',       $type_id,       PDO::PARAM_INT);
                $stmt->bindParam( ':category_id',   $category_id,   PDO::PARAM_INT);
                $stmt->bindParam( ':group_id',      $group_id,      PDO::PARAM_INT);

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
    public function fetchGroupRecords($group_id) {
        if(isset(self::$pdo) || self::connectDB()) {
            $sql = 'SELECT *
                    FROM `full_records`
                    WHERE `group_id`=:group_id;';
            
            $stmt = self::$pdo->prepare($sql);
            $stmt->bindParam( ':group_id',   $group_id,   PDO::PARAM_INT);

            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            
            return $results; //格納されていなければ false を返す
        } else {
            return self::$connect_error;
        }
    }

    // あるグループのレコードを一定数取り出す（画面に収まる数など）*要order切り替え
    public function fetchGroupLimitedRecords($group_id, $limit, $offset = 0, $order) {
        if(isset(self::$pdo) || self::connectDB()) {
            $order_clause = $this->selectOrder($order);    // 昇順・降順を選択する

            $sql = "SELECT * FROM `full_records`
                    WHERE `group_id`=:group_id
                    {$order_clause}
                    LIMIT :limit
                    OFFSET :offset;";
            
            $stmt = self::$pdo->prepare($sql);
            $stmt->bindParam( ':group_id',  $group_id,  PDO::PARAM_INT);
            $stmt->bindParam( ':limit',     $limit,     PDO::PARAM_INT);
            $stmt->bindParam( ':offset',    $offset,    PDO::PARAM_INT);
            
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            
            return $results; //格納されていなければ false を返す
        } else {
            return self::$connect_error;
        }
    }

    /**********************************************************
     * ダッシュボードで集計を表示するための関数
     **********************************************************/
    // 今までの合計収支を返す ダッシュボードに表示する
    public function fetchBalance($group_id) {
        $outgo = $this->fetchOutgo($group_id);
        $income = $this->fetchIncome($group_id);

        if(!is_numeric($outgo) || !is_numeric($income)) {
            return self::$connect_error;    // DB接続失敗時の処理
        }
        
        if($outgo == false) {
            $outgo = 0;
        }
        if($income == false) {
            $income = 0;
        }

        $result = $income - $outgo;
        return $result;
    }
    
    // 今までの合計支出を返す ダッシュボードに表示する
    public function fetchOutgo($group_id) {
        if(isset(self::$pdo) || self::connectDB()) {
            $sql = 'SELECT `type_id`, SUM(`payment`) AS `outgo`
                    FROM `main`
                    WHERE `group_id` = :group_id
                    AND `type_id` = :type_id;';
            
            $stmt = self::$pdo->prepare($sql);
            $stmt->bindParam( ':group_id',  $group_id,              PDO::PARAM_INT);
            $stmt->bindParam( ':type_id',   self::$outgo_type_id,   PDO::PARAM_INT);

            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);        

            
            return $result['outgo']; //格納されていなければ false を返す
        } else {
            return self::$connect_error;
        }
    }
    // 今までの合計収入を返す ダッシュボードに表示する
    public function fetchIncome($group_id) {
        if(isset(self::$pdo) || self::connectDB()) {
            $sql = 'SELECT `type_id`, SUM(`payment`) AS `income`
                    FROM `main`
                    WHERE `group_id` = :group_id
                    AND `type_id` = :type_id;';
            
            $stmt = self::$pdo->prepare($sql);
            $stmt->bindParam( ':group_id',  $group_id,              PDO::PARAM_INT);
            $stmt->bindParam( ':type_id',   self::$income_type_id,  PDO::PARAM_INT);

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
    public function fetchFilteredOutgo($group_id, $target_date = null, $category_id = null, $period_param = 0) {
        if(isset(self::$pdo) || self::connectDB()) {
            $period = $this->select_a_period($period_param);    // 月別、週別の指定
            $target_date = $this->select_a_date($target_date);  // 基準になる日付の指定

            if(is_null($category_id)) {
                // 期間のみでfilterする場合
                $results = $this->fetchDateFilteredOutgo(
                                group_id:   $group_id,
                                target_date:$target_date,
                                period:     $period
                            );
            } else {
                // 期間とカテゴリでfilterする場合
                $results = $this->fetchFullyFilteredOutgo(
                                group_id:       $group_id,
                                category_id:    $category_id,
                                target_date:    $target_date,
                                period:         $period
                            );
            }
            
            return $results['sum']; //格納されていなければ false を返す
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
    public function fetchFilteredRecords($group_id, $target_date = null, $category_id = null, $period_param = 0, $order = 0) {
        if(isset(self::$pdo) || self::connectDB()) {
            $order_clause = $this->selectOrder($order);        // 昇順・降順を選択する
            $period = $this->select_a_period($period_param);    // 月別、週別の指定
            $target_date = $this->select_a_date($target_date);  // 基準になる日付の指定

            if(is_null($category_id)) {
                // 期間のみでfilterする場合
                $results = $this->fetchDateFilteredRecords(
                                group_id:       $group_id,
                                target_date:    $target_date,
                                period:         $period,
                                order_clause:   $order_clause
                            );
            } else {
                // 期間とカテゴリでfilterする場合
                $results = $this->fetchFullyFilteredRecords(
                                group_id:       $group_id,
                                category_id:    $category_id,
                                target_date:    $target_date,
                                period:         $period,
                                order_clause:   $order_clause
                            );
            }
            
            return $results; //格納されていなければ false を返す
        } else {
            return self::$connect_error;
        }
    }
    // 1列分の値だけを取り出す
    public function fetchCategoryColumns($order = 1) {
        if(isset(self::$pdo) || self::connectDB()) {
            // 昇順・降順を選択する
            $order_clause = $this->selectOrder($order);

            $sql = "SELECT `type_id`, `category_name`
                    FROM  `categories` " . $order_clause;

            $stmt = self::$pdo->prepare($sql);
            // $stmt->bindParam( ':column', $column, PDO::PARAM_STR);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_COLUMN|PDO::FETCH_GROUP);

            return $results;

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
    private function fetchDateFilteredOutgo($group_id, $target_date = null, $period) {
        $sql = "SELECT sum(payment) AS `sum`
                FROM `main`
                WHERE `group_id` = :group_id
                AND `type_id` = :type_id
                AND {$period}(payment_at) = {$period}({$target_date})
                AND YEAR(payment_at) = YEAR({$target_date})";           //$target_date には関数も入るためバインドしない

        $stmt = self::$pdo->prepare($sql);
        $stmt->bindParam( ':group_id',      $group_id,              PDO::PARAM_INT);
        $stmt->bindParam( ':type_id',       self::$outgo_type_id,   PDO::PARAM_INT);
        // var_dump($stmt);
        $stmt->execute();
        $results = $stmt->fetch(PDO::FETCH_ASSOC);
        return $results;
    }

    // あるグループの月別、週別の、特定カテゴリにおける支出合計を出力する * 直接呼び出さない
    private function fetchFullyFilteredOutgo($group_id, $category_id, $target_date, $period) {
        $sql = "SELECT sum(payment) AS `sum`
                FROM `main`
                WHERE `group_id` = :group_id
                AND `type_id` = :type_id
                AND `category_id` = :category_id
                AND {$period}(payment_at) = {$period}({$target_date})
                AND YEAR(payment_at) = YEAR({$target_date})";           //$target_date には関数も入るためバインドしない

        $stmt = self::$pdo->prepare($sql);
        $stmt->bindParam( ':group_id',      $group_id,              PDO::PARAM_INT);
        $stmt->bindParam( ':category_id',   $category_id,           PDO::PARAM_INT);
        $stmt->bindParam( ':type_id',       self::$outgo_type_id,   PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetch(PDO::FETCH_ASSOC);
        return $results;
    }
    // あるグループの月別、週別のレコードを取り出すメソッド * 直接呼び出さない
    private function fetchDateFilteredRecords($group_id, $target_date = null, $period, $order_clause) {
        $sql = "SELECT *
                FROM `full_records`
                WHERE `group_id` = :group_id
                AND `type_id` = :type_id
                AND {$period}(payment_at) = {$period}({$target_date})
                AND YEAR(payment_at) = YEAR({$target_date})
                {$order_clause}";           //$target_date には関数も入るためバインドしない

        $stmt = self::$pdo->prepare($sql);
        $stmt->bindParam( ':group_id',      $group_id,              PDO::PARAM_INT);
        $stmt->bindParam( ':type_id',       self::$outgo_type_id,   PDO::PARAM_INT);
        // var_dump($stmt);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }

    // あるグループの月別、週別の、特定カテゴリにおけるレコードを取り出すメソッド * 直接呼び出さない
    private function fetchFullyFilteredRecords($group_id, $target_date = null, $category_id, $period, $order_clause) {
        $sql = "SELECT *
                FROM `full_records`
                WHERE `group_id` = :group_id
                AND `type_id` = :type_id
                AND `category_id` = :category_id
                AND {$period}(payment_at) = {$period}({$target_date})
                AND YEAR(payment_at) = YEAR({$target_date})
                {$order_clause}";           //$target_date には関数も入るためバインドしない

        $stmt = self::$pdo->prepare($sql);
        $stmt->bindParam( ':group_id',      $group_id,              PDO::PARAM_INT);
        $stmt->bindParam( ':type_id',       self::$outgo_type_id,   PDO::PARAM_INT);
        $stmt->bindParam( ':category_id',   $category_id,           PDO::PARAM_INT);
        // var_dump($stmt);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }

    // 月別か週別か期間を選ぶ * 直接呼び出さない
    private function select_a_period($period_param = 0) {
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
    private function select_a_date($target_date = null) {
        if(is_null($target_date)){
            $target_date = "NOW()";
        }
        return $target_date;
    }
}