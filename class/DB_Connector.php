<?php
class DB_Connector
{
    private const DNS = 'mysql:dbname=kakeibo_db;host=localhost;charset=utf8';
    private const DB_USER = 'root';
    private const DB_PASSWORD = '';
    protected static ?PDO $pdo;    //PDO か nullでなければいけない
    protected static string $connect_error = 'データベースへの接続に失敗しました';
    protected static string $transaction_error = '処理に失敗しました';

    protected String $target_table;
    

    // 対象テーブルを選択
    function __construct(string $target_table)
    {
        $this->target_table = $target_table;
        self::connectDB(); //PDOオブジェクトを生成

        // 以下でPDOの設定を行う
        self::$pdo->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_EMPTY_STRING);   // カラムがnullのままinsertできるように設定
        self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);        // エラー発生時にExceptionを投げるように設定
    }

    /****************************************************************************
    * DBへの接続関連メソッド
    *****************************************************************************/
    // DBとの接続処理を行う (基本的に内部で呼び出す)
    public static function connectDB()
    {
        if (!isset(self::$pdo)) {
            try {
                self::$pdo = new PDO(self::DNS, self::DB_USER, self::DB_PASSWORD);
                //print('接続に成功しました。<br>');
                return true;
            } catch (PDOException $e) {
                // print('Error:'.$e->getMessage());
                die();
                return false;
            }
        }
    }
    // DBとの切断処理を行う
    public static function disconnectDB() {
        self::$pdo = null;
    }

    /**************************************************************************
     * 基本メソッド（idのみで行えるDB操作）
     **********************************************************************/
    // select * from 対象テーブル where = 指定したid
    public function fetchOne(int $target_id)
    {
        if (isset(self::$pdo) || self::connectDB()) {
            $sql = "SELECT *
                    FROM `{$this->target_table}`
                    WHERE `id`=:id";
            
            $stmt = self::$pdo->prepare($sql);
            $stmt->bindParam(':id', $target_id, PDO::PARAM_INT);
            
            $stmt->execute();
            $results = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $results;
        } else {
            // 接続失敗時はstringでエラーメッセージを返す
            return self::$connect_error;
        }
    }

    // select * from 対象テーブル
    public function fetchAll(int $order = 0)
    {
        if (isset(self::$pdo) || self::connectDB()) {
            // 昇順・降順を選択する
            $order_clause = $this->selectOrder($order);

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
    public function deleteOne(int $target_id)
    {
        if (isset(self::$pdo) || self::connectDB()) {
            try {
                self::$pdo->beginTransaction();
                $sql = "DELETE FROM `{$this->target_table}`
                        WHERE `id`=:id";
                
                $stmt = self::$pdo->prepare($sql);
                $stmt->bindParam(':id', $target_id, PDO::PARAM_INT);
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
    //order by句を返す
    protected function selectOrder(int $order = 0, string $culmun = 'id')
    {
        switch ($order) {
            case 1:
                $order_clause = "order by `{$culmun}` asc";
                break;

            default:
                $order_clause = "order by `{$culmun}` desc";
        }
        return $order_clause;
    }
}