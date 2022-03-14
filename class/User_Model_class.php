<?php
require_once dirname(__FILE__) . '/DB_Controller_users2.php';

class UserModel {
  private $username = null;
  private $password = null;
  private $mail = null;
  private $user_image = null;
  private $group_id = null;
  private $isdeleted = null;

  // セッター
  public function setUserName($username)
  {
      $this->username = $username;
      return $this;
  }

  public function setPassword($password)
  {
      $this->password = $password;
      return $this;
  }

  public function setMail($mail)
  {
      $this->mail = $mail;
      return $this;
  }

  public function setUserImage($user_image)
  {
      $this->user_image = $user_image;
      return $this;
  }

  public function setGroupId($group_id)
  {
      $this->group_id = $group_id;
      return $this;
  }

  public function setIsdeleted($isdeleted)
  {
      $this->isdeleted = $isdeleted;
      return $this;
  }

  // ゲッター
  public function getUserName()
  {
      return $this->username;
  }

  public function getPassword()
  {
      return $this->password;
  }

  public function getMail()
  {
      return $this->mail;
  }

  public function getUserImage()
  {
      return $this->user_image;
  }

  public function getGroupId()
  {
      return $this->group_id;
  }

  public function getIsdeleted()
  {
      return $this->isdeleted;
  }

  // メールアドレスからユーザーを検索する
  public function getModelByEmail($mail)
  {
    $user_db = DB_Controller_users::getFromEmail($strMail);
    return (isset($user_db[0])) ? $this->setProperty(reset($user_db)) : null;
  }

  // パスワードが一致しているか判定
  public function checkPassword($password)
  {
    $hash = $this->getPassword();
    return password_verify($password, $hash);
  }

  // プロパティをセットする
  public function setProperty()
  {
    
  }
}

