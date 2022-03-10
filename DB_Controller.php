<?php
class DB_Controller {
    private static $dsn = 'mysql:dbname=kakeibo_DB;host=localhost';
    private static $DB_user = 'root';
    private static $DB_password = '';

    private String $sql;
    private String $target_table;
    private $pdo;
    

    // 対象テーブルを選択
    function __construct($target_table) {
        $this->target_table = $target_table;
    }

    // DBに接続するメソッド 接続成功時にtrue を返す
    // 切断（= PDO オブジェクトへの null の代入）は下記のそれぞれのメソッドの終わりに行う
    public function connect_DB() {
        try{
            $this->pdo = new PDO(self::$dsn, self::$DB_user, self::$DB_password);
            //print('接続に成功しました。<br>');
            $this->pdo->query('SET NAMES utf8');  //DB に文字コードを指定するSQL文を送る
            return true;
        }catch (PDOException $e){
            print('Error:'.$e->getMessage());
            die();
            return false;
        }
    }

    /**************************************************************************
     * 基本メソッド（idのみで行えるDB操作）
     **********************************************************************/
    // select * from 対象テーブル where = 指定したid
    public function fetch_a_record($target_id) {
        if($this->connect_DB()) {
            $sql = 'select * from ' . $this->target_table . ' where id=:id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam( ':id', $target_id, PDO::PARAM_INT);
            //sqlを 実行
            $stmt->execute();

            //クエリ結果を格納する
            $results = $stmt->fetch(PDO::FETCH_ASSOC);
            $pdo = null;
            return $results;
        }
    }

    // select * from 対象テーブル
    public function fetch_all_records() {
        if($this->connect_DB()) {
            //echo $this->target_table;
            $sql = 'select * from ' . $this->target_table . ' order by id desc';
            foreach ($this->pdo->query($sql) as $row) {
                $results[] = $row;
            }
            $pdo = null;
            return $results;
        }
    }
    // delete from 対象テーブル
    public function delete_a_record($target_id) {
        if($this->connect_DB()) {
            $sql = 'delete from ' . $this->target_table . ' where id=:id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam( ':id', $target_id, PDO::PARAM_INT);
            $stmt->execute();
            $pdo = null;
        }
    }
}