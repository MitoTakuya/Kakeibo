<?php
require_once __DIR__ . '/DbConnector.php';
class DbConnectorCategories extends DbConnector {

    protected static $target_table = 'categories';

    // @Override    Categoriesを下記のように出力する
    /* e.g.
     * Array ( [1] => Array ( [0] => 食費 [1] => 光熱費...)
     *         [2] => Array ( [0] => 給与 [1] => その他 ) )
     */ 
    public static function fetchCategories()
    {
        try {
            self::$temp_selected_col = "`type_id`, `category_name`";
            $pdo_method = function () {
                $results = self::$temp_stmt->fetchAll(PDO::FETCH_COLUMN|PDO::FETCH_GROUP);
                return $results;
            };
            $results = self::fetch($pdo_method);

            // クエリ結果が0件の場合、空の配列を返す
            return $results;
            
        } catch (PDOException $e) {
            // print('Error:'.$e->getMessage());
            throw $e;
        }
    }
}