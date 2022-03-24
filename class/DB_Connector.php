<?php
abstract class DB_Connector
{
    private const DNS = 'mysql:dbname=kakeibo_db;host=localhost;charset=utf8';
    private const DB_USER = 'root';
    private const DB_PASSWORD = '';
    protected static ?PDO $pdo;    //PDO か nullでなければいけない

    // 対象テーブル
    protected static $target_table = null;

    // テーブル操作に使う変数
    protected static int $outgo_type_id = 1;
    protected static int $income_type_id = 2;

    // エラーメッセージ
    protected static string $connect_error = 'データベースへの接続に失敗しました';
    protected static string $transaction_error = '処理に失敗しました';

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
            $target_table = static::$target_table;

            $sql = "SELECT *
                    FROM `{$target_table}`
                    WHERE `id`=:id";
            
            $stmt = self::$pdo->prepare($sql);
            $stmt->bindParam(':id', $target_id, PDO::PARAM_INT);
            echo $sql;
            $stmt->execute();
            $results = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $results;
    }

    // select * from 対象テーブル
    public static function fetchAll(int $order = 0)
    {
            $target_table = static::$target_table;
            // 昇順・降順を選択する
            $order_clause = self::selectOrder($order);

            $sql = "SELECT *
                    FROM  `{$target_table}` " . $order_clause;

            $stmt = self::$pdo->prepare($sql);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $results;
    }
    // delete from 対象テーブル
    public static function deleteOne(int $target_id)
    {
        try {
            $target_table = static::$target_table;
            self::$pdo->beginTransaction();
            $sql = "DELETE FROM `{$target_table}`
                    WHERE `id`=:id";
            
            $stmt = self::$pdo->prepare($sql);
            $stmt->bindParam(':id', $target_id, PDO::PARAM_INT);
            $stmt->execute();
            self::$pdo->commit();

        } catch (PDOException $e) {
            self::$pdo->rollBack();
            return self::$transaction_error;
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
}