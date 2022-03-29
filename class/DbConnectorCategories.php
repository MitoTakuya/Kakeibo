<?php
require_once __DIR__ . '/DbConnector.php';
class DbConnectorCategories extends DbConnector {

    protected static $target_table = 'categories';

    // @Override    Categoriesを下記のように出力する
    /* e.g.
     * Array ( [1] => Array ( [0] => 食費 [1] => 光熱費...)
     *         [2] => Array ( [0] => 給与 [1] => その他 ) )
     */ 
    public static function fetchCategory()
    {
        self::$temp_selected_col = "`type_id`, `category_name`";
        $pdo_method = function () {
            $results = self::$temp_stmt->fetchAll(PDO::FETCH_COLUMN|PDO::FETCH_GROUP);
            return $results;
        };
        $results = self::fetchSome($pdo_method);

        // クエリ結果が0件で空の配列が返ってきた場合はfalseを返す
        if (count($results) == 0) {
            return false;
        } else {
            return $results;
        }
    }
}