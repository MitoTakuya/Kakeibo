<?php
#############################################
# 記帳に関するDB操作を行うクラス
#############################################
require_once('DB_Controller.php');
class DB_Controller_main extends DB_Controller {
    protected static $outgo_type_id = 1;
    protected static $income_type_id = 2;

    // 対象テーブルを選択
    function __construct() {
        parent::__construct('main');
    }

    /**************************************************************************
     * mainテーブル操作用のメソッド
     **********************************************************************/
    // $memo は引数を無い場合があるため、デフォルト値として''を設定する。
    // （nullはそのままstringにバインドできないため）
    public function insert_a_record($title, $payment, $payment_at, $user_id, $type_id, $category_id, $group_id, $memo = '') {
        if($this->connect_DB()) {
            $sql = 'INSERT INTO `main`(`title`, `memo`, `payment`, `payment_at`, `user_id`, `type_id`, `category_id`, `group_id`)
                    VALUES(:title, :memo, :payment, :payment_at, :user_id, :type_id, :category_id, :group_id);';
            
            $stmt = $this->pdo->prepare($sql);
            //SQL文中の プレース部を 定義しておいた変数に置き換える
            $stmt->bindParam( ':title',         $title,         PDO::PARAM_STR);
            $stmt->bindParam( ':memo',          $memo,          PDO::PARAM_STR);
            $stmt->bindParam( ':payment',       $payment,       PDO::PARAM_STR);
            $stmt->bindParam( ':payment_at',    $payment_at,    PDO::PARAM_STR);
            $stmt->bindParam( ':user_id',       $user_id,       PDO::PARAM_INT);
            $stmt->bindParam( ':type_id',       $type_id,       PDO::PARAM_INT);
            $stmt->bindParam( ':category_id',   $category_id,   PDO::PARAM_INT);
            $stmt->bindParam( ':group_id',      $group_id,      PDO::PARAM_INT);

            //sqlを 実行
            $stmt->execute();
            $this->pdo = null;
        } else {
            return self::$connect_error;
        }
    }
    // レコードの更新
    public function update_a_record($id, $title, $payment, $payment_at, $user_id, $type_id, $category_id, $group_id, $memo = '') {
        if($this->connect_DB()) {
            $sql = 'UPDATE `main` SET `title` =:title, `memo` = :memo, `payment` = :payment, `payment_at` = :payment_at, `user_id` = :user_id, `type_id` = :type_id, `category_id` = :category_id, `group_id` = :group_id
                    where `id`=:id;';
            
            $stmt = $this->pdo->prepare($sql);

            $stmt->bindParam( ':id',            $id,            PDO::PARAM_INT);
            $stmt->bindParam( ':title',         $title,         PDO::PARAM_STR);
            $stmt->bindParam( ':memo',          $memo,          PDO::PARAM_STR);
            $stmt->bindParam( ':payment',       $payment,       PDO::PARAM_STR);
            $stmt->bindParam( ':payment_at',    $payment_at,    PDO::PARAM_STR);
            $stmt->bindParam( ':user_id',       $user_id,       PDO::PARAM_INT);
            $stmt->bindParam( ':type_id',       $type_id,       PDO::PARAM_INT);
            $stmt->bindParam( ':category_id',   $category_id,   PDO::PARAM_INT);
            $stmt->bindParam( ':group_id',      $group_id,      PDO::PARAM_INT);

            //sqlを 実行
            $stmt->execute();
            $this->pdo = null;
        } else {
            return self::$connect_error;
        }
    }
    // あるグループの全レコードを取り出す * fetch_group_records_to_display に統合予定
    public function fetch_all_group_records($group_id) {
        if($this->connect_DB()) {

            $sql = 'SELECT * FROM `full_records` WHERE `group_id`=:group_id;';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam( ':group_id',   $group_id,   PDO::PARAM_INT);

            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $this->pdo = null;
            return $results; //格納されていなければ false を返す
        } else {
            return self::$connect_error;
        }
    }

    // あるグループのレコードを一定数取り出す（画面に収まる数など）*要order切り替え
    public function fetch_group_records_to_display($group_id, $limit, $offset = 0, $order) {
        if($this->connect_DB()) {
            // 昇順・降順を選択する
            $order_clause = $this->select_order($order);

            $sql = "SELECT * FROM `full_records` WHERE `group_id`=:group_id {$order_clause} limit :limit offset :offset;";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam( ':group_id',  $group_id,  PDO::PARAM_INT);
            $stmt->bindParam( ':limit',     $limit,     PDO::PARAM_INT);
            $stmt->bindParam( ':offset',    $offset,    PDO::PARAM_INT);
            
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $this->pdo = null;
            return $results; //格納されていなければ false を返す
        } else {
            return self::$connect_error;
        }
    }

