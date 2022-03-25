<?php 
require_once __DIR__ . "/class/Config.php";
require_once __DIR__ . "/class/LoginController.php";
session_start();
LoginController::checkLogin();
