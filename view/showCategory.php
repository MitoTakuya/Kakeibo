<?php
require_once __DIR__.'/../categoryController.php';
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
        integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="../stylesheet/css/registory.css">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>show</title>
</head>
<body>
    <!-- ヘッダー -->
    <?php include __DIR__ . "/_header.php" ?>
    <div class="container mt-5">
        <div>
            <p class="show-table text-center mb-4">
            <?php if (isset($current_cattegory['category_name'])): ?>
                カテゴリ：<?php echo $current_cattegory['category_name'] ?>
            <?php else :?>
                カテゴリ：データが存在しません。
            <?php endif ;?>
            </p>
        </div>
        <div class="registory-box table-responsive">
            <table class="table table-striped border border-5 border">
                <tbody>
                    <!-- 一覧の項目名 -->
                    <tr>
                        <td scope="col" class="payment_at">日付</td> 
                        <td scope="col" class="type_name">収支</td> 
                        <td scope="col" class="title">タイトル</td> 
                        <td scope="col" class="payment">金額</td> 
                        <td scope="col" class="memo">メモ</td> 
                        <td scope="col" class="user_name">ユーザ名</td> 
                        <td scope="col" class="updated_at">更新日</td> 
                        <td scope="col" class="edit-column">編集</td>          
                        <td scope="col" class="delet-column">削除</td>          
                    </tr>
                    <?php foreach($records as $record) :?>
                    <tr id="<?= $record['id']; ?>">
                        <td scope="row" id="payment_at"><?= date('Y-m-d', strtotime(Config::h($record["payment_at"]))) ?></td>
                        <?php if($record["type_id"] === 1) :?>
                            <td><i class="fa-solid fa-minus" style="color: red; font-size:24px;"></i></td>
                        <?php else :?>
                            <td><i class="fa-solid fa-plus" style="color: blue; font-size:24px;"></i></td>
                        <?php endif ;?>
                        <td scope="row" id="title"><?= Config::h(mb_strimwidth($record["title"], 0, 25,'…')) ?></td>
                        <td scope="row" id="payment"><?= number_format($record["payment"]) ?>円</td>
                        <td scope="row" id="memo"><?= Config::h(mb_strimwidth($record["memo"], 0, 25,'…')) ?></td>
                        <td scope="row" id="user_name"><?= Config::h($record["user_name"]) ?></td>
                        <td scope="row" id="updated_at"><?= date('Y-m-d', strtotime(Config::h($record["updated_at"]))) ?></td>
                        <td><button type="button" class="btn btn-info edit-btn" name="edit-record">編集</button></td>
                        <td><button type="button" class="btn btn-danger delete-btn" name="delete-id">削除</button></td>
                    </tr>
                    <?php endforeach ;?>
                </tbody>
            </table>
        </div>
        <div class="text-center mt-5">	
            <a href="http://<?= $_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']) ?>/dashboard.php">カテゴリ一覧に戻る</a>
        </div>
    </div>
    <div class="mt-3"></div>
    <!-- 支出詳細のリンク -->
    <?php if (isset($payment)): ?>
        <div class="container-fluid">
            <div class="row justifyr">
                <div class="col-md-4"></div>
                支出：
                <div class="btn-group" role="group" aria-label="Basic example">
                    <?php foreach($payment as $pay): ?>
                        <form action="./showCategory.php?id=<?= $pay['category_id'] ?>" method="post">
                            <input type="submit" name="category_name" value="<?php echo $pay['category_name'] ?>" class="btn btn-outline-dark">
                            <input type="hidden" name="token" value=<?= $_SESSION['token'] ?>>
                        </form>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?> 
    <!-- 収入詳細のリンク -->
    <?php if (isset($income)): ?>
        <div class="container-fluid">
            <div class="row justifyr">
                <div class="col-md-4"></div>
                収入：
                <div class="btn-group" role="group" aria-label="Basic example">
                    <?php foreach($income as $in): ?>
                        <form action="./showCategory.php?id=<?= $in['category_id'] ?>" method="post">
                            <input type="submit" name="category_name" value="<?php echo $in['category_name'] ?>" class="btn btn-outline-dark">
                            <input type="hidden" name="token" value=<?= $_SESSION['token'] ?>>
                        </form>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?> 
    
    <!-- ページネーション -->
    <?php if($max_page > 1) :?>
        <div class="container mb-5">
            <nav aria-label="Page navigation example">
            <ul class="pagination justify-content-end">
                <?php if($now > 1) :?>
                    <li class="page-item">
                        <a class="page-link" href="showCategory.php?id=<?= $category_id ?>&page_id=<?= $previous ?>">前へ</a>
                    </li>
                <?php else :?>
                    <li class="page-item disabled">
                        <a class="page-link">前へ</a>
                    </li>
                <?php endif ;?>
                <?php for($i = 1; $i <= $max_page; $i++) :?>
                    <?php if($i == $now) :?>
                        <li class="page-item disabled"><a class="page-link" id="carrent_page"><?= $now ?></a></li>
                    <?php else :?>
                        <li class="page-item"><a class="page-link" id="page-num<?= $i ?>" href='showCategory.php?id=<?= $category_id ?>&page_id=<?= $i ?>'><?= $i ?></a></li>
                    <?php endif ;?>
                <?php endfor ;?>
                <?php if($now < $max_page) :?>
                    <li class="page-item" id="next-page">
                        <a class="page-link" href="showCategory.php?id=<?= $category_id ?>&page_id=<?= $next ?>">次へ</a>
                    </li>
                <?php else :?>
                    <li class="page-item disabled">
                        <a class="page-link">次へ</a>
                    </li>
                <?php endif ;?>
            </ul>
            </nav>
        </div>
    <?php endif ;?>

    <!-- モーダルウィンドウ -->
	<div class="modal"></div>
	<div class="edit_form">
		<h2 class="post_title">編集</h2>
		<form method="post" action="../updateRegistory.php" enctype="multipart/form-data">
		<input type="hidden" value="<?php echo $_SESSION['token']; ?>" name="token">
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
		<!-- Optional JavaScript -->
		<!-- jQuery first, then Popper.js, then Bootstrap JS -->
		<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
			integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"
			integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"
			integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>

        <script src="../stylesheet/js/registory.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
 
</body>
</html>