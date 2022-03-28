<?php 
require_once __DIR__ . "/class/Config.php";
require_once __DIR__ . "/class/UserLogin.php";
session_start();
UserLogin::checkLogin();
