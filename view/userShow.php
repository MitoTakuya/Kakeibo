<?php
require_once __DIR__ . "/../userEditController.php";
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
		<title>ユーザー詳細</title>
	</head>
	<body>
		<!-- ヘッダー -->
		<?php include __DIR__ . "/_header.php" ?>
		<div class="container">
			<div class="mt-4"></div>
			<div class="box" style="border-radius: 10px;">
				<div class="card-header bg-white">
					<div class="check"><h5><i class="fas fa-desktop"></i>ユーザー情報</h5></div>
				</div>
				<div class="card-body">
					<div class="card-text">
						<div class="check"><h5><i class="fas fa-pen"></i>ログイン中ユーザー</h5></div>
						<div class="ml-4">
							<h5><span><?php echo $current_user['user_name']; ?>
								<img src="../images/<?php echo $current_user['user_image']; ?>" border-radius="50%" alt="アイコン画像"
									style="height:50px;width:50px;border-radius:50%"/><span class="float-right">
										<button type="button" class="btn btn-primary" onclick="location.href='userUpdate.php'">
										更新する</button></span>
						</div>
						<div class="mt-4"></div>
						<?php 
						if (isset($other_users)):
						foreach($other_users as $row) {
						?>
						<div class="check"><h5><i class="fa fa-user"></i>グループメンバー</h5></div>
						<div class="ml-4">
							<h5><span><?php echo $row['user_name']; ?>
								<img src="../images/<?php echo $row['user_image']; ?>" border-radius="50%" alt="アイコン画像" style="height:50px;width:50px;border-radius:50%"/>
							</span></h5>
						</div>
						<?php
						}
						endif;
						?>
					</div>
				</div>
			</div>
			<div class="box" style="border-radius: 10px;">
				<div class="card-header bg-white">
					<div class="check"><h5><i class="fas fa-calculator"></i>グループ情報</h5></div>
				</div>
				<div class="card-body">
					<div class="card-text" id="groupCard">
						<?php
						// ユーザーグループ情報
						foreach ($user_show as $group) {
						?>
						<div class="check">
							<h5><i class="fas fa-user-group"></i>グループ名
							<span class="float-right"><button type="button" class="btn btn-primary"
							onclick="location.href='groupUpdate.php'">更新する</button></span>
							</h5>
						</div>
						<div class="ml-4">
							<span><?php echo $group['group_name']; ?></span>
						</div>
						<div class="check"><h5><i class="fas fa-piggy-bank"></i>目標貯金額</h5></div>
						<div class="ml-4">
							<span><?php echo number_format($group['goal']); ?>円</span>							
						</div>
						<h5 class="check"><i class="fas fa-lock"></i>家計簿パスワード <span><button class="btn btn-secondary btn-sm" @click="active">表示する</button></span>
						</h5>
						<div class="ml-4">
							<span v-if="isActive">*****************</span>
							<span v-else><?php echo $group['group_password']; ?></span>
						</div>
						<?php
						break;
							}
						?>
					</div>
				</div>
			</div>
		</div>
		<!-- Optional JavaScript -->
		<!-- jQuery first, then Popper.js, then Bootstrap JS -->
		<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
		<!-- Vue.jsの読み込み -->
		<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
		<script src="../stylesheet/js/userShow.js"></script>
	</body>
</html>