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
    protected static $temp_selected_col = null;     // select対象のカラムを文字列で格納する
    protected static $temp_set_clause = null;       // set句を格納する      e.g. SET `title` =:title, `memo` = :memo,...)
    protected static $temp_where_clause = null;     // where句を格納する    e.g. WHERE `title` =:title AND `memo` = :memo ... AND type_id IN(1,2)
    protected static $temp_orderby_clause = null;   // orderby句を格納する

    // テーブル操作に使う変数
    protected static int $outgo_type_id = 1;
    protected static int $income_type_id = 2;

    // エラーメッセージ定数
    public const CONNECT_ERROR = 'データベースへの接続に失敗しました';
    public const TRANSACTION_ERROR = '処理に失敗しました';

/*******************************************************************************
* DBへの接続関連メソッド
*******************************************************************************/
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

/*******************************************************************************
 * 基本メソッド（idのみで行えるDB操作）
 *******************************************************************************/

    // idを指定してレコードを1つ取り出すメソッド
    public static function fetchOne(int $target_id)
    {
            // SQL文をセットする
            $target_table = static::$target_table;
            static::$temp_sql ="SELECT *
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

    // where句の条件を満たすレコードをすべて取得する
    // where句に指定できる条件は「WHERE xxx=:xxx AND yyy=:yyy ...」の形のみ
    public static function fetchAll(
        int $id = null,
        string $title = null,
        int $payment = null, 
        string $payment_at = null,
        int $user_id = null,
        int $type_id = null,
        int $category_id = null,
        int $group_id = null,
    ){
        // 受け取った値に対応するwhere句を生成する
        static::$temp_inputs['where'] = get_defined_vars();
        static::makeWhereClause();

        // SQL文をセットする
        $target_table = static::$target_table;
        $where_clause = static::$temp_where_clause;
        $orderby_clause = static::$temp_orderby_clause;

        static::$temp_sql ="SELECT *
                            FROM `{$target_table}`
                            {$where_clause}
                            {$orderby_clause}";
        static::$temp_stmt = self::$pdo->prepare(static::$temp_sql);

        // バインド後にSQL文を実行し、結果を取得する
        static::bind();
        static::$temp_stmt->execute();
        $results = static::$temp_stmt->fetchALL(PDO::FETCH_ASSOC);
        
        return $results;
    }

    // where句の条件を満たすレコードをすべて取得する
    // where句に指定できる条件は「WHERE xxx=:xxx AND yyy=:yyy ...」の形のみ
    protected static function fetchPaticularCol(
        int $id = null,
        string $title = null,
        int $payment = null, 
        string $payment_at = null,
        int $user_id = null,
        int $type_id = null,
        int $category_id = null,
        int $group_id = null,
    ){
        // 受け取った値に対応するwhere句を生成する
        static::$temp_inputs['where'] = get_defined_vars();
        static::makeWhereClause();

        // SQL文をセットする
        $selected_col = static::$temp_selected_col;
        $target_table = static::$target_table;
        $where_clause = static::$temp_where_clause;
        $orderby_clause = static::$temp_orderby_clause;

        static::$temp_sql ="SELECT {$selected_col}
                            FROM `{$target_table}`
                            {$where_clause}
                            {$orderby_clause}";
        static::$temp_stmt = self::$pdo->prepare(static::$temp_sql);

        // バインド後にSQL文を実行し、結果を取得する
        static::bind();
        static::$temp_stmt->execute();
        $results = static::$temp_stmt->fetchALL(PDO::FETCH_ASSOC);
        
        return $results;
    }

    // idを指定してレコードを1つ削除するメソッド
    public static function deleteOne(int $target_id)
    {
        try {
            self::$pdo->beginTransaction();

            // SQL文をセットする
            $target_table = static::$target_table;
            static::$temp_sql ="DELETE FROM `{$target_table}`
                                WHERE `id`=:id";
            static::$temp_stmt = self::$pdo->prepare(static::$temp_sql);
            
            // バインド後にSQL文を実行する
            static::$temp_stmt->bindParam(':id', $target_id, PDO::PARAM_INT);
            static::$temp_stmt->execute();

            self::$pdo->commit();

        } catch (PDOException $e) {
            self::$pdo->rollBack();
            return self::TRANSACTION_ERROR;
        }
    }

    // SQL文のWHERE句を組み立てる
    protected static function makeWhereClause()
    {
        // 条件式を初期化する
        $temp_clause = '';
        // 条件格納用の空の配列を作る
        $filters = array();

        // 入力された「[~id] => 1,...」の配列から、「[0] => `~id`=:~id,...」の形の配列$filtersをつくる
        foreach(static::$temp_inputs['where'] as $key => $input) {
            if (!is_null($input)) {
                // echo "{$key} => {$input}";
                $filters[] = "`{$key}`=:{$key}";
            }
        }
        // print_r($filters);

        // $filters に格納された値を文字列結合し、「WHERE `~id`=:~id,...」の形の文字列をつくる
        if (count($filters) > 0) {
            for($i = 0; $i < count($filters); $i++) {
                $temp_clause .= $filters[$i];
                if ($i < count($filters) - 1) {
                    $temp_clause .= ' AND ';
                }
            }
        }
        if ($temp_clause === '') {
            static::$temp_where_clause = null;
        } else {
            static::$temp_where_clause = 'WHERE ' . $temp_clause;
        }
    }
    
    // SQL文のSET句を組み立てる
    protected static function makeSetClause()
    {
        // 条件式を初期化する
        $temp_clause = '';
        // 条件格納用の空の配列を作る
        $filters = array();

        // 入力された「[~id] => 1,...」の配列から、「[0] => `~id`=:~id,...」の形の配列$filtersをつくる
        foreach(self::$temp_inputs['set'] as $key => $input) {
            if (!is_null($input)) {
                // echo "{$key} => {$input}";
                $filters[] = "`{$key}`=:{$key}";
            }
        }

        // $filters に格納された値を文字列結合し、「WHERE `~id`=:~id,...」の形の文字列をつくる
        if (count($filters) > 0) {
            for($i = 0; $i < count($filters); $i++) {
                $temp_clause .= $filters[$i];
                if ($i < count($filters) - 1) {
                    $temp_clause .= ', ';
                }
            }
        }
        // set句をstatic変数に格納する
        if ($temp_clause === '') {
            self::$temp_set_clause = null;
        } else {
            self::$temp_set_clause = 'SET ' . $temp_clause;
        }
    }

    // orderby句の基準にするカラムと、並び順（ascかdescか）を指定するメソッド
    // SQL実行メソッドを呼び出す前に、コントローラー側で実行する
    public static function selectOrder(bool $desc = false, string $culmun = 'id')
    {
        if ($desc) {
            static::$temp_orderby_clause = "order by `{$culmun}` desc";
        }
        static::$temp_orderby_clause = "order by `{$culmun}` asc";
    }

    // PDOStatement->bindValue()を一括で行うメソッド
    protected static function bind()
    {
        // 一時変数に格納されている、引数として受け取った値をforeachで回す
        foreach (static::$temp_inputs as $inputs) {
            foreach($inputs as $column => $input) {
                if (!is_null($input)) {
                    // echo "{$key} := {$input}<br>";
                    if (is_int($input)) {
                        static::$temp_stmt->bindValue($column, $input, PDO::PARAM_INT);
                    } else {
                        static::$temp_stmt->bindValue($column, $input, PDO::PARAM_STR);
                    }
                }
            }
        }
    }

    // SQL文実行に使った一時変数をすべて初期化する
    protected static function resetTempVars()
    {
        static::$temp_inputs = null;
        static::$temp_sql = null;
        static::$temp_stmt = null;
        static::$temp_selected_col = null;
        static::$temp_set_clause = null;
        static::$temp_where_clause = null;
        static::$temp_orderby_clause = null;
    }
}