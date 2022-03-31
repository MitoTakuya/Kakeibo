<?php
require_once __DIR__ . '/DbConnector.php';
class DbConnectorFullRecords extends DbConnector {

    protected static $target_table = 'full_records';

    // あるグループのレコードを一定数取り出す（画面に収まる数など *後で別クラスに移す
    // DbConnector::makeOrderClauserder()で事前にorderby句の設定が必要
    public static function fetchLimitedRecords(
        int $group_id,
        int $limit,
        int $offset = 0
    ){
        try {
            // 受け取った値に対応する一時変数に格納する
            self::$temp_inputs['temp'] = get_defined_vars();

            // where句をつくる
            self::$temp_where_clause = "WHERE `group_id`=:group_id";

            // limitoffset句付きのorderby句
            self::addLimit();

            // SQL文を実行する
            self::fetch();

            // クエリ結果が0件の場合、空の配列を返す
            return self::$temp_result;

        } catch (PDOException $e) {
            // print('Error:'.$e->getMessage());
            throw $e;
        }
    }

/**********************************************************
 * 詳細画面で表示するためのレコードを取り出すメソッド
 **********************************************************/
// あるグループの月別、週別の、特定カテゴリにおける支出合計を出力する *要order切り替え
/*
    使用例 : fetch_filtered_records(group_id:1, target_date:'20220301')
    (グループid1番の「2022年3月1日」の月の全レコードを出力)
*/
    public static function fetchFilteredRecords(
        int $group_id,
        int $type_id = null,
        ?string $target_date = null,
        ?string $category_id = null
    ) {
        try {
            // 受け取った値から不要な値を取り除き、where句を生成する
            self::$temp_inputs['where'] = self::validateInputs(get_defined_vars());
            self::makeWhereClause();
            self::addPeriodFilter($target_date);

            // SQL文を実行する
            self::fetch();
            
            // クエリ結果が0件の場合、空の配列を返す
            return self::$temp_result;

        } catch (PDOException $e) {
            // print('Error:'.$e->getMessage());
            throw $e;
        }
    }
}