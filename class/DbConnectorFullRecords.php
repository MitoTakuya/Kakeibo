<?php
require_once __DIR__ . '/DbConnector.php';
class DbConnectorFullRecords extends DbConnector {

    protected static $target_table = 'full_records';

    // あるグループのレコードを一定数取り出す（画面に収まる数など *後で別クラスに移す
    // DbConnector::makeOrderClauserder()で事前にorderby句の設定が必要
    public static function fetchGroupLimitedRecords(
        int $group_id,
        int $limit,
        int $offset = 0
    ){
        // 受け取った値に対応する一時変数に格納する
        self::$temp_inputs['temp'] = get_defined_vars();

        self::$temp_selected_col = ' * ';
        self::$temp_where_clause = "WHERE `group_id`=:group_id";
        // limitoffset句付きのorderby句
        self::addLimit();

        $results = self::fetchSome();

        // クエリ結果が0件で空の配列が返ってきた場合はfalseを返す
        if (count($results) == 0) {
            return false;
        } else {
            return $results;
        }
    }

    public static function fetchFilteredRecords(
        int $group_id,
        int $type_id = null,
        ?string $target_date = null,
        ?string $category_id = null
    ) {
        // 受け取った値に対応するwhere句を生成する
        self::$temp_inputs['where'] = get_defined_vars();
        unset(self::$temp_inputs['where']['target_date']);// target_date はwhere句に含めないためunset
        self::makeWhereClause();
        self::addPeriodFilter($target_date);
        
        // select対象を設定
        self::$temp_selected_col = ' * ';

        $results = self::fetchSome();
        
        // クエリ結果が0件で空の配列が返ってきた場合はfalseを返す
        if (count($results) == 0) {
            return false;
        } else {
            return $results;
        }
    }
}