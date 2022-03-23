<?php

$error_messages = array();

require_once __DIR__.'/class/DB_Connector_main.php';

    //★仮置き sessionグループIDを使用する予定
    $group_id = 1;

    //インスタンス作成
    $db_connect = new DB_Connector_main;

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


<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../stylesheet/css/registory.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flat-ui/2.3.0/css/vendor/bootstrap/css/bootstrap.min.css" integrity="sha512-cp9JSDyi0CDCvBfFKYLWXevb3r8hRv5JxcxLkUq/LEtAmOg7X0yzR3p0x/g+S3aWcZw18mhxsCXyelKWmXgzzg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>top</title>
</head>
<body>
<div class="pb-2">
	<label>カテゴリ（支出）</label>
	<ul>
		<?php foreach($category_outgoes as $key => $category_outgo) :?>
			<a href="/kakeibo/view/show_category.php?id=<?= $key + 1 ?>">
				<li value="<?= $key + 1 ?>"><?= $category_outgo ?></li>
			</a>	
		<?php endforeach; ?>
	</ul>
</div>
<div class="pb-2">
	<label>カテゴリ（収入）</label>
	<ul>
		<?php foreach($category_incomes as $key => $category_income) :?>
			<a href="/kakeibo/view/show_category.php?id=<?= $key + 101 ?>">
				<li value="<?= $key + 101 ?>"><?= $category_income ?></li>
			</a>
		<?php endforeach; ?>
	</ul>
</div>
    
<script src="../stylesheet/js/registory.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flat-ui/2.3.0/js/flat-ui.min.js" integrity="sha512-GG/1z6B4MVJdQOw35lE4otrbjd2WYV+zhXgjUR+DTeaAc7s/ijgWsexEScSOIo8J4RlhC28CVerDjYQSH89ekQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flat-ui/2.3.0/js/vendor/jquery.min.js" integrity="sha512-ju6u+4bPX50JQmgU97YOGAXmRMrD9as4LE05PdC3qycsGQmjGlfm041azyB1VfCXpkpt1i9gqXCT6XuxhBJtKg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flat-ui/2.3.0/js/vendor/respond.min.js" integrity="sha512-qWVvreMuH9i0DrugcOtifxdtZVBBL0X75r9YweXsdCHtXUidlctw7NXg5KVP3ITPtqZ2S575A0wFkvgS2anqSA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

</body>
</html>