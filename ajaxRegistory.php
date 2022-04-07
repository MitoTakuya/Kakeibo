<?php
require_once __DIR__ . '/init.php';

try  {
    DbConnector::connectDB();
    // ajaxでPOSTされたときに以下を実行する。
	if($_POST['id']) {
        //check_token()
        $record_id = $_POST['id'];
        $method = $_POST['method'];
        $group_id = $_SESSION['group_id'];
        
        if ($method === 'del_registory') {

            //mainテーブルの対象レコードを削除
            $result = DbConnectorMain::deleteOne($record_id);
            //削除後のトータルレコード件数を取得
            $total_record = DbConnectorMain::countRecords($group_id);
            //jsonの動作を安定させる
            header('Content-type: application/json');
            //resultをjsonに変換する
            echo json_encode($total_record);    

        }elseif ($method === 'select') {
            
            //mainテーブルの対象レコード取得
            $result =  DbConnectorMain::fetchOne($record_id);
            //jsonの動作を安定させる
            header('Content-type: application/json');
            //resultをjsonに変換する
            echo json_encode($result);

        }elseif ($method === 'del_category') {
            
            $target_date = $_SESSION['target_date'];
            $category_id = (int)$_POST['category_id'];
            
            //mainテーブルの対象レコードを削除
            $result = DbConnectorMain::deleteOne($record_id);
            //削除後のカテゴリ別レコード件数を取得
            $total_record = DbConnectorMain::countRecords($group_id, $target_date, $category_id);
            //jsonの動作を安定させる
            header('Content-type: application/json');
            //resultをjsonに変換する
            echo json_encode($total_record);

        }

    } 
}catch (Exception $e) {

    header('Location: http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']).'/error.php');
    exit();     
    // switch ($e) {
    //     case 2002:
    //         $error_message = DbConnector::CONNECT_ERROR;
    //         break;
    //     case 1:
    //         $error_message = DbConnector::CONNECT_ERROR;
    //         break;
    //     default:
    //     $error_message = "予期せぬエラーが発生しました。";
    //         break;
    // }
    // require_once __DIR__.'/view/error.php';
    // die();

}
