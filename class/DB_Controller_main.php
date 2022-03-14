<?php
require_once('DB_Controller.php');
class DB_Controller_main extends DB_Controller {

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
            $sql = 'INSERT INTO articles(title, memo, payment, payment_at, user_id, type_id, category_id, group_id) VALUES(:title, :memo, :payment, :payment_at, :user_id, :type_id, :category_id, :group_id);';
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
        }
    }
    // レコードの更新
    public function update_a_record($id, $title, $payment, $payment_at, $user_id, $type_id, $category_id, $group_id, $memo = '') {
        if($this->connect_DB()) {
            $stmt = $this->pdo->prepare('UPDATE main SET main(title, memo, payment, payment_at, user_id, type_id, category_id, group_id) VALUES(:title, :memo, :payment, :payment_at, :user_id, :type_id, :category_id, :group_id) WHERE id=:id;');
            //SQL文中の プレース部を 定義しておいた変数に置き換える
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
        }
    }
    // あるグループの全レコードを取り出す * fetch_group_records_to_display に統合予定
    public function fetch_all_group_records($group_id) {
        if($this->connect_DB()) {

            // sql文を定義する。
            $sql = 'SELECT * FROM `full_records` WHERE `group_id`=:group_id;';

            $stmt = $this->pdo->prepare($sql);
            //SQL文中の プレース部を 定義しておいた変数に置き換える
            $stmt->bindParam( ':group_id',   $group_id,   PDO::PARAM_INT);

            //sqlを 実行
            $stmt->execute();

            // $resultにsql実行結果を代入する
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            //print_r($results); 
            $this->pdo = null;
            return $results; //格納されていなければ false を返す
        }
    }

    // あるグループのレコードを一定数取り出す（画面に収まる数など）
    public function fetch_group_records_to_display($group_id, $limit, $offset = 0) {
        if($this->connect_DB()) {

            // sql文を定義する。
            $sql = "SELECT * FROM `full_records` WHERE `group_id`=:group_id order by id desc limit {$limit} offset {$offset};";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam( ':group_id',   $group_id,   PDO::PARAM_INT);
            $stmt->execute();

            // $resultにsql実行結果を代入する
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $this->pdo = null;
            return $results; //格納されていなければ false を返す
        }
    }

    /**********************************************************
     * ダッシュボードで集計を表示するための関数
     **********************************************************/
    // 今までの合計収支を返す ダッシュボードに表示する
    public function group_total_balance($group_id) {
        if($this->connect_DB()) {
            $outgo = $this->group_total_outgo($group_id);
            $income = $this->group_total_income($group_id);

            if($outgo == false) {
                $outgo = 0;
            }
            if($income == false) {
                $income = 0;
            }

            $result = $income - $outgo;

            return $result;
        }
    }
    // 今までの合計支出を返す ダッシュボードに表示する
    public function group_total_outgo($group_id) {
        if($this->connect_DB()) {

            // sql文を定義する。 支出のtype_idは2
            $sql = 'select type_id, sum(`payment`) AS `outgo` from `main` where `group_id` = :group_id and type_id = 2;';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam( ':group_id',   $group_id,   PDO::PARAM_INT);
            $stmt->execute();

            // $resultにsql実行結果を代入する
            $result = $stmt->fetch(PDO::FETCH_ASSOC);        

            $this->pdo = null;
            return $result['outgo']; //格納されていなければ false を返す
        }
    }
    // 今までの合計収入を返す ダッシュボードに表示する
    public function group_total_income($group_id) {
        if($this->connect_DB()) {

            // sql文を定義する。 収入のtype_idは1
            $sql = 'select type_id, sum(`payment`) AS `income` from `main` where `group_id` = :group_id and type_id = 1;';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam( ':group_id',   $group_id,   PDO::PARAM_INT);
            $stmt->execute();

            // $resultにsql実行結果を代入する
            $result = $stmt->fetch(PDO::FETCH_ASSOC);        

            $this->pdo = null;
            return $result['income']; //格納されていなければ false を返す
        }
    }

    // あるグループの月別、週別の支出合計を出力する
    public function get_filtered_outgo($group_id, $target_date = null, $category_id = null, $period_param = 0) {
        if($this->connect_DB()) {
            // 月別、週別の指定
            $period = $this->select_a_period($period_param);

            if(is_null($category_id)) {
                // 期間のみでfilterする場合
                $results = $this->get_filtered_outgo_by_a_date(group_id:$group_id, target_date:$target_date, period_param:$period_param);
            } else {
                // 期間とカテゴリでfilterする場合
                if(is_null($target_date)) {
                    
                    $target_date = 'SYSDATE()';
                }
                $sql = "select sum(payment) as `sum`
                        from main
                        where group_id = :group_id
                        and type_id = 2
                        and category_id = :category_id
                        and {$period}(payment_at) = {$period}({$target_date})
                        and Year(payment_at) = Year({$target_date})";

                $stmt = $this->pdo->prepare($sql);
                $stmt->bindParam( ':group_id',      $group_id,      PDO::PARAM_INT);
                $stmt->bindParam( ':category_id',   $category_id,   PDO::PARAM_INT);
                $stmt->execute();
                $results = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            // $resultにsql実行結果を代入する
            $this->pdo = null;
            return $results['sum']; //格納されていなければ false を返す
        }
    }
    // あるグループの月別、週別の支出合計を出力する * 直接呼び出さない
    private function get_filtered_outgo_by_a_date($group_id, $target_date = null, $period_param = 0) {
        if($this->connect_DB()) {
            // 月別、週別の指定
            $period = $this->select_a_period($period_param);

            if(is_null($target_date)) {
                $target_date = 'SYSDATE()';
            }

            $sql = "select sum(payment) as `sum`
                    from main
                    where group_id = :group_id
                    and type_id = 2
                    and {$period}(payment_at) = {$period}({$target_date})
                    and Year(payment_at) = Year({$target_date})";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam( ':group_id',      $group_id,      PDO::PARAM_INT);
            // var_dump($stmt);
            $stmt->execute();
            
            $results = $stmt->fetch(PDO::FETCH_ASSOC);
            return $results;
        }
    }
    // 月別か週別か期間を選ぶ
    private function select_a_period($period_param = 0) {
        switch ($period_param) {
            case 1:
                // 引数で週を選択しなければ週別。
                $period = "WEEK";
                break;
            
            default:
                // 引数で期間を選択しなければ月別。
                $period = "MONTH";
                break;
        }
        return $period;
    }
}