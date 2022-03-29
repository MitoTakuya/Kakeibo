<?php

class AutoLoader
{
    // クラスファイルがあるディレクトリリスト
    protected static $dirs;

    public static function register()
    {
        spl_autoload_register(array('AutoLoader', 'autoLoad'));
    }

    // クラスディレクトリを登録するメソッド
    public static function registerDirectory($dir)
    {
        self::$dirs[] = $dir. '/' . 'class';
    }

    // autoloadはインスタンス生成時に呼ばれるがその時対象となるクラス名を引数として引き受ける
    public static function autoLoad($className)
    {
        foreach (self::$dirs as $dir) {
            $filePath = $dir . '/' . $className . '.php';
            if (is_readable($filePath)) {
                require $filePath;
                return;
            }
        }
    }
}
