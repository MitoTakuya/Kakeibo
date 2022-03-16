<?php
// session_start();

require_once __DIR__.'/class/DB_Controller_main.php';

// ajaxでPOSTされたときに以下を実行する。
// if (isset($_SESSION['csrf_token']) && isset($_SESSION['id']))  {
	if( $_POST['id'] ) {

        $id = $_POST['id'];
        //カテゴリTBL
        
        //インスタンス作成
        $db_main = new DB_Controller_main();
        
        //カテゴリTBLより全データを連想配列で取得
        $result = $db_main->delete_a_record($id);
        exit;
        if(!is_array($result)) {
            $error_messages = "データ取得に失敗しました。";
        }

	}

