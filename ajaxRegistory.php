<?php
session_start();
require_once __DIR__.'/class/DbConnectorMain.php';

if (DbConnector::connectDB()) {
    // ajaxでPOSTされたときに以下を実行する。
	if($_POST['id']) {
        //check_token()
        $record_id = $_POST['id'];
        $method = $_POST['method'];
        //インスタンス作成
        $db_main = new DbConnectorMain();
        
        if ($_POST['method'] === 'delete') {  

            //mainテーブルの対象レコードを削除
            $result = $db_main->deleteOne($record_id);

        }elseif ($_POST['method'] === 'select') {
            
            //mainテーブルの対象レコード取得
            $result = $db_main->fetchOne($record_id);
            //jsonの動作を安定させる
            header('Content-type: application/json');
            //resultをjsonに変換する
            echo json_encode($result);

        }

    } 
}
