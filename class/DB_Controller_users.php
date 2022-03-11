<?php
require_once dirname(__FILE__) . '/DB_Controller.php';
class DB_Controller_users extends DB_Controller {

    public static $user_errors = array();

    // 対象テーブルを選択
    function __construct() {
        parent::__construct('users');
    }

    // 入力内容チェック
    public function input_confirmation() {

        // 入力情報確認
        if(!isset($_POST['user_name']) || !strlen($_POST['user_name']) || str_replace(array(" ", "　"), "", $_POST['user_name']) === '') {
            self::$user_errors['user_name'] = '名前を入力してください';
        } else if(mb_strlen($_POST['user_name']) > 30) {
            self::$user_errors['user_name'] = '名前は30文字以内で入力してください';
        } else {
            $user_name = $_POST['user_name'];
        }

        if(trim($_POST['mail']) === "") {
            self::$user_errors['mail'] = "メールアドレスを入力してください";
        } else {
            $mail = $_POST['mail'];
        }

        if(trim($_POST['password']) === "") {
            self::$user_errors['password'] = "パスワードを入力してください";
        }else if (strlen($_POST['password']) < 4) {
            self::$user_errors['password'] = 'パスワードを4文字以上で入力してください';
        } else {
            $password = $_POST['password'];
        }

        if(!empty($user_image)) {
            $ext = substr($user_image, -3);
            if($ext != 'jpg' && $ext != 'gif' && $ext != 'png') {
                self::$user_errors['user_image'] = '画像の形式は[jpg],[gif],[png]のみです。';
            }
        }

        if (count(self::$user_errors) == 0) {
            $user_image = date('YmdHis') . $_FILES['user_image']['name'];
            // 画像をアップロード
            // move_uploaded_file($_FILES['image']['tmp_name'], '../images/'. $image);
            move_uploaded_file($user_image, '../images/'. $user_image);
            // ユーザー情報登録
            $this->insert_an_user($user_name, $password, $mail, $user_image);
            return "ok";
        } else {
            return self::$user_errors;
        }
    }

    protected function redirect($url)
    {
        header('Location: http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']).$url);
    }



    /**************************************************************************
     * userテーブル操作用のメソッド
     **********************************************************************/
    // 
    public function insert_an_user($user_name, $password, $mail, $user_image) {
        if($this->connect_DB()) {
            $stmt = $this->pdo->prepare('INSERT INTO users(user_name, password, mail, user_image, group_id) VALUES(:user_name, :password, :mail, :user_image, :group_id);');
            $group_id = 1;
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
    // 論理的削除を行うメソッド
    public function disable_a_user($target_id) {
        if($this->connect_DB()) {
            $stmt = $this->pdo->prepare('UPDATE `users` SET `is_deleted`=true WHERE id=:id');
            //SQL文中の プレース部を 定義しておいた変数に置き換える
            $stmt->bindParam( ':id', $target_id, PDO::PARAM_INT);
            //sqlを 実行
            $stmt->execute();
        }
    }
    // 論理的削除を取り消すメソッド
    public function delete_a_user($target_id) {
        if($this->connect_DB()) {
            $stmt = $this->pdo->prepare('UPDATE `users` SET `is_deleted`=false WHERE id=:id');
            //SQL文中の プレース部を 定義しておいた変数に置き換える
            $stmt->bindParam( ':id', $target_id, PDO::PARAM_INT);
            //sqlを 実行
            $stmt->execute();
        }
    }
    public function fetch_an_image($target_id) {
        if($this->connect_DB()) {
            $stmt = $this->pdo->prepare('UPDATE `users` SET `is_deleted`=false WHERE id=:id');
            //SQL文中の プレース部を 定義しておいた変数に置き換える
            $stmt->bindParam( ':id', $target_id, PDO::PARAM_INT);
            //sqlを 実行
            $stmt->execute();
        }
    }
}