<?php
require_once __DIR__ . "/../userGroupController.php";
?>
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
		<title>ユーザーグループ更新</title>
	</head>
	<body>
	<!-- ヘッダー -->
	<?php include __DIR__ . "/_header.php" ?>
	<div class="container">
		<div class="mt-4"></div>
		<div class="mx-auto">
			<div class="box" style="border-radius: 10px;">
			<span class="input-group-addon ">
				<p><i class="fas fa-desktop"></i></i>グループ情報更新</p>
			</span>
			<form method="POST" action="" enctype="multipart/form-data">
				<input type="hidden" name="id" value="<?php echo $row['id']; ?>">
				<div class="divider-form"></div>
				<div class="form-group">
				<p><i class="fas fa-user-group"></i>
					<label for="group_name">グループ名</label>
					<input type="text" id="group_name" name="group_name" class="form-control" value="<?php echo $row["group_name"]; ?>">
					<?php if(!empty($group_errors['group_name'])): ?>
					<span class="text-danger"><?php echo $group_errors['group_name']; ?></span>
					<?php endif; ?>
				</p>
				</div>

				<div class="divider-form"></div>
				<div class="form-group">
				<p><i class="fas fa-piggy-bank"></i>
					<label for="goal">目標貯金額</label>
					<input type="number" id="goal" name="goal" class="form-control" min="0" value="<?php echo $row["goal"]; ?>">
					<?php if(!empty($group_errors['goal'])): ?>
					<span class="text-danger"><?php echo $group_errors['goal']; ?></span>
					<?php endif; ?>
				</p>
				</div>

				<div class="divider-form"></div>
				<input type="submit" class="btn btn-block btn-lg btn-primary" value="送信">
				<button type="button" class="btn btn-block btn-lg btn-secondary" onclick="history.back()">戻る</button>
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