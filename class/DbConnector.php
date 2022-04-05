<?php
abstract class DbConnector
{
    private const DNS = 'mysql:dbname=kakeibo_db;host=localhost;charset=utf8';
    private const DB_USER = 'root';
    private const DB_PASSWORD = '';
    protected static ?PDO $pdo;    //PDO か nullでなければならない

    // 対象テーブル
    protected static $target_table = null;

/***** 一時格納用の$temp_変数 ***************************************************
 * 一度 $temp_変数 にSQLを分割して格納し、DbConnector::fetch()でそれらを
 * 文字列結合して実行する。
 * 実行後、すべての$temp_変数は下記の値に初期化される。
*******************************************************************************/
    /*----- $temp_to_bind について ---------------------------------------------------------------------------/
    | バインドしたい値を配列で代入するとself::bind()で一括でbindされる
    | $temp_to_bind[temp] = array('カラム名' => 値)の形で代入していく
    | $temp_to_bind[where] = array('カラム名' => 値) の形で代入しておくと where句を作れる makeWhereClause()参照
    | $temp_to_bind[set] = array('カラム名' => 値) の形で代入しておくと set句を作れる makeSetClause()参照
    /--------------------------------------------------------------------------------------------------------*/
    protected static $temp_to_bind = null;

    protected static $temp_sql = null;              // 使用するSQL文を格納する
    protected static $temp_stmt = null;             // PDOStatementを格納する
    protected static $temp_selected_col = " * ";    // select対象のカラムを文字列で格納する
    protected static $temp_set_clause = null;       // set句を格納する      e.g. SET `title` =:title, `memo` = :memo,...)
    protected static $temp_where_clause = null;     // where句を格納する    e.g. WHERE `title` =:title AND `memo` = :memo ... AND type_id IN(1,2)
    protected static $temp_orderby_clause = null;   // orderby句を格納する
    protected static $temp_groupby_clause = null;   // groupby句を格納する
    protected static $temp_join_clause = null;      // join句を格納する

    // クエリ結果を格納する Controllerへはこれをreturnする
    protected static $temp_result = null;

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
                throw $e;
            }
        }
    }

    // DBとの切断処理を行う
    public static function disconnectDB() {
        self::$pdo = null;
    }

