<?php
class DB_Controller {
    private static $dsn = 'mysql:dbname=kakeibo_db;host=localhost';
    private static $DB_user = 'root';
    private static $DB_password = '';
    protected static $connect_error = 'データベースへの接続に失敗しました';

    protected String $target_table;
    protected $pdo;
    

    // 対象テーブルを選択
    function __construct($target_table) {
        $this->target_table = $target_table;
    }

    /****************************************************************************
    * DBに接続するメソッド 接続成功時にtrue を返す。それぞれのメソッドの開始時に呼び出す
    * 切断（= PDO オブジェクトへの null の代入）は下記のそれぞれのメソッドの終わりに行う
    *****************************************************************************/
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
            $sql = "select * from `{$this->target_table}` where `id`=:id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam( ':id', $target_id, PDO::PARAM_INT);
            
            $stmt->execute();
            $results = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $this->pdo = null;
            return $results;
        } else {
            // 接続失敗時はstringでエラーメッセージを返す
            return self::$connect_error;
        }
    }

    // select * from 対象テーブル
    public function fetch_all_records($order = 0) {
        if($this->connect_DB()) {
            // 昇順・降順を選択する
            $order_clause = $this->select_order($order);

            $sql = "select * from  `{$this->target_table}` " . $order_clause;
            $stmt = $this->pdo->prepare($sql);

            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $this->pdo = null;
            return $results;

        } else {
            // 接続失敗時はstringでエラーメッセージを返す
            return self::$connect_error;
        }
    }
    // delete from 対象テーブル
    public function delete_a_record($target_id) {
        if($this->connect_DB()) {
            $sql = "delete from ` {$this->target_table} ` where `id`=:id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam( ':id', $target_id, PDO::PARAM_INT);
            $stmt->execute();

            $this->pdo = null;
        } else {
            // 接続失敗時はstringでエラーメッセージを返す
            return self::$connect_error;
        }
    }
    //order by句を返す
    protected function select_order($order = 0, $culmun = 'id') {
        switch($order){
            case 1:
                $order_clause = "order by `{$culmun}` asc";
                break;

            default:
                $order_clause = "order by `{$culmun}` desc";
        }
        return $order_clause;
    }
}