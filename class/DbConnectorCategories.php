<?php
class DbConnectorCategories extends DbConnector
{
    protected static $target_table = 'categories';

    // @Override    Categoriesを下記のように出力する
    /* e.g.
     * Array ( [1] => Array ( [0] => 食費 [1] => 光熱費...)
     *         [2] => Array ( [0] => 給与 [1] => その他 ) )
     */
    public static function fetchCategories()
    {
        try {
            // select対象を選択する
            self::$temp_selected_col = "`type_id`, `category_name`";

            // PDOメソッドを選択する
            $pdo_method = 'pdoFetchColGr';

            // SQL文を実行する
            self::fetch($pdo_method);

            // クエリ結果が0件の場合、空の配列を返す
            return self::$temp_result;
        } catch (PDOException $e) {
            // print('Error:'.$e->getMessage());
            throw $e;
        }
    }
}
