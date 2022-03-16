<?php
require_once dirname(__FILE__) . '/DB_Controller.php';
class DB_Controller_users extends DB_Controller {
    
    public static $user_errors = array();
    private $group_id = 0;

    // 対象テーブルを選択
    function __construct() {
        parent::__construct('users');
    }

    public function setGroupId($group_id) {
        $this->group_id = $group_id;
    }
    public function getGroupId() {
        return $this->group_id;
    }

    // ユーザー登録入力内容チェック
    public function input_confirmation() {
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

        // ユーザーグループ
        // 新規グループ選択時
        if($_POST['user_group'] == "new_group") {
            if(!isset($_POST['group_form']) || !strlen($_POST['group_form']) || str_replace(array(" ", "　"), "", $_POST['group_form']) === '') {
                self::$user_errors['group_form'] = '家計簿名を入力してください';
            } else if(mb_strlen($_POST['group_form']) > 30) {
                self::$user_errors['group_form'] = '30文字以内で入力してください';
            } else {
                $group_name = $_POST['group_form'];
                // ユニークキー作成方法は検討
                $group_password =  uniqid();
            }
            // 既存グループ選択時
        } elseif($_POST['user_group'] == "existing_group") {
            if(!isset($_POST['group_form']) || !strlen($_POST['group_form']) || str_replace(array(" ", "　"), "", $_POST['group_form']) === '') {
                self::$user_errors['group_form'] = 'グループパスワードを入力してください';
            } else if(mb_strlen($_POST['group_form']) > 30) {
                self::$user_errors['group_form'] = '30文字以内で入力してください';
            } else {
                $group_password = $_POST['group_form'];
                // パスワードからuser_groupのidを検索
                $error = $this->search_group_id($group_password);
                if(!$error) {
                    self::$user_errors['group_form'] = 'グループパスワードが違います。';
                } else {
                    $this->setGroupId($error);
                }
            }
        }


        // エラーがなければユーザー情報登録
        if (count(self::$user_errors) == 0) {
            // パスワードを暗号化
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $user_image = date('YmdHis') . $_FILES['user_image']['name'];
            // 画像をアップロード
            move_uploaded_file($user_image, '../images/'. $user_image);

            if($_POST['user_group'] ==  "new_group") {
                // ユーザー、新規グループ登録
                $this->create_user_with_group($group_name, $group_password, $user_name, $hash, $mail, $user_image);
            } else {
                // ユーザー登録
                $group_id = $this->getGroupId();
                $this->insert_an_user($user_name, $hash, $mail, $user_image, $group_id);
            }
            return "ok";
        } else {
            return self::$user_errors;
        }
    }
    // ログイン入力確認
    public function login_confirmation() {

        // POSTされていないときは処理を中断する
        if(!filter_input_array(INPUT_POST)) {
            return;
        }
        
        // メールアドレスが入力されているか確認
        if(trim($_POST['mail']) === "") {
            self::$user_errors['login_mail'] = "メールアドレスを入力してください";
        } else {
            $mail = $_POST['mail'];
        }

        // パスワードが入力されているか確認
        if(trim($_POST['password']) === "") {
            self::$user_errors['login_password'] = "パスワードを入力してください";
        } else {
            $password = $_POST['password'];
        }

        // エラーがなければ保存
        if(count(self::$user_errors) == 0) {
            $this->login_user($mail, $password);
            return "login";
        } else {
            return self::$user_errors;
        }
    }

    protected function redirect($url)
    {
        header('Location: http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']).$url);
    }

    // ユーザー・新規グループ登録
    public function create_user_with_group($group_name, $group_password, $user_name, $hash, $mail, $user_image)
    {
        // transaction開始 後で記入
        $this->insert_user_group($group_name, $group_password);
        $group_id = $this->getGroupId();
        $this->insert_an_user($user_name, $hash, $mail, $user_image, $group_id);
        // commit　後で記入
    }

    // ログイン用メソッド
    public function login_user($mail, $password) {
        if($this->connect_DB()) {
            $stmt = $this->pdo->prepare('SELECT `id`, `password`, `mail` FROM users WHERE mail=:mail');
            //SQL文中の プレース部を 定義しておいた変数に置き換える
            $stmt->bindParam( ':mail', $mail, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch();
            // 指定したハッシュがパスワードにマッチしているか
            if(password_verify($password, $user['password'])) {
                // ユーザー情報をセッションに保存
                $_SESSION['id'] = $user['id'];
                return "login_ok";
            } else {
                return self::$user_errors;
            }
        }
    }

    // ユーザーグループ登録
    public function insert_user_group($group_name, $group_password) {
        if($this->connect_DB()) {
            $stmt = $this->pdo->prepare('INSERT INTO `user_groups`(`group_name`, `group_password`) VALUES(:group_name, :group_password);');
            //SQL文中の プレース部を 定義しておいた変数に置き換える
            $stmt->bindParam( ':group_name', $group_name, PDO::PARAM_STR);
            $stmt->bindParam( ':group_password', $group_password, PDO::PARAM_STR);
            //sqlを 実行
            $stmt->execute();
            $this->setGroupId($this->pdo->lastInsertId());
        }
    }

    // ユーザーグループ検索
    public function search_group_id($group_password) {
        if($this->connect_DB()) {
            $stmt = $this->pdo->prepare('SELECT `id`FROM user_groups WHERE group_password=:group_password');
            $stmt->bindParam( ':group_password', $group_password, PDO::PARAM_STR);

            //sqlを 実行
            $stmt->execute();
            $id = $stmt->fetch();
            return isset($id['id']);
        }
    } 

    /**************************************************************************
     * userテーブル操作用のメソッド
     **********************************************************************/
    // 
    public function insert_an_user($user_name, $password, $mail, $user_image, $group_id) {
        if($this->connect_DB()) {
            $stmt = $this->pdo->prepare('INSERT INTO users(user_name, password, mail, user_image, group_id) VALUES(:user_name, :password, :mail, :user_image, :group_id);');
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