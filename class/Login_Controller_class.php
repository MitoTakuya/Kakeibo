<?php
require_once dirname(__FILE__) . '/User_Model_class.php';
require_once dirname(__FILE__) . '/Db_controller_users2.php';

class LoginController {

  // セッションに保存する名前
  const LOGINUSER = 'loginUserModel';


  // メールアドレスとパスワードでログインする
  static public function login() {

    // POSTされていないときは処理を中断する
    if(!filter_input_array(INPUT_POST)) {
      return;
    }

    // フォームからの値を受け取る
    $mail = filter_input(INPUT_POST, 'mail');
    $password = filter_input(INPUT_POST, 'password');

    // いずれかが空文字の場合は何もしない
    if('' == $mail || '' == $password) {
      return;
    }


    // emailからユーザーモデルを取得する
    $objUserModel = new UserModel();
    $objUserModel->getModelByEmail($mail);

    // パスワードをチェックする
    if(!$objUserModel->checkPassword($password)) {
      // ログイン失敗
      throw new Exception('ログインに失敗しました');  
    }

      //セッション固定攻撃対策
      session_regenerate_id(true);

      //セッションに保存
      $_SESSION[self::LOGINUSER] = $objUserModel;


    // ページ遷移(仮のページ)；
    header("location: ../view/index.php");
  }

  // ログインしているかチェックする
  static public function checkLogin() {
    $objUserModel = (isset($_SESSION[self::LOGINUSER])) ?
    $_SESSION[self::LOGINUSER] :
    null;

    if (is_object($objUserModel)) {
      return;
    }
    header('location: ../view/login.php');
  }

  // ログイン中のユーザーモデルを取得する
  static public function getLoginUser() {
    return $_SESSION[self::LOGINUSER];
  }

  // ログアウトする
  static public function logout() {
    $_SESSION = [];
    session_destroy();
    header('Location: ../view/login.php');
  }

}