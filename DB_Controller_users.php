<?php
require_once('./DB_Controller.php');
class DB_Controller_users extends DB_Controller {
    private static $dsn = 'mysql:dbname=bbs;host=localhost';
    private static $DB_user = 'root';
    private static $DB_password = '';

    private String $sql;
    private String $target_table;
    private $pdo;
    

    // 対象テーブルを選択
    function __construct() {
        parent::__construct('users');
    }

    /**************************************************************************
     * userテーブル操作用のメソッド
     **********************************************************************/
    // 
    public function insert_an_article($user_name, $password, $mail, $user_image, $group_id) {
        if($this->connect_DB()) {
            $stmt = $this->pdo->prepare('INSERT INTO main(user_name, password, mail, user_image, group_id) VALUES(:user_name, :password, :mail, :user_image, :group_id);');
            //SQL文中の プレース部を 定義しておいた変数に置き換える
            $stmt->bindParam( ':user_name', $user_name, PDO::PARAM_STR);
            $stmt->bindParam( ':password', $password, PDO::PARAM_STR);
            $stmt->bindParam( ':mail', $mail, PDO::PARAM_STR);
            $stmt->bindParam( ':user_image', $user_image, PDO::PARAM_STR); 
            $stmt->bindParam( ':group_id', $group_id, PDO::PARAM_INT); 

            //sqlを 実行
            $stmt->execute();
        }
    }
}