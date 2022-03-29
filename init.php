<?php 
// オートローダー読み込み
require_once __DIR__ . '/class/AutoLoader.php';
$loader = AutoLoader::registerDirectory(__DIR__);
$loader = AutoLoader::register();
session_start();
UserLogin::checkLogin();
