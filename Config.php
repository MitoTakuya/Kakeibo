<?php
############################################################
#設定に関する処理をまとめたクラス
############################################################

class Config {

    //トークンの作成　Config::createToken() で使用可能
    public static function createToken() {
        $token_byte = openssl_random_pseudo_bytes(16);
        $csrf_token = bin2hex($token_byte);
        $_SESSION['csrf_token'] = $csrf_token;
    }

    //入力文字のエスケープ処理　Config::h($変数) で使用可能
    public static function h($str) {
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    }
    

}