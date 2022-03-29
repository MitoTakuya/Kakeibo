<?php
require_once __DIR__.'/../registoryController.php';
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
	<link rel="stylesheet" href="../stylesheet/css/registory.css">
	<!-- FontAwesome -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
	<title>記帳画面</title>
</head>
<body>
	<nav class="navbar navbar-dark bg-dark">
		<a href="login.php" class="navbar-brand">ログイン</a>
		<a href="users_new.php" class="navbar-brand text-right">新規登録</a>
	</nav>
	<div class="container mt-4">
		<!-- <div class="mt-4"></div> -->
		<div class="mx-auto">
			<ul class="tab-group">
				<li class="tab tab-A is-active">支出</li>
				<li class="tab tab-B">収入</li>
			</ul>
			<!-- 支出用記帳フォーム -->
			<div class="registory-box mb-5">
				<div class="panel-group mt-1">
					<div class="panel tab-A is-show p-2">
					<form action="../registoryController.php?type_id=1" method="post">
						<div class="form-group">
							<p><i class="fa fa-lock"></i>
							<label>日付</label>
							<input type="date" class="form-control" name="payment_at" value="<?= date('Y-m-d') ?>" required>
						</div>
						<div class="divider"></div>
						<div class="form-group">
							<p><i class="fa fa-lock"></i>
							<label>タイトル</label>
							<input type="text" class="form-control"  name="title" required>
						</div>
						<div class="divider"></div>
						<div class="form-group">
							<p><i class="fa fa-lock"></i>
							<label>カテゴリ</label>
							<select id="outgoes" class="form-control" name="category_id">
								<?php foreach($category_outgoes as $key => $category_outgo) :?>
									<option value="<?= $key + 1 ?>"><?= $category_outgo ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="divider"></div>
						<div class="form-group">
							<p><i class="fa fa-lock"></i>
							<label>金額</label>
							<input type="text" onblur="addComma(this);" pattern="^((([1-9]\d*)(,\d{3})*)|0)$" 
								class="form-control" name="payment" maxlength="13" min="1" required>
						</div>
						<div class="divider"></div>
						<div class="form-group">
							<p><i class="fa fa-lock"></i>
							<label>メモ</label>
								<textarea name="content" class="form-control" cols="40" rows="5"></textarea><br>
						</div>
						<div class="divider"></div>
						<input type="submit" class="btn btn-primary mb-3" name="entry" value="登録する">
					</form>
					</div>

					<div class="panel tab-B p-2">
					<form action="../registoryController.php?type_id=2" method="post">
						<div class="form-group">
							<p><i class="fa fa-lock"></i>
							<label>日付</label>
							<input type="date" class="form-control" name="payment_at" value="<?= date('Y-m-d') ?>" required>
						</div>
						<div class="divider"></div>
						<div class="form-group">
							<p><i class="fa fa-lock"></i>
							<label>タイトル</label>
							<input type="text" class="form-control"  name="title" required>
						</div>
						<div class="divider"></div>
						<div class="form-group">
							<p><i class="fa fa-lock"></i>
							<label>カテゴリ</label>
							<select id="incomes" class="form-control" name="category_id">
							<?php foreach($category_incomes as $key => $category_income) :?>
							<option value="<?= $key + 101 ?>"><?= $category_income ?></option>
						<?php endforeach; ?>
							</select>
						</div>
						<div class="divider"></div>
						<div class="form-group">
							<p><i class="fa fa-lock"></i>
							<label>金額</label>
							<input type="text" onblur="addComma(this);" pattern="^((([1-9]\d*)(,\d{3})*)|0)$" 
								class="form-control" name="payment" maxlength="13" min="1" required>
						</div>
						<div class="divider"></div>
						<div class="form-group">
							<p><i class="fa fa-lock"></i>
							<label>メモ</label>
								<textarea name="content" class="form-control" cols="40" rows="5"></textarea><br>
						</div>
						<div class="divider"></div>
						<input type="submit" class="btn btn-primary mb-3" name="entry" value="登録する">
					</form>
					</div>
				</div>
			</div>
		</div>
	</div>

	<p class="show-table text-center mb-5">記帳一覧</p>

    <div class="container mb-5">
		<div class="registory-box table-responsive">
			<table class="table table-striped border border-5">
				<tbody>
					<!-- 一覧の項目名 -->
					<tr>
						<td scope="col" class="payment_at">日付</td> 
						<td scope="col" class="type_name">収支</td> 
						<td scope="col" class="title">タイトル</td> 
						<td scope="col" class="category_name">カテゴリー</td> 
						<td scope="col" class="payment">金額</td> 
						<td scope="col" class="memo">メモ</td> 
						<td scope="col" class="user_name">ユーザ名</td> 
						<td scope="col" class="updated_at">更新日</td> 
						<td scope="col" class="created_at">登録日</td> 
						<td scope="col" class="edit-column">編集</td>          
						<td scope="col" class="delete-column">削除</td>          
					</tr>
					<?php foreach($records as $record) :?>
						<tr id="<?php echo $record['id']; ?>">
						<td scope="row" id="payment_at"><?= $record["payment_at"] ?></td>
						<?php if($record["type_id"] === 1) :?>
							<td><i class="fa-solid fa-minus" style="color: red; font-size:24px;"></i></td>
						<?php else :?>
							<td><i class="fa-solid fa-plus" style="color: blue; font-size:24px;"></i></td>
						<?php endif ;?>
						<td scope="row" id="title"><?= mb_strimwidth($record["title"], 0, 25,'…') ?></td>
						<td scope="row" id="category_name"><?= $record["category_name"] ?></td>
						<td scope="row" id="payment"><?= number_format($record["payment"]) ?>円</td>
						<td scope="row" id="memo"><?= mb_strimwidth($record["memo"], 0, 25,'…') ?></td>
						<td scope="row" id="user_name"><?= $record["user_name"] ?></td>
						<td scope="row" id="updated_at"><?= $record["updated_at"] ?></td>
						<td scope="row" id="created_at"><?= $record["created_at"] ?></td>
						<td><button type="button" class="btn btn-info edit-btn" name="edit-record">編集</button></td>
						<td><button type="button" class="btn btn-danger delete-btn" name="delete-id">削除</button></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<div>
    <div>

	<!-- モーダルウィンドウ -->
	<div class="modal">
		<div class="modal_form">
		<h2 class="post_title">編集</h2>
		<form method="post" action="../updateRegistory.php" enctype="multipart/form-data">
		<input type="hidden" id="record_id" name="record_id">
		<input type="hidden" id="type_id" name="type_id">
		<div>
			<label>日付</label>
		</div>
			<input type="date" class="mb-2" id="edit_payment_at" name="payment_at" required>
		<div>
			<label>タイトル</label>
		</div>
			<input type="text" class="mb-2" id="edit_title"  name="title" required>
		<div>
			<label>カテゴリ</label>
			<div class="mb-2" id="modal_categories" style="background-color: white; color: brack;">
				<!-- モーダル表示する際にJavaScriptでhtmlを追加する -->
			</div>
		</div>
		<div class="amount">
				<label>金額</label>
			</div>
				<input type="text" class="mb-2" id="edit_payment" onblur="addComma(this);" 
					pattern="^((([1-9]\d*)(,\d{3})*)|0)$" name="payment" maxlength="12" min="1" required>
			<div class="pb-2">
			<div>
				<label>メモ</label>
			</div>
			<textarea name="content" id="edit_memo" cols="35" rows="5"></textarea><br>
		</div>
		<button class="btn btn-primary" type="submit" name="update" id="update">更新</button>
		<button class="btn btn-danger" id="close" type="button">キャンセル</button>
		</form>
		</div>
	</div>

	<script src="../stylesheet/js/registory.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<!-- <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
		integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" 
		integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" 
		integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous">
	</script> -->
	</body>
</html>
