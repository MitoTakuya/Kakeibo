<?php
require('class/DB_Controller_users.php');

if(!empty($_POST)){
  $new_user = new DB_Controller_users();
  $user_errors = $new_user->input_confirmation();
  if($user_errors == "ok") {
    // ログインに飛ばす
    header('Location: ../view/login.php');
  }
}