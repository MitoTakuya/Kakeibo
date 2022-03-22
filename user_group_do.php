<?php
require('class/DB_Connector_user_group.php');

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    // ユーザーグループ情報取得
    $row = DB_Connector_user_group::fetchUserGroup($_GET['id']);
}
