<?php
require('class/DB_Controller_users.php');

if(!empty($_POST)){
  $new_user = new DB_Controller_users();
  // ユーザー登録の際
  if(isset($_POST['new_user'])) {
    $user_errors = $new_user->input_confirmation();
    if($user_errors == "ok") {
      // ログインに飛ばす
      header('Location: ../view/login.php');
    }
  // ログインの際
  } else if(isset($_POST['login_user'])) {
    $user_errors = $new_user->login_confirmation();
    if($user_errors == "login") {
      // トップページに飛ばす
      header('Location: ../view/index.php');
    }
  }
}