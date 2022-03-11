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
    // 今までの合計支出を返す
    public function calculate_group_total_balance($group_id) {
        if($this->connect_DB()) {

            // sql文を定義する。
            $sql = 'select type_id, sum(`payment`) AS `sum` from `main` where `group_id` = :group_id GROUP BY type_id;';

            $stmt = $this->pdo->prepare($sql);
            //SQL文中の プレース部を 定義しておいた変数に置き換える
            $stmt->bindParam( ':group_id',   $group_id,   PDO::PARAM_INT);

            //sqlを 実行
            $stmt->execute();

            // $resultにsql実行結果を代入する
            $query_result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // [0]['sum] = 収入合計、[1]['sum'] = 支出合計
            $result = $query_result[0]['sum'] - $query_result[1]['sum'];

            $this->pdo = null;
            return $result; //格納されていなければ false を返す
        }
    }
    // グループごとの合計支出を計算する
    public function fetch_group_records($group_id) {
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

    // カテゴリでfilterする条件式を生成する 
    public function filter_by_a_category($group_id, $category_id) {
        if($this->connect_DB()) {
            $sql = 'SELECT * FROM `full_records` WHERE `group_id`=:group_id AND `category_id`=:category_id;';
            $stmt = $this->pdo->prepare($sql);
            //SQL文中の プレース部を 定義しておいた変数に置き換える
            $stmt->bindParam( ':group_id',      $group_id,      PDO::PARAM_INT);
            $stmt->bindParam( ':category_id',   $category_id,   PDO::PARAM_INT);

            //sqlを 実行
            $stmt->execute();

            // $resultにsql実行結果を代入する
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            //print_r($results); 
            $this->pdo = null;
            return $results; //格納されていなければ false を返す
        }
    }
    public function test($group_id)
    {
        if($this->connect_DB()) {
            $term = '\'MM\'';
            $sql = 'SELECT TRUNCATE(`payment_at`,' . $term . ') FROM `full_records` WHERE `group_id`=:group_id ';
            $stmt = $this->pdo->prepare($sql);
            //SQL文中の プレース部を 定義しておいた変数に置き換える
            $stmt->bindParam( ':group_id',      $group_id,      PDO::PARAM_INT);

            //sqlを 実行
            $stmt->execute();

            // $resultにsql実行結果を代入する
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            //print_r($results); 
            $this->pdo = null;
            return $results; //格納されていなければ false を返す
        }
    }
}