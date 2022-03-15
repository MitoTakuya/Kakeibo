<?php
class DB_Controller {
    private static string $dsn = 'mysql:dbname=kakeibo_db;host=localhost;charset=utf8';
    private static string $DB_user = 'root';
    private static string $DB_password = '';
    protected static ?PDO $pdo;    //PDO か nullでなければいけない
    protected static string $connect_error = 'データベースへの接続に失敗しました';

    protected String $target_table;
    
    protected bool $is_ready;
    

    // 対象テーブルを選択
    function __construct($target_table) {
        $this->target_table = $target_table;
        self::connect_DB(); //PDOオブジェクトを生成
        // 以下でPDOの設定を行う
        #...
    }

    /****************************************************************************
    * DBに接続するメソッド 接続成功時にtrue を返す。それぞれのメソッドの開始時に呼び出す
    * 切断（= PDO オブジェクトへの null の代入）は下記のそれぞれのメソッドの終わりに行う
    *****************************************************************************/
    public static function connect_DB() {
        if(!isset(self::$pdo)) {
            try{
                self::$pdo = new PDO(self::$dsn, self::$DB_user, self::$DB_password);
                //print('接続に成功しました。<br>');
                return true;
            }catch (PDOException $e){
                print('Error:'.$e->getMessage());
                die();
                return false;
            }
        }
    }

    /**************************************************************************
     * 基本メソッド（idのみで行えるDB操作）
     **********************************************************************/
    // select * from 対象テーブル where = 指定したid
    public function fetch_a_record($target_id) {
        if(isset(self::$pdo) || self::connect_DB()) {
            $sql = "SELECT *
                    FROM `{$this->target_table}`
                    WHERE `id`=:id";
            
            $stmt = self::$pdo->prepare($sql);
            $stmt->bindParam( ':id', $target_id, PDO::PARAM_INT);
            
            $stmt->execute();
            $results = $stmt->fetch(PDO::FETCH_ASSOC);
            
            
            return $results;
        } else {
            // 接続失敗時はstringでエラーメッセージを返す
            return self::$connect_error;
        }
    }

    // select * from 対象テーブル
    public function fetch_all_records($order = 0) {
        if(isset(self::$pdo) || self::connect_DB()) {
            // 昇順・降順を選択する
            $order_clause = $this->select_order($order);

            $sql = "SELECT *
                    FROM  `{$this->target_table}` " . $order_clause;

            $stmt = self::$pdo->prepare($sql);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            
            return $results;

        } else {
            // 接続失敗時はstringでエラーメッセージを返す
            return self::$connect_error;
        }
    }
    // delete from 対象テーブル
    public function delete_a_record($target_id) {
        if(isset(self::$pdo) || self::connect_DB()) {
            $sql = "DELETE FROM
                    ` {$this->target_table} `
                    FROM `id`=:id";
            
            $stmt = self::$pdo->prepare($sql);
            $stmt->bindParam( ':id', $target_id, PDO::PARAM_INT);
            $stmt->execute();

            
        } else {
            // 接続失敗時はstringでエラーメッセージを返す
            return self::$connect_error;
        }
    }
    // DBとの切断処理を行う
    public static function disconnect_DB() {
        self::$pdo = null;
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