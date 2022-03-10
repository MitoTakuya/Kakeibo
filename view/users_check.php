<?php 
require_once dirname(__FILE__) . "/../dbconnect.php";
require_once dirname(__FILE__) . "/../encode.php";
session_start();

// ユーザー情報が送られてきているかチェック
if(!isset($_SESSION['join'])) {
	header('Location: index.php');
	exit();
}

if(!empty($_POST['check'])) {
	// パスワードを暗号化
	$hash = password_hash($_SESSION['join']['password'], PASSWORD_DEFAULT);

	// 入力情報をDBに登録
	$db = getDb();
	$stmt = $db->prepare("INSERT INTO users(name, email, password, picture) VALUES(:name, :email, :password, :picture)");
	// 登録するデータをセット
	$stmt->bindValue(':name', $_SESSION['join']['name']);
	$stmt->bindValue(':email', $_SESSION['join']['email']);
	$stmt->bindValue(':password', $hash);
	$stmt->bindValue(':picture', $_SESSION['join']['image']);

	$stmt->execute();

	// sessionを破棄
	unset($_SESSION['join']);
	header('Location: post.php');
	exit();
}
?>
<!doctype html>
<html lang="ja">
	<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="../stylesheet/user.css">
		<link rel="stylesheet" href="../stylesheet/style.css">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <title>ユーザー登録確認</title>
  </head>
  <body>
		<nav class="navbar navbar-dark bg-dark">
			<a href="login.php" class="navbar-brand">ログイン</a>
			<a href="index.php" class="navbar-brand text-right">新規登録</a>
		</nav>
    <div class="container">
      <div class="mt-4"></div>
      <div class="box">
				<form action="" method="POST">
					<input type="hidden" name="check" value="check">
					<div class="card-header bg-white">
						<div class="check"><h5><i class="fas fa-desktop"></i>入力情報の確認</h5></div>
					</div>
					<div class="card-body">
						<div class="card-text">
							<div class="check"><h5><i class="fas fa-pen"></i> ニックネーム:</h5></div>
							<h5><span><?php echo e($_SESSION['join']['name']); ?></span></h5>
							<div class="check"><h5><i class="far fa-envelope"></i> メールアドレス:</h5></div>
							<h5><span><?php echo e($_SESSION['join']['email']); ?></span></h5>
							<div class="check"><h5><i class="fa fa-lock"></i> パスワード:</h5></div>
							<h5><span><?php echo e($_SESSION['join']['password']); ?></span></h5>
							<div class="check"><h5><i class="fa fa-image"></i> アイコン画像</h5></div>
							<?php $ext = substr($_SESSION['join']['image'], -3); ?>
							<?php if($ext != 'jpg' && $ext != 'gif' && $ext != 'png'): ?>
								<h5><span>アイコン画像は設定されていません</span></h5>
							<?php else: ?>
								<img src="../images/<?php echo e($_SESSION['join']['image']); ?>" border-radius="50%" alt="アイコン画像" style="height:50px;width:50px;border-radius:50%"/>
							<?php endif; ?>
						</div>
						<div class="text-right">
							<input type="submit" class="btn btn-primary" value="登録する">
							<button type="button" class="btn btn-secondary" onclick="location.href='index.php'">修正する</button>
						</div>
					</div>
				</form>
      </div>
    </div>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
  </body>
</html>