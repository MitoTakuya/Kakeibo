<?php
require_once __DIR__ . '/DbConnector.php';
class DbConnectorMain extends DbConnector {

    protected static $target_table = 'main';

    /**************************************************************************
     * mainテーブル操作用のメソッド
     **********************************************************************/
    // mainテーブルのレコードを1つ追加する
    public static function insertRecord(
        string $title,
        int $payment, 
        string $payment_at,
        int $user_id,
        int $type_id,
        int $category_id,
        int $group_id,
        string $memo = null
    ) {
        try {
            // トランザクション開始
            self::$pdo->beginTransaction();

            // 受け取った値に対応するset句を生成する
            self::$temp_inputs['set'] = get_defined_vars();
            self::makeSetClause();

            // SQL文を実行する
            self::insertOne();

            // トランザクション終了
            self::$pdo->commit();

        } catch (PDOException $e) {
            self::$pdo->rollBack();
            return self::TRANSACTION_ERROR;
        }
    }

    // mainテーブルのレコードを1つ更新する
    public static function updateRecord(
        int $id,
        string $title = null,
        int $payment = null,
        string $payment_at = null,
        int $user_id = null,
        int $type_id = null,
        int $category_id = null,
        int $group_id = null,
        string $memo = null
    ) {
        try {
            // トランザクション開始
            self::$pdo->beginTransaction();

            // 受け取った値から不要な値を取り除き、set句を生成する
            self::$temp_inputs['set'] = self::validateInputs(get_defined_vars());
            self::makeSetClause();

            // SQL文を実行する *エラーが起来た際はrollback()も行う
            self::updateOne();

            // トランザクション終了
            self::$pdo->commit();

        } catch (PDOException $e) {
            // self::$pdo->rollBack();
            throw $e;
        }
    }

/**********************************************************
 * ダッシュボードで集計を表示するための関数
 **********************************************************/
    // 今までの合計収支を返す ダッシュボードに表示する
    public static function fetchBalance(int $group_id)
    {
        try {
            self::$pdo->beginTransaction();
            $outgo = self::fetchOutgo($group_id);
            $income = self::fetchIncome($group_id);
            self::$pdo->commit();

            if (!is_numeric($outgo) || !is_numeric($income)) {
                return self::CONNECT_ERROR;    // DB接続失敗時の処理
            }
            
            // 接続に失敗してエラーメッセージが格納されている場合は0を代入する
            if ($outgo == false) {
                $outgo = 0;
            }
            if ($income == false) {
                $income = 0;
            }

            $result = $income - $outgo;
            return $result;
            
        } catch (PDOException $e) {
            self::$pdo->rollBack();
            throw $e;
        }
    }

    // 今までの合計支出を返す ダッシュボードに表示する
    public static function fetchOutgo(int $group_id)
    {
        try {
            // where句をつくる
            $type_id = self::$outgo_type_id;
            self::$temp_inputs['where'] = get_defined_vars();
            self::makeWhereClause();

            // SELECTする対象を一時変数に格納する
            self::$temp_selected_col = "`type_id`, IFNULL(SUM(`payment`), 0) AS `outgo`";

            // PDOメソッドの指定
            $pdo_method = 'pdoFetchAssoc';
            
            // 親クラスのメソッドで結果を取り出す
            self::fetch($pdo_method);
            return self::$temp_result['outgo'];

        } catch (PDOException $e) {
            // print('Error:'.$e->getMessage());
            throw $e;
        }
    }

    // 今までの合計収入を返す ダッシュボードに表示する
    public static function fetchIncome(int $group_id)
    {
        try {
            // where句をつくる
            $type_id = self::$income_type_id;
            self::$temp_inputs['where'] = get_defined_vars();
            self::makeWhereClause();

            // SELECTする対象を一時変数に格納する
            self::$temp_selected_col = "`type_id`, IFNULL(SUM(`payment`), 0) AS `income`";

            // PDOメソッドの指定
            $pdo_method = 'pdoFetchAssoc';
            
            // 親クラスのメソッドで結果を取り出す
            self::fetch($pdo_method);
            return self::$temp_result['income'];

        } catch (PDOException $e) {
            // print('Error:'.$e->getMessage());
            throw $e;
        }
    }


