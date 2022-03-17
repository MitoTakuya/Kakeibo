<?php
include('./class/DB_Connector_main.php');
$DB_connector = new DB_Connector_main();

// 支出とカテゴリを一緒に引っぱってくるメソッド（追加する）
$categories = $DB_connector->fetchCategoryColumns();

DB_Connector_main::disconnectDB();
