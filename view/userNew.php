<?php
require_once __DIR__ . "/../userController.php";
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
	<title>ユーザー登録</title>
</head>

<body>
	<!-- ヘッダー -->
	<?php include __DIR__ . "/_beforeHeader.php" ?>
	<div class="container">
		<div class="mt-4"></div>
		<div class="mx-auto">
			<div class="box">
				<span class="input-group-addon ">
					<p><i class="fas fa-desktop"></i> ユーザー登録</p>
				</span>
				<div id="app">
					<!-- モーダル -->
					<form method="POST" id="form" action="" enctype="multipart/form-data">
						<!-- Modal -->
						<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog"
							aria-labelledby="exampleModalLabel" aria-hidden="true">
							<div class="modal-dialog modal-dialog-centered" role="document">
								<div class="modal-content">
									<div class="modal-header">
										<h5 class="modal-title" id="exampleModalLabel">ユーザー登録確認</h5>
									</div>
									<div class="modal-body" style="word-wrap: break-word">
										<!-- 確認画面 -->
										<p><i class="fas fa-pen"></i> ニックネーム:<span class="ml-2"><span
													id="modalName"></span></span></p>
										<p><i class="far fa-envelope"></i> メールアドレス:<span class="ml-2"><span
													id="modalMail"></span></span></p>
										<p><i class="fa fa-lock"></i> パスワード:<span class="ml-2"><span
													id="modalPassword"></span></span></p>
										<p><i class="fa fa-image"></i> ユーザーイメージ:<span class="ml-2">
												<span v-if="url"><img :src="url"
														style="height:50px;width:50px;border-radius:50%;"></span></span>
										</p>
										<p><i class="fa fa-ticket"></i> グループ:<span class="ml-2"><span
													id="modalGroup"></span></span></p>
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-secondary"
											data-dismiss="modal">修正する</button>
										<input type="submit" name="new_user" class="btn btn-primary" value="送信する">
									</div>
								</div>
							</div>
						</div>
						<div class="divider-form"></div>
						<div class="form-group">
							<p><i class="fas fa-pen"></i>
								<label for="user_name">ニックネーム</label>
								<input type="text" id="user_name" name="user_name" class="form-control"
									value="<?php if (!empty($_POST['user_name'])) { echo Config::h($_POST['user_name']); } ?>" required>
								<?php if (!empty($user_errors['user_name'])): ?>
								<span class="text-danger"><?php echo $user_errors['user_name']; ?></span>
								<?php endif; ?>
							</p>
						</div>

						<div class="divider-form"></div>
						<div class="form-group">
							<p><i class="far fa-envelope"></i>
								<label for="mail">メールアドレス</label>
								<input type="email" id="mail" name="mail" class="form-control"
									value="<?php if (!empty($_POST['mail'])) { echo Config::h($_POST['mail']); } ?>">
								<?php if (!empty($user_errors['mail'])): ?>
								<span class="text-danger"><?php echo $user_errors['mail']; ?></span>
								<?php endif; ?>
							</p>
						</div>

						<div class="divider-form"></div>
						<div class="form-group">
							<p><i class="fa fa-lock"></i>
								<label for="password">パスワード</label>
								<input type="password" id="password" name="password" class="form-control"
									value="<?php if (!empty($_POST['password'])) { echo Config::h($_POST['password']); } ?>">
								<?php if (!empty($user_errors['password'])): ?>
								<span class="text-danger"><?php echo $user_errors['password']; ?></span>
								<?php endif; ?>
							</p>
						</div>

						<div class="divider-form"></div>
						<div class="form-group">
							<p><i class="fa fa-image"></i>
								<label for="user_image">アイコン写真</label>
							<p><input type="file" name="user_image" ref="preview" @change="uploadFile"></p>
							<?php if (!empty($user_errors['user_image'])): ?>
							<span class="text-danger"><?php echo $user_errors['user_image']; ?></span>
							<?php endif; ?>
							</p>
						</div>

						<div class="divider-form"></div>
						<div class="form-group">
							<p><i class="fa fa-ticket"></i> グループ作成</p>
							<p>
							<div id="crate_group">
								<input type="radio" id="new_group" name="user_group" value="new_group" v-model="group"
									checked>
								<label for="new_group">新規グループ</label>
								<input type="radio" id="existing_group" name="user_group" value="existing_group"
									v-model="group">
								<label for="existing_group">既存グループ</label>

								<div class="divider-form"></div>
								<div v-if="group === 'new_group'">
									<label for="group_form">新規家計簿名</label>
									<input type="text" id="group_name" name="group_form" class="form-control"
										key="value1">
								</div>
								<div v-if="group === 'existing_group'">
									<label for="group_form">グループパスワード</label>
									<input type="text" id="group_name" name="group_form" class="form-control"
										key="value2">
								</div>
								<?php if (!empty($user_errors['group_form'])): ?>
								<span class="text-danger"><?php echo $user_errors['group_form']; ?></span>
								<?php endif; ?>
							</div>
							</p>
						</div>

						<div class="divider-form"></div>
						<button id="js-submit" type="button" name="new_user" class="btn btn-block btn-lg btn-primary"
							data-toggle="modal" data-target="#exampleModal">
							確認する
						</button>
						<p class="text-center">すでにアカウントをお持ちですか？</p>
						<a href="login.php" class="nav-item nav-link text-center">ログインする</a>
					</form>
				</div>
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
	<!-- Vue.jsの読み込み -->
	<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
	<script src="../stylesheet/js/userNew.js"></script>
</body>

</html>