/*******************************************************************************
 * DB操作メソッド
 *******************************************************************************/
    // idを指定してレコードを1つ取り出すメソッド
    public static function fetchOne(int $target_id)
    {
        try {
            // SQL文をセットする
            $target_table = static::$target_table;
            self::$temp_sql ="SELECT * FROM `{$target_table}`
                                WHERE `id`=:id";
            
            // SQL文をバインド・実行し、結果を取得する
            self::$temp_stmt = self::$pdo->prepare(self::$temp_sql);
            self::$temp_stmt->bindParam(':id', $target_id, PDO::PARAM_INT);
            self::$temp_stmt->execute();

            self::pdoFetchAssoc();

            // 一時変数を初期化する
            self::resetTemps();
            
            return self::$temp_result;
        } catch (PDOException $e) {
            // print('Error:'.$e->getMessage());
            throw $e;
        }
    }

    // 一時変数$temp_~ に格納したSQL文の句同士を文字列結合して実行し、
    // self::$temp_result にクエリ結果を格納する
    // 一時変数に格納したSQL文の句同士を文字列結合する
    // bind()で$temp_to_bindに含まれている要素すべてにPDO::bindValue()をかける
    protected static function fetch($pdo_fetch_method = "pdoFetchAllAssoc")
    {
        try {
            $selected_col = self::$temp_selected_col;
            $target_table = static::$target_table;
            $where_clause = self::$temp_where_clause;
            $orderby_clause = self::$temp_orderby_clause;
            $groupby_clause = self::$temp_groupby_clause;
            $join_clause = self::$temp_join_clause;

            // 上記のSQL文の句同士を結合する
            self::$temp_sql ="SELECT {$selected_col}
                                FROM `{$target_table}` {$join_clause}
                                {$where_clause}
                                {$orderby_clause}
                                {$groupby_clause}";
            self::$temp_stmt = self::$pdo->prepare(self::$temp_sql);
            // echo self::$temp_sql."<br>";

            // $temp_to_bindに含まれている要素すべてにPDO::bindValue()をかける
            self::bind();

            // SQL文を実行する
            self::$temp_stmt->execute();

            // PDO::fetch系のメソッドを可変関数で呼び出す
            static::$pdo_fetch_method();

            // 一時変数を初期化する
            self::resetTemps();

        } catch (PDOException $e) {
            self::resetTemps();
            // print('Error:'.$e->getMessage());
            throw $e;
        }
    }

    // idを指定してレコードを1つ削除するメソッド
    public static function deleteOne(int $target_id)
    {
        try {
            self::$pdo->beginTransaction();

            // SQL文をセットする
            $target_table = static::$target_table;
            self::$temp_sql = "DELETE FROM `{$target_table}`
                                WHERE `id`=:id";
            self::$temp_stmt = self::$pdo->prepare(self::$temp_sql);
            
            // バインド後にSQL文を実行する
            self::$temp_stmt->bindParam(':id', $target_id, PDO::PARAM_INT);
            self::$temp_stmt->execute();

            self::$pdo->commit();

            // 一時変数を初期化する
            self::resetTemps();

        } catch (PDOException $e) {
            self::resetTemps();
            self::$pdo->rollBack();
            throw $e;
        }
    }

    // 子クラスで生成したset句を使ってinsert set文を実行するメソッド
    protected static function insertOne() {
        try {
            // SQL文をセットする
            $target_table = static::$target_table;
            $set_clause = self::$temp_set_clause;
            
            self::$temp_sql = "INSERT INTO `{$target_table}` {$set_clause};";
            self::$temp_stmt = self::$pdo->prepare(self::$temp_sql);

            // バインド後、insert文を実行する
            // echo self::$temp_sql;
            self::bind();
            self::$temp_stmt->execute();

            // 一時変数を初期化する
            self::resetTemps();

        } catch (PDOException $e) {
            self::resetTemps();
            // print('Error:'.$e->getMessage());
            self::$pdo->rollBack();
            throw $e;
        }
    }

    // 子クラスで生成したset句を使って update文を実行するメソッド
    protected static function updateOne() {
        try {
            // SQL文をセットする
            $target_table = static::$target_table;
            $set_clause = self::$temp_set_clause;
            self::$temp_sql = "UPDATE `{$target_table}` {$set_clause} WHERE id=:id;";
            self::$temp_stmt = self::$pdo->prepare(self::$temp_sql);

            // バインド後、insert文を実行する
            self::bind();
            self::$temp_stmt->execute();

            // 一時変数を初期化する
            self::resetTemps();
        } catch (PDOException $e) {
            self::resetTemps();
            // print('Error:'.$e->getMessage());
            self::$pdo->rollBack();
            throw $e;
        }
    }

