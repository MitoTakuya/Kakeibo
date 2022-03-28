<?php
abstract class DbConnector
{
    private const DNS = 'mysql:dbname=kakeibo_db;host=localhost;charset=utf8';
    private const DB_USER = 'root';
    private const DB_PASSWORD = '';
    protected static ?PDO $pdo;    //PDO か nullでなければならない

    // 対象テーブル
    protected static $target_table = null;

    // 一時格納用変数(関数間で共通してデータを扱えるようにするため)
    protected static $temp_inputs = null;           // 入力値を格納する
    protected static $temp_sql = null;              // 使用するSQL文を格納する
    protected static $temp_stmt = null;             // PDOStatementを格納する
    protected static $temp_set_clause = null;       // set句を格納する      e.g. SET `title` =:title, `memo` = :memo,...)
    protected static $temp_where_clause = null;     // where句を格納する    e.g. WHERE `title` =:title AND `memo` = :memo ... AND type_id IN(1,2)

    // テーブル操作に使う変数
    protected static int $outgo_type_id = 1;
    protected static int $income_type_id = 2;

    // エラーメッセージ定数
    public const CONNECT_ERROR = 'データベースへの接続に失敗しました';
    public const TRANSACTION_ERROR = '処理に失敗しました';

    /****************************************************************************
    * DBへの接続関連メソッド
    *****************************************************************************/
    // DBとの接続処理を行う (基本的に内部で呼び出す)
    public static function connectDB()
    {
        if (!isset(self::$pdo)) {
            try {
                self::$pdo = new PDO(self::DNS, self::DB_USER, self::DB_PASSWORD);
                // 以下でPDOの設定を行う
                self::$pdo->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_EMPTY_STRING);   // カラムがnullのままinsertできるように設定
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);        // エラー発生時にExceptionを投げるように設定

                //print('接続に成功しました。<br>');
                return true;
            } catch (PDOException $e) {
                // print('Error:'.$e->getMessage());
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
    public static function fetchOne(int $target_id)
    {
            // SQL文をセットする
            $target_table = static::$target_table;
            static::$temp_sql = "SELECT *
                            FROM `{$target_table}`
                            WHERE `id`=:id";
            
            // SQL文をバインド・実行し、結果を取得する
            static::$temp_stmt = self::$pdo->prepare(static::$temp_sql);
            static::$temp_stmt->bindParam(':id', $target_id, PDO::PARAM_INT);
            static::$temp_stmt->execute();
            $results = static::$temp_stmt->fetch(PDO::FETCH_ASSOC);

            // 一時変数を初期化する
            static::resetTempVars();
            
            return $results;
    }

    // select * from 対象テーブル
    public static function fetchAll(int $order = 0)
    {
            // SQL文をセットする
            $target_table = static::$target_table;
            $order_clause = self::selectOrder($order);  // 昇順・降順を選択する
            static::$temp_sql = "SELECT *
                                FROM  `{$target_table}` " . $order_clause;

            static::$temp_stmt = self::$pdo->prepare(static::$temp_sql);
            static::$temp_stmt->execute();
            $results = static::$temp_stmt->fetchAll(PDO::FETCH_ASSOC);

            return $results;
    }
    // delete from 対象テーブル
    public static function deleteOne(int $target_id)
    {
        try {
            $target_table = static::$target_table;
            self::$pdo->beginTransaction();
            static::$temp_sql = "DELETE FROM `{$target_table}`
                    WHERE `id`=:id";
            
            static::$temp_stmt = self::$pdo->prepare(static::$temp_sql);
            static::$temp_stmt->bindParam(':id', $target_id, PDO::PARAM_INT);
            static::$temp_stmt->execute();
            self::$pdo->commit();

        } catch (PDOException $e) {
            self::$pdo->rollBack();
            return self::TRANSACTION_ERROR;
        }
    }
    //order by句を返す
    protected static function selectOrder(int $order = 0, string $culmun = 'id')
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
    // SQL文実行に使った一時変数をすべて初期化する
    protected static function resetTempVars()
    {
        static::$temp_inputs = null;
        static::$temp_sql = null;
        static::$temp_stmt = null;
        static::$temp_set_clause = null;
        static::$temp_where_clause = null;
    }
}