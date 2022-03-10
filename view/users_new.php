
<!doctype html>
<html lang="ja">
	<head>
		<!-- Required meta tags -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<!-- Bootstrap CSS -->
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
		<link rel="stylesheet" href="../stylesheet/css/user.css">
		<!-- FontAwesome -->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
		<title>ユーザー登録</title>
	</head>
	<body>
	<nav class="navbar navbar-dark bg-dark">
		<a href="#" class="navbar-brand">ログイン</a>
		<a href="#" class="navbar-brand text-right">新規登録</a>
	</nav>
	<div class="container">
		<div class="mt-4"></div>
		<div class="mx-auto">
			<div class="box">
				<span class="input-group-addon ">
					<p><i class="fas fa-desktop"></i></i>ユーザー登録</p>
				</span>
				<form method="POST" action="" enctype="multipart/form-data">
					<div class="divider-form"></div>
					<div class="form-group">
						<p><i class="fas fa-pen"></i>
							<label for="name">ニックネーム</label>
							<input type="text" id="name" name="name" class="form-control" required>
						</p>
					</div>

					<div class="divider-form"></div>
					<div class="form-group">
						<p><i class="far fa-envelope"></i>
							<label for="email">メールアドレス</label>
							<input type="email" id="email" name="email" class="form-control">
						</p>
					</div>

					<div class="divider-form"></div>
					<div class="form-group">
						<p><i class="fa fa-lock"></i>
							<label for="password">パスワード</label>
							<input type="password" id="password" name="password" class="form-control">
						</p>
					</div>

					<div class="divider-form"></div>
					<div class="form-group">
						<p><i class="fa fa-image"></i>
							<label for="image">アイコン写真</label>
							<p><input type="file" name="image"></p>
						</p>
					</div>
					<div class="divider-form"></div>
					<input type="submit" class="btn btn-block btn-lg btn-primary" value="確認する">
					<p class="text-center">すでにアカウントをお持ちですか？</p>
					<a href="login.php" class="nav-item nav-link text-center">ログインする</a>
				</form>
			</div>
		</div>
	</div>
	<!-- Optional JavaScript -->
	<!-- jQuery first, then Popper.js, then Bootstrap JS -->
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
  </body>
</html>