/*******************************************************************************
 * SQL文を組み立てるメソッド
 *******************************************************************************/
    /*----- SQL文のWHERE句を組み立てる---------------------------------------------------/
    | self::$temp_to_bind[where] = array('カラム名' => 値, 'カラム名2' => 値2, ...) から
    | "WHERE `カラム名`=:カラム名 AND `カラム名2`=:カラム名2 AND ... `カラム名n`=:カラム名n"
    | の形でwhere句を作り、self::$temp_where_clauseに代入する
    | self::fetch()でSQL文に文字列結合される
    | **「=」以外を使う場合は self::$temp_where_clause に適宜追記する必要がある
    /---------------------------------------------------------------------------------*/
    protected static function makeWhereClause()
    {
        // 条件式を格納するための一時変数を用意する
        $temp_clause = '';

        // 入力された「[~id] => 1,...」の配列から、「`~id`=:~id AND `~id`=:~id,...」の形の文字列をつくる
        $i = 0;
        foreach(self::$temp_to_bind['where'] as $key => $input) {
            // 句の頭以外を「AND」で区切る
            if ($i > 0) {
                $temp_clause .= ' AND ';
            }
            // echo "{$key} => {$input}";
            $temp_clause .= "`{$key}`=:{$key}";
            $i++;
        }
        // where句をstatic変数に格納する
        if (strpos(self::$temp_where_clause, 'WHERE')) {
            self::$temp_where_clause .= ' AND ';
        } else {
            self::$temp_where_clause = ' WHERE ';
        }
        if ($temp_clause !== '') {
            self::$temp_where_clause .= $temp_clause;
        }
    }
    
    /*----- SQL文のSET句を組み立てる ---------------------------------------------------/
    | self::$temp_to_bind[set] = array('カラム名' => 値, 'カラム名2' => 値2, ...) から
    | "set `カラム名`=:カラム名, `カラム名2`=:カラム名2, ... `カラム名n`=:カラム名n"
    | の形でwhere句を作り、self::$temp_where_clauseに代入する
    | self::fetch()でSQL文に文字列結合される
    /---------------------------------------------------------------------------------*/
    protected static function makeSetClause()
    {
        // 条件式を初期化する
        $temp_clause = '';

        // // 入力された「[~id] => 1,...」の配列から、「`~id`=:~id, `~id`=:~id,...」の形の文字列をつくる
        $i = 0;
        foreach(self::$temp_to_bind['set'] as $key => $input) {
            // 句の頭以外を「,」で区切る
            if ($i > 0) {
                $temp_clause .= ', ';
            }
            // echo "{$key} => {$input}";
            $temp_clause .= "`{$key}`=:{$key} ";
            $i++;
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
    public static function makeOrderClause(bool $desc = false, string $column = 'id')
    {
        if ($desc) {
            self::$temp_orderby_clause = "ORDER BY `{$column}` DESC";
        } else {
            self::$temp_orderby_clause = "ORDER BY `{$column}` ASC";
        }
    }

    // orderby句にlimit offset句を付与するメソッド
    protected static function addLimit()
    {
        $limit = " LIMIT :limit ";
        $offset = " OFFSET :offset ";
        if (self::$temp_orderby_clause === '') {
            self::$temp_orderby_clause = " ORDER BY id ASC".$limit.$offset;
        } else {
            self::$temp_orderby_clause .= $limit.$offset;
        }
    }

    // where句に月で絞るためのキーワードをself::$temp_where_clauseに付け足す
    protected static function addPeriodFilter(?string $target_date = null, $column = "payment_at")
    {
        if (is_null($target_date) || strlen($target_date) !== 8) {
            $target_date = "NOW()";
        }
        $period = " MONTH({$column}) = MONTH({$target_date})
                    AND YEAR({$column}) = YEAR({$target_date})";

        if (is_null(self::$temp_where_clause) || self::$temp_where_clause === '') {
            // WHEREが無ければ付け足す
            self::$temp_where_clause = " WHERE ".$period;
        } else {
            self::$temp_where_clause .= " AND ".$period;
        }
    }

    // 受け取った配列から不要な要素と、値がnullの要素を取り除く
    protected static function validateInputs($array)
    {
        // target_dateキー 使わないためunset
        unset($array['target_date']);

        // 値がnullの
        $remove_null = function($vars)
        {
            return ($vars <> null);
        };
        $result = array_filter($array, $remove_null);
        
        return $result;
    }

/*******************************************************************************
 * 結合したSQL文に対して、
 * $temp_to_bindの各要素を取り出してbindValue()を一括で行うメソッド
 *******************************************************************************/
    protected static function bind()
    {
        // 一時変数に格納されている、引数として受け取った値をforeachで回す
        if (!is_null(self::$temp_to_bind)) {
            // print_r(self::$temp_to_bind);
            foreach (self::$temp_to_bind as $inputs) {
                foreach($inputs as $column => $input) {
                    // echo "<br>{$column} := {$input}<br>";
                    if (is_int($input)) {
                        self::$temp_stmt->bindValue($column, $input, PDO::PARAM_INT);
                    } else {
                        self::$temp_stmt->bindValue($column, $input, PDO::PARAM_STR);
                    }
                }
            }
        }
    }

/*******************************************************************************
 * PDOのfetch系メソッドでクエリ結果を取り出す
 * 取り出したクエリ結果はself::$temp_resultに代入される
 * DbConnecter::fetch()内で、可変関数から呼び出される
 *******************************************************************************/
    protected static function pdoFetchAllAssoc()
    {
        self::$temp_result = self::$temp_stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    protected static function pdoFetchAssoc()
    {
        self::$temp_result = self::$temp_stmt->fetch(PDO::FETCH_ASSOC);
    }

    protected static function pdoFetchColGr()
    {
        self::$temp_result = self::$temp_stmt->fetchAll(PDO::FETCH_COLUMN|PDO::FETCH_GROUP);
    }

/*******************************************************************************
 * SQL文実行に使った一時変数をすべて初期化する
 * self::fetch()を実行した後、不要になったSQL文の削除に使う
 *******************************************************************************/
    protected static function resetTemps()
    {
        self::$temp_to_bind = null;
        self::$temp_sql = null;
        self::$temp_stmt = null;
        self::$temp_selected_col = " * ";
        self::$temp_set_clause = null;
        self::$temp_where_clause = null;
        self::$temp_orderby_clause = null;
        self::$temp_groupby_clause = null;
        self::$temp_join_clause = null;
    }
}