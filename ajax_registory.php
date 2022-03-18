<?php
session_start();

require_once __DIR__.'/class/DB_Connector_main.php';

// ajaxでPOSTされたときに以下を実行する。
// if (isset($_SESSION['csrf_token']) && isset($_SESSION['id']))  {
	if($_POST['id']) {
        
        $record_id = $_POST['id'];
        $method = $_POST['method'];
        //インスタンス作成
        $db_main = new DB_Connector_main();
        
        if ($_POST['method'] === 'delete') {  

            //mainテーブルの対象レコードを削除
            $result = $db_main->deleteOne($record_id);

        }elseif ($_POST['method'] === 'select') {

            //mainテーブルの対象レコード取得
            $result = $db_main->fetchOne($record_id);
            // ヘッダーを指定することによりjsonの動作を安定させる
            header('Content-type: application/json');
            //resultを配列からjsonに変換する
            echo json_encode($result);

        }
        //★エラー時の処理追加します。
        //起こりうることとして、更新ボタンを押したが、すでにデータが他者によって削除されていた時など。



	}

