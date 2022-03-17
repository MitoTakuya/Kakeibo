<?php
session_start();

require_once __DIR__.'/class/DB_Controller_main.php';

// ajaxでPOSTされたときに以下を実行する。
// if (isset($_SESSION['csrf_token']) && isset($_SESSION['id']))  {
	if($_POST['id']) {
        
        $record_id = $_POST['id'];
        
        //インスタンス作成
        $db_main = new DB_Controller_main();
        
        //mainテーブルの対象レコードを削除
        $result = $db_main->delete_a_record($record_id);

        if(!is_array($result)) {
            $error_message = "データ取得に失敗しました。";
        }

	}

