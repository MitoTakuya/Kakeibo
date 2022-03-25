<?php
require_once('DB_Connector.php');
class DB_Connector_category extends DB_Connector {

    protected static $target_table = 'categories';

    // @Override    Categoriesを下記のように出力する
    /* e.g.
     * Array ( [1] => Array ( [0] => 食費 [1] => 光熱費...)
     *         [2] => Array ( [0] => 給与 [1] => その他 ) )
     */ 
    public static function fetchAll(int $order = 1)
    {
        // 昇順・降順を選択する
        $order_clause = self::selectOrder($order);

        $sql = "SELECT `type_id`, `category_name`
                FROM  `categories` " . $order_clause;

        $stmt = self::$pdo->prepare($sql);
        // $stmt->bindParam(':column', $column, PDO::PARAM_STR);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_COLUMN|PDO::FETCH_GROUP);


        // クエリ結果が0件で空の配列が返ってきた場合はfalseを返す
        if (count($results) == 0) {
            return false;
        } else {
            return $results;
        }
    }
}