    /**********************************************************
     * ダッシュボードで集計を表示するための関数
     **********************************************************/
    // 今までの合計収支を返す ダッシュボードに表示する
    public function group_total_balance($group_id) {
        $outgo = $this->group_total_outgo($group_id);
        $income = $this->group_total_income($group_id);

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
    public function group_total_outgo($group_id) {
        if($this->connect_DB()) {
            $sql = 'select `type_id`, sum(`payment`) AS `outgo` from `main` where `group_id` = :group_id and `type_id` = :type_id;';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam( ':group_id',  $group_id,              PDO::PARAM_INT);
            $stmt->bindParam( ':type_id',   self::$outgo_type_id,   PDO::PARAM_INT);

            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);        

            $this->pdo = null;
            return $result['outgo']; //格納されていなければ false を返す
        } else {
            return self::$connect_error;
        }
    }
    // 今までの合計収入を返す ダッシュボードに表示する
    public function group_total_income($group_id) {
        if($this->connect_DB()) {
            $sql = 'select `type_id`, sum(`payment`) AS `income` from `main` where `group_id` = :group_id and `type_id` = :type_id;';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam( ':group_id',  $group_id,              PDO::PARAM_INT);
            $stmt->bindParam( ':type_id',   self::$income_type_id,  PDO::PARAM_INT);

            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);        

            $this->pdo = null;
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
    public function get_filtered_outgo($group_id, $target_date = null, $category_id = null, $period_param = 0) {
        if($this->connect_DB()) {
            $period = $this->select_a_period($period_param);    // 月別、週別の指定
            $target_date = $this->select_a_date($target_date);  // 基準になる日付の指定

            if(is_null($category_id)) {
                // 期間のみでfilterする場合
                $results = $this->get_filtered_outgo_by_a_date(
                                group_id:   $group_id,
                                target_date:$target_date,
                                period:     $period
                            );
            } else {
                // 期間とカテゴリでfilterする場合
                $results = $this->get_filtered_outgo_by_date_and_category(
                                group_id:       $group_id,
                                category_id:    $category_id,
                                target_date:    $target_date,
                                period:         $period
                            );
            }
            $this->pdo = null;
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
    public function fetch_filtered_records($group_id, $target_date = null, $category_id = null, $period_param = 0) {
        if($this->connect_DB()) {
            $period = $this->select_a_period($period_param);    // 月別、週別の指定
            $target_date = $this->select_a_date($target_date);  // 基準になる日付の指定

            if(is_null($category_id)) {
                // 期間のみでfilterする場合
                $results = $this->fetch_filtered_records_by_a_date(
                                group_id:       $group_id,
                                target_date:    $target_date,
                                period:         $period
                            );
            } else {
                // 期間とカテゴリでfilterする場合
                $results = $this->fetch_filtered_records_by_date_and_category(
                                group_id:       $group_id,
                                category_id:    $category_id,
                                target_date:    $target_date,
                                period:         $period
                            );
            }
            $this->pdo = null;
            return $results; //格納されていなければ false を返す
        } else {
            return self::$connect_error;
        }
    }
    // 1列分の値だけを取り出す
    public function fetch_category_column($order = 1) {
        if(isset(self::$pdo) || self::connect_DB()) {
            // 昇順・降順を選択する
            $order_clause = $this->select_order($order);

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
    private function get_filtered_outgo_by_a_date($group_id, $target_date = null, $period) {
        $sql = "select sum(payment) as `sum`
                from `main`
                where `group_id` = :group_id
                and `type_id` = :type_id
                and {$period}(payment_at) = {$period}({$target_date})
                and Year(payment_at) = Year({$target_date})";           //$target_date には関数も入るためバインドしない

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam( ':group_id',      $group_id,              PDO::PARAM_INT);
        $stmt->bindParam( ':type_id',       self::$outgo_type_id,   PDO::PARAM_INT);
        // var_dump($stmt);
        $stmt->execute();
        $results = $stmt->fetch(PDO::FETCH_ASSOC);
        return $results;
    }

    private function get_filtered_outgo_by_date_and_category($group_id, $category_id, $target_date, $period) {
        $sql = "select sum(payment) as `sum`
                from `main`
                where `group_id` = :group_id
                and `type_id` = :type_id
                and `category_id` = :category_id
                and {$period}(payment_at) = {$period}({$target_date})
                and Year(payment_at) = Year({$target_date})";           //$target_date には関数も入るためバインドしない

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam( ':group_id',      $group_id,              PDO::PARAM_INT);
        $stmt->bindParam( ':category_id',   $category_id,           PDO::PARAM_INT);
        $stmt->bindParam( ':type_id',       self::$outgo_type_id,   PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetch(PDO::FETCH_ASSOC);
        return $results;
    }
    // あるグループの月別、週別のレコードを取り出すメソッド
    private function fetch_filtered_records_by_a_date($group_id, $target_date = null, $period) {
        $sql = "select *
                from `full_records`
                where `group_id` = :group_id
                and `type_id` = :type_id
                and {$period}(payment_at) = {$period}({$target_date})
                and Year(payment_at) = Year({$target_date})";           //$target_date には関数も入るためバインドしない

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam( ':group_id',      $group_id,              PDO::PARAM_INT);
        $stmt->bindParam( ':type_id',       self::$outgo_type_id,   PDO::PARAM_INT);
        // var_dump($stmt);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }

    // あるグループの月別、週別の、特定カテゴリにおけるレコードを取り出すメソッド
    private function fetch_filtered_records_by_date_and_category($group_id, $target_date = null, $category_id, $period) {
        $sql = "select *
                from `full_records`
                where `group_id` = :group_id
                and `type_id` = :type_id
                and `category_id` = :category_id
                and {$period}(payment_at) = {$period}({$target_date})
                and Year(payment_at) = Year({$target_date})";           //$target_date には関数も入るためバインドしない

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam( ':group_id',      $group_id,              PDO::PARAM_INT);
        $stmt->bindParam( ':type_id',       self::$outgo_type_id,   PDO::PARAM_INT);
        $stmt->bindParam( ':category_id',   $category_id,           PDO::PARAM_INT);
        // var_dump($stmt);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }

    // 月別か週別か期間を選ぶ
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

    // 日付が渡されなければ、実行時点の日付を返す。
    private function select_a_date($target_date = null) {
        if(is_null($target_date)){
            $target_date = "NOW()";
        }
        return $target_date;
    }
}