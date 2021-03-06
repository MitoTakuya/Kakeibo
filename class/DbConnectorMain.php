<?php
class DbConnectorMain extends DbConnector
{
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
            self::$temp_to_bind['set'] = get_defined_vars();
            self::makeSetClause();

            // SQL文を実行する
            self::insertOne();

            // トランザクション終了
            self::$pdo->commit();
        } catch (PDOException $e) {
            // *rollback()はinsertOne()内で行う
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
            self::$temp_to_bind['set'] = self::validateInputs(get_defined_vars());
            self::makeSetClause();

            // SQL文を実行する
            self::updateOne();

            // トランザクション終了
            self::$pdo->commit();
        } catch (PDOException $e) {
            // *rollback()はupdateOne()内で行う
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
            throw $e;
        }
    }

    // 今までの合計支出を返す ダッシュボードに表示する
    public static function fetchOutgo(int $group_id)
    {
        try {
            // where句をつくる
            $type_id = self::$outgo_type_id;
            self::$temp_to_bind['where'] = get_defined_vars();
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
            self::$temp_to_bind['where'] = get_defined_vars();
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
            self::$temp_to_bind['where'] = self::validateInputs(get_defined_vars());
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
    ) {
        try {
            // 受け取った値に対応するwhere句を生成する
            self::$temp_to_bind['where'] = self::validateInputs(get_defined_vars());
            self::makeWhereClause();
            self::addPeriodFilter($target_date);

            // SQL文の句を作る
            self::$temp_where_clause = str_replace('`type_id`', '`main`.`type_id`', self::$temp_where_clause);
            self::$temp_selected_col = "main.`category_id`, categories.category_name, categories.type_id, IFNULL(SUM(`payment`), 0) AS `payment`";
            self::$temp_groupby_clause = "GROUP BY `category_id`";
            self::$temp_join_clause = "JOIN `categories` on `categories`.`id` = `main`.`category_id`";

            // SQL文を実行する
            self::fetch();

            // レコードがある場合、収入と支出に分ける
            $categorized_outgo_list = array();
            $categorized_income_list = array();
            foreach (self::$temp_result as $outgo) {
                if ($outgo['type_id'] == 1) {
                    $categorized_outgo_list[] = $outgo;
                }
            }
            foreach (self::$temp_result as $income) {
                if ($income['type_id'] == 2) {
                    $categorized_income_list[] = $income;
                }
            }
            $result[] = $categorized_outgo_list;
            $result[] = $categorized_income_list;
            
            return $result;
        } catch (PDOException $e) {
            // print('Error:'.$e->getMessage());
            throw $e;
        }
    }

    // あるグループの、過去のレコードに登録のあるカテゴリーのidと名前を返す
    // $target_date を渡されていれば年月で絞る
    public static function fetchCategories(int $group_id, ?string $target_date = null)
    {
        try {
            // 受け取った値に対応するwhere句を生成する
            self::$temp_to_bind['where']['group_id'] = $group_id;
            self::makeWhereClause();
            self::makeOrderClause(desc: false, column: 'main`.`category_id');
            self::$temp_where_clause = str_replace('`group_id`', '`main`.`group_id`', self::$temp_where_clause);

            // $target_date を渡されていれば年月のフィルターをwhere句に加える。
            if (!is_null($target_date)) {
                self::addPeriodFilter($target_date);
            }

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
            self::$temp_to_bind['temp']['group_id'] = $group_id;

            // where句とselect対象を指定する
            self::$temp_where_clause = "WHERE group_id = :group_id";
            self::$temp_selected_col = "id, payment_at ";

            // 購入日付・昇順に設定
            self::makeOrderClause(column: 'payment_at');

            // PDOメソッドの指定
            $pdo_method = 'pdoFetchAssoc';

            // SQL文を実行し、結果を得る
            self::fetch($pdo_method);

            if (self::$temp_result) {
                // レコードが存在する場合
                return self::$temp_result['payment_at'];
            } else {
                // レコードが存在しない場合
                return date("Ymd");
            }
        } catch (PDOException $e) {
            // print('Error:'.$e->getMessage());
            throw $e;
        }
    }

    // グループのレコード数を返す
    // $target_date を渡されていれば年月で絞る
    public static function countRecords(
        int $group_id,
        ?string $target_date = null,
        ?int $category_id = null
    ) {
        try {
            // バインド対象を一時変数に格納に格納する
            self::$temp_to_bind['where'] = self::validateInputs(get_defined_vars());

            // where句とselect対象を指定する
            self::makeWhereClause();
            self::$temp_selected_col = "COUNT(*) AS records";

            // $target_date を渡されていれば年月のフィルターをwhere句に加える。
            if (!is_null($target_date)) {
                self::addPeriodFilter($target_date);
            }

            // PDOメソッドの指定（一番上のレコードだけを取り出す）
            $pdo_method = 'pdoFetchAssoc';

            // SQL文を実行し、結果を得る
            self::fetch($pdo_method);
            return self::$temp_result['records'];
        } catch (PDOException $e) {
            throw $e;
        }
    }

    // あるグループのrecord登録のある月の、月初を取得する
    private static function getDateHavingRecords(
        int $group_id,
    ) {
        try {
            // バインド対象を一時変数に格納に格納する
            self::$temp_to_bind['where'] = get_defined_vars();

            // where句とselect対象を指定する
            self::makeWhereClause();
            self::$temp_selected_col = "DISTINCT DATE_FORMAT(payment_at, '%Y-%m-01') AS payment_at";
            self::$temp_groupby_clause = "GROUP BY payment_at";

            // SQL文を実行し、結果を得る
            self::fetch();
            return self::$temp_result;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public static function makeDateList(
        int $group_id,
    ) {
        try {
            // あるグループのrecord登録のある月の、月初日をすべて重複なく取得する
            self::makeOrderClause(desc:true, column:'payment_at');
            $date_having_records = DbConnectorMain::getDateHavingRecords($group_id);

            // recordが無ければ当月だけの配列を返す
            if (count($date_having_records) === 0) {
                $past_dates[] = array('year' => date('Y'), 'month' => date('n'), 'year_month' => date('Ym'));
                return $past_dates;
            }

            // もしレコードがなく、当月が含まれていなければ加える
            $latest = strtotime($date_having_records[0]['payment_at']);
            if (date('Ym') > date('Ym', $latest)) {
                $past_dates[] = array(
                    'year' => date('Y'),
                    'month' => date('n'),
                    'year_month' => date('Ym')
                );
            }

            // 日付を配列に直す
            foreach ($date_having_records as $date) {
                $parsed_date = strtotime($date['payment_at']);
                $past_dates[] = array(
                    'year' => date('Y', $parsed_date),
                    'month' => date('n', $parsed_date),
                    'year_month' => date('Ym', $parsed_date)
                );
                // echo $registration_date->format('Ymd')."<br>";
            }
            return $past_dates;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    // ユーザーの今月の支出
    public static function userPayment(int $user_id)
    {
        try {
            // バインドするカラム名をstatic変数に代入する
            self::$temp_to_bind['temp'] = get_defined_vars();
            // where句をつくる
            self::$temp_where_clause = 'WHERE `user_id`=:user_id && `type_id`=1 && month(payment_at) = month(now()) && year(payment_at) = year(now())';
            // SQL文の句を作る
            self::$temp_selected_col = " SUM(payment) as payment, DATE_FORMAT(payment_at, '%Y%m') as date";
            self::$temp_groupby_clause = "GROUP BY `date`";
            
            // PDOメソッドの指定
            $pdo_method = 'pdoFetchAssoc';
            
            // 親クラスのメソッドで結果を取り出す
            self::fetch($pdo_method);
            return self::$temp_result;
        } catch (PDOException $e) {
            // print('Error:'.$e->getMessage());
            throw $e;
        }
    }

    public static function groupPayment(int $group_id)
    {
        try {
            // バインドするカラム名をstatic変数に代入する
            self::$temp_to_bind['temp'] = get_defined_vars();
            // where句をつくる
            self::$temp_where_clause = 'WHERE `group_id`=:group_id && `type_id`=1 && month(payment_at) = month(now()) && year(payment_at) = year(now())';
            // SQL文の句を作る
            self::$temp_selected_col = " SUM(payment) as payment, DATE_FORMAT(payment_at, '%Y%m') as date";
            self::$temp_groupby_clause = "GROUP BY `date`";
            
            // PDOメソッドの指定
            $pdo_method = 'pdoFetchAssoc';
            
            // 親クラスのメソッドで結果を取り出す
            self::fetch($pdo_method);
            return self::$temp_result;
        } catch (PDOException $e) {
            // print('Error:'.$e->getMessage());
            throw $e;
        }
    }
}
