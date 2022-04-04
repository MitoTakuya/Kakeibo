<!-- ナビゲーションバー -->
<nav class="navbar navbar-dark" style="background-color: #1B435D">
    <!-- タイトル -->
    <a href="dashboard.php" class="navbar-brand">家計簿</a> 
    <!-- ハンバーガーメニュー -->
    <img class="circle" src="../images/<?php echo Config::h($_SESSION['user_image']); ?>" type="button"
    style="height:50px;width:50px;border-radius:50%;cursor:pointer"  data-toggle="collapse" data-target="#navbarNav"
    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation"/>
    <!-- ナビゲーションメニュー -->
    <div class="collapse navbar-collapse mr-2" id="navbarNav">
        <ul class="navbar-nav text-right">
            <li class="nav-item">
                <a class="nav-link" href="registory.php">記帳</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="userShow.php">ユーザー詳細</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">ログアウト</a>
            </li>
        </ul>
    </div>
</nav>