    // あるグループの月別の特定カテゴリにおける支出合計を出力する
    // カテゴリを指定しない場合は月別の支出合計を出力する
    /*
        使用例：get_filtered_outgo(group_id:1, target_date:'20220301')
        (グループid1番の「2022年3月1日」の合計支出を出力)
    */
    public static function fetchFilteredOutgo(
        int $group_id,
        int $category_id = null,
        ?string $target_date = null
    ) {
        try {
            // 受け取った値から不要な値を取り除き、where句を生成する
            $type_id = self::$outgo_type_id;
            self::$temp_inputs['where'] = self::validateInputs(get_defined_vars());
            self::makeWhereClause();
            static::addPeriodFilter($target_date); // where句に日時指定を追加

            // select対象を決定する
            self::$temp_selected_col = "IFNULL(SUM(`payment`), 0) AS `sum`";

            // PDOメソッドの指定
            $pdo_method = 'pdoFetchAssoc';

            // SQL文を実行し、結果を格納する
            self::fetch($pdo_method);
            return self::$temp_result['sum']; //格納されていなければ false を返す

        } catch (PDOException $e) {
            // print('Error:'.$e->getMessage());
            throw $e;
        }
    }

    public static function fetchCategorizedList(
        int $group_id,
        int $type_id = null,
        ?string $target_date = null
    ){
        try {
            // 受け取った値に対応するwhere句を生成する
            self::$temp_inputs['where'] = self::validateInputs(get_defined_vars());
            self::makeWhereClause();
            self::addPeriodFilter($target_date);

            // SQL文の句を作る
            self::$temp_where_clause = str_replace('`type_id`', '`main`.`type_id`', self::$temp_where_clause);
            self::$temp_selected_col = "main.`category_id`, categories.category_name,  IFNULL(SUM(`payment`), 0) AS `payment`";
            self::$temp_groupby_clause = "GROUP BY `category_id`";
            self::$temp_join_clause = "JOIN `categories` on `categories`.`id` = `main`.`category_id`";

            // SQL文を実行する
            self::fetch();
            return self::$temp_result;

        } catch (PDOException $e) {
            // print('Error:'.$e->getMessage());
            throw $e;
        }
    }

    public static function fetchCategories(int $group_id)
    {
        try {
            // 受け取った値に対応するwhere句を生成する
            self::$temp_inputs['where'] = get_defined_vars();
            self::makeWhereClause();
            self::makeOrderClause(desc: false, column: 'main`.`category_id');
            self::$temp_where_clause = str_replace('`group_id`', '`main`.`group_id`', self::$temp_where_clause);

            // SQL文の句を作る
            self::$temp_selected_col = "DISTINCT main.`category_id`, categories.category_name ";
            self::$temp_join_clause = "JOIN `categories` on `categories`.`id` = `main`.`category_id`";

            // SQL文を実行する
            self::fetch();
            return self::$temp_result;

        } catch (PDOException $e) {
            // print('Error:'.$e->getMessage());
            throw $e;
        }
    }

    // 支払い日付が最も古いレコードidの記入日付を返す
    // 昇順に並び替えて、一番初めのレコードだけ取得する
    public static function fetchOldestDate(int $group_id)
    {
        try {
            // バインド対象を一時変数に格納に格納する
            self::$temp_inputs['temp']['group_id'] = $group_id;

            // where句とselect対象を指定する
            self::$temp_where_clause = "WHERE group_id = :group_id";
            self::$temp_selected_col = "id, payment_at ";

            // 購入日付・昇順に設定
            self::makeOrderClause(column: 'payment_at');

            // PDOメソッドの指定
            $pdo_method = 'pdoFetchAssoc';

            // SQL文を実行し、結果を得る
            self::fetch($pdo_method);
            return self::$temp_result['payment_at'];

        } catch (PDOException $e) {
            // print('Error:'.$e->getMessage());
            throw $e;
        }
    }

    // グループのレコード数を返す
    public static function countRecords(int $group_id)
    {
        try {
            // バインド対象を一時変数に格納に格納する
            self::$temp_inputs['temp']['group_id'] = $group_id;

            // where句とselect対象を指定する
            self::$temp_where_clause = "WHERE group_id = :group_id";
            self::$temp_selected_col = "COUNT(*) AS records";

            // PDOメソッドの指定（一番上のレコードだけを取り出す）
            $pdo_method = 'pdoFetchAssoc';

            // SQL文を実行し、結果を得る
            self::fetch($pdo_method);
            return self::$temp_result['records'];

        }catch (PDOException $e){
            throw $e;
        }
    }
}
