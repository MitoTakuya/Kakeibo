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
        if (!$replace == '') {
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
    public static function check_token() {
        if (empty($_SESSION['token']) || $_SESSION['token'] !== $_POST['token']) {
            $error_message = "不正な通信です。";
            require_once __DIR__.'/../view/error.php';
            die();
        }
    }

    // エラーコードを受けて、対応するエラーメッセージ
    // 引数はintとstringの両方があり得るため、タイプヒンティングを行わない
    public static function getErrorMessage($error_code)
    {
        switch ($error_code) {
            // DBがオープンでない場合
            case 2002:
                $error_message = DbConnector::CONNECT_ERROR;
                return $error_message;
                break;
            // DBとの接続が途中からできなくなった場合
            case 'HY000':
            case 2006:
                $error_message = DbConnector::TRANSACTION_ERROR;
                return $error_message;
                break;
            // その他
            default:
                $error_message = '予期せぬエラーが発生しました';
                return $error_message;
                break;
        }
    }

    // Internet Exploerでアクセスした場合エラーページへ
    public static function is_ie()
    {
        // ユーザーエージェントを取得
        $browser = $_SERVER['HTTP_USER_AGENT'];
        if (strstr($browser, 'Trident') || strstr($browser, 'MSIE')) {
            header('Location: http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']).'/ieError.php');
            exit();
        }
        return false;
    }

    // Internet Exploer以外でieErrorページを開かない処理
    public static function not_ie()
    {
        // ユーザーエージェントを取得
        $browser = $_SERVER['HTTP_USER_AGENT'];
        if (strstr($browser, 'Trident') || strstr($browser, 'MSIE')) {
            return true;
        } else {
            header('Location: http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']).'/login.php');
            exit();
        }
    }
} 
