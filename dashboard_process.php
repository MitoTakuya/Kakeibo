<?php
include('../class/DB_Connector_main.php');
$DB_connector = new DB_Connector_main();


if ($_POST['selecting_date']) {
    $target_date = $_POST['selecting_date'];
}

// 支出とカテゴリを一緒に引っぱってくるメソッド（追加する）
$categories = $DB_connector->fetchCategoryColumns();
$categorized_outgo_list = array( //DBCの後でメソッドと入れ替える。(groupid: , target_month: , )
                array('category_id' => 1, 'category_name' => '食費', 'outgo' => 50000),
                array('category_id' => 2, 'category_name' => '交通費', 'outgo' => 30000),
                array('category_id' => 3, 'category_name' => '娯楽費', 'outgo' => 10000)
            );
$archives = "{ year : '2021', month : 12, year_month : 20211201},
            { year : '2022', month : 1,  year_month : 20220101},
            { year : '2022', month : 2,  year_month : 20220201},
            { year : '2022', month : 3,  year_month : 20220301}";
DB_Connector_main::disconnectDB();
