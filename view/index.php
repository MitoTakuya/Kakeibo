<?php 
session_start()
?>
<nav class="navbar navbar-dark bg-dark sticky-top">
    <!-- タイトル -->
    <a class="nav-link" href="user_show.php?id=<?php echo $_SESSION['id']; ?>">ユーザー詳細</a>
    <!-- <a class="nav-link" href="user/show.php?id=<?php echo e($_SESSION['id']); ?>">ユーザー詳細</a> -->
  </nav>
<h2>ログイン完了</h2>