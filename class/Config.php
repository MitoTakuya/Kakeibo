<?php
############################################################
#設定やバリデーション関連の処理を記述するクラスです。
############################################################

class Config {
    //入力値のエスケープ処理　Config::h($変数) で使用可能
    public static function h($str) {
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    }

    //入力値の前後の空白を除去し、空の場合はfalseを返す
    public static function delete_space($str) {
        $replace = preg_replace( '/\A[\p{C}\p{Z}]++|[\p{C}\p{Z}]++\z/u', '', $str);
        if (!$replace === '') {
            return $replace;
        }else {
            return false;
        }
    }

    //トークンの作成　ログイン時に実行しセッションとして使用する。
    public static function create_token() {
        $token_byte = openssl_random_pseudo_bytes(16);
        $csrf_token = bin2hex($token_byte);
        $_SESSION['token'] = $csrf_token;
    }

    //トークンチェック　データのPOST先で以下メソッドを実行して確認する。
    function check_token() {
        if (empty($_SESSION['token']) || $_SESSION['token'] !== $_POST['token']) {
            // include('error.php');
            echo "不正な通信です。";
            die();
        }
    }

    //セッション削除
    public static function destroy_session() {
        // セッション開始
        session_start();  
        // セッション変数を全て削除
        $_SESSION = array();
        // セッションクッキーを削除
        if (isset($_COOKIE["PHPSESSID"])) {
        setcookie("PHPSESSID", '', time() - 1800, '/');
        }
        // セッションの登録データを削除
        session_destroy();
        header('Location: http://localhost/');
        exit;
    }

} 
