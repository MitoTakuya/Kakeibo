<?php
require_once('./DB_Controller.php');
class DB_Controller_main extends DB_Controller {
    // 対象テーブルを選択
    function __construct() {
        parent::__construct('main');
    }

    /**************************************************************************
     * mainテーブル操作用のメソッド
     **********************************************************************/
    // 
    public function insert_an_record($title, $memo, $payment, $payment_at, $user_id, $type_id, $category_id, $group_id) {
        if($this->connect_DB()) {
            $stmt = $this->pdo->prepare('INSERT INTO articles(title, memo, payment, payment_at, user_id, type_id, category_id, group_id) VALUES(:title, :memo, :payment, :payment_at, :user_id, :type_id, :category_id, :group_id);');
            //SQL文中の プレース部を 定義しておいた変数に置き換える
            $stmt->bindParam( ':title', $title, PDO::PARAM_STR);
            $stmt->bindParam( ':memo', $memo, PDO::PARAM_STR);
            $stmt->bindParam( ':payment', $payment, PDO::PARAM_STR);
            $stmt->bindParam( ':payment_at', $payment_at, PDO::PARAM_STR); //仮
            $stmt->bindParam( ':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam( ':type_id', $type_id, PDO::PARAM_INT);
            $stmt->bindParam( ':category_id', $category_id, PDO::PARAM_INT);
            $stmt->bindParam( ':group_id', $group_id, PDO::PARAM_INT);

            //sqlを 実行
            $stmt->execute();
        }
    }
}