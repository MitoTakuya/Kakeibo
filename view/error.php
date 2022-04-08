<?php
$uri = basename($_SERVER['REQUEST_URI']);
$unlogined = false;
$from_user = false;
if ($uri == 'userNew.php') {
    $unlogined = true;
} else {
    require_once __DIR__.'/../init.php';
}
if ($uri == 'userNew.php'||$uri == 'groupUpdate.php' || $uri == 'userUpdate.php') {
    $from_user = true;
}
?>

<!doctype html>
<html lang="ja">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
		integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
	<link rel="stylesheet"
		href="http://<?= $_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']) ?>/stylesheet/css/user.css">
	<title>error</title>
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
					<span style="color: black;"> エラー発生</span>
					<div class="divider-form"></div>
				</div>
				<div class="input-group-addon text-center mb-5">
					<span>内容：</span>
					<?php if (isset($error_message)): ?>
					<span class="text-danger"><?= $error_message ?></span>
					<?php else :?>
					<span class="text-danger">予期せぬエラーが発生しました。</span>
					<?php endif ;?>
				</div>

				<?php if (isset($error_message) && $error_message !== "不正な通信です。" && $from_user === false): ?>
				<input type="button" class="btn btn-block btn-lg btn-primary" value="再読込み"
					onclick="window.location.reload(true);" />
				<?php endif ;?>

				<?php if ($unlogined): ?>
				<input type="button" class="btn btn-block btn-lg btn-primary" value="戻る" onclick="history.back()" />
				<?php else :?>
				<a href="http://<?= $_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']) ?>/logout.php?error=1"
					class="nav-item nav-link text-center">再ログイン</a>
				<?php endif ;?>
			</div>
		</div>
	</div>
	<!-- Optional JavaScript -->
	<!-- jQuery first, then Popper.js, then Bootstrap JS -->
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
		integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
	</script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"
		integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous">
	</script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"
		integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous">
	</script>

</body>

</html>