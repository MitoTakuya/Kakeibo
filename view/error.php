<!doctype html>
<html lang="ja">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
		integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
	<link rel="stylesheet" href="http://<?= $_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']) ?>/stylesheet/css/user.css">
	<title>エラー画面</title>
</head>

<body>
	<!-- ヘッダー -->
	<?php if (isset($_SESSION['id'])): ?>
		<?php include __DIR__ . "/_header.php" ?>
	<?php else: ?>
		<?php include __DIR__ . "/_beforeHeader.php" ?>
	<?php endif; ?>
	
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
					<span>エラー内容：</span>
					<span class="text-danger"><?= $error_message ?></span>
				</div>
				<input type="button" class="btn btn-block btn-lg btn-primary" 
					value="再読込み" onclick="window.location.reload(true);" />
					<a href="http://<?= $_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']) ?>/logout.php?error=1" 
						class="nav-item nav-link text-center">再ログイン</a>
			</div>
		</div>
	</div>

</body>
</html>
