<?php
//後で消すファイル
$error_messages = array();

require_once __DIR__.'/../class/DbConnectorMain.php';

    //★仮置き sessionグループIDを使用する予定
    $group_id = 1;

    //インスタンス作成
    $db_connect = new DbConnectorMain;

    //メインTBLより特定グループのレコード取得する
    $records = $db_connect->fetchGroupRecords($group_id);

    //カテゴリTBLよりカテゴリ名を取得する
    $categories = $db_connect->fetchCategoryColumns();

    //★接続エラーが起きた場合どうするか？ログイン画面にリダイレクトする？
    if(!$categories) {
        $error_messages = $categories;
        var_dump($error_messages);
        exit;
    }
    
    //収支別カテゴリに分ける
    $category_outgoes = $categories[1];
    $category_incomes = $categories[2];

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
		<title>registory_clone</title>
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
				<div class="registory-box">
					<div class="panel-group mt-1">
						<div class="panel tab-A is-show p-2">
						<form action="../from_registory.php?type_id=1" method="post">
							<div class="form-group">
								<p><i class="fa fa-lock"></i>
								<label>日付</label>
								<input type="date" class="form-control" name="payment_at" required>
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
						<form action="../from_registory.php?type_id=1" method="post">
							<div class="form-group">
								<p><i class="fa fa-lock"></i>
								<label>日付</label>
								<input type="date" class="form-control" name="payment_at" required>
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
		
		<script src="../stylesheet/js/registory.js"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
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
