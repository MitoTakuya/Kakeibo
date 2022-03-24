<?php
require_once ("../users_do.php");
?>
<!doctype html>
<html lang="ja">
	<head>
		<!-- Required meta tags -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<!-- Bootstrap CSS -->
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
			integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
		<link rel="stylesheet" href="../stylesheet/css/user.css">
		<!-- FontAwesome -->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
		<title>エラー</title>
	</head>
	<body>
		<nav class="navbar navbar-dark bg-dark">
			<a href="login.php" class="navbar-brand">ログイン</a>
			<a href="users_new.php" class="navbar-brand text-right">新規登録</a>
		</nav>
		<div class="container">
			<div class="mt-4"></div>
			<div class="mx-auto">
				<div class="box">
					<div class="input-group-addon text-center mb-5">
						<i class="fa-solid fa-triangle-exclamation" style="color: red;"></i>
						<span style="color: black;"> エラーが発生しました。</span>
						<div class="divider-form"></div>
					</div>

					<div class="input-group-addon text-center mb-5">
						<!-- <span>エラー内容：</span><span class="text-danger"><?php echo $error_message['error']; ?></span> -->
						<span>エラー内容：</span><span class="text-danger">データベース接続ができませんでした。</span>
						<span class="text-danger"></span>
					</div>
					<!-- これどうするか検討中 -->
					<input type="submit" name="login_user" class="btn btn-block btn-lg btn-primary" value="送信">
					<a href="login.php" class="nav-item nav-link text-center">再ログイン</a>
					</form>
				</div>
			</div>
		</div>
		<!-- Optional JavaScript -->
		<!-- jQuery first, then Popper.js, then Bootstrap JS -->
		<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
			integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" 
			integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" 
			integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous">
		</script>
	</body>
</html>
