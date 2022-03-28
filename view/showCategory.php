<?php
require_once __DIR__.'/../categoryController.php';
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../stylesheet/css/registory.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
        integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>show</title>
</head>
<body>
    <nav class="navbar navbar-dark bg-dark mb-4">
        <a href="login.php" class="navbar-brand">ログイン</a>
        <a href="users_new.php" class="navbar-brand text-right">新規登録</a>
    </nav>

    <div class="container mt-5">
        <h3>カテゴリ詳細一覧</h3>
        <table class="table table-striped border border-5 border">
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
                    <td scope="col" class="delet-column">削除</td>          
                </tr>
                <?php if(!$records) :?>
                    <p><?= "対象のデータは存在しません。"; ?></p> 
                <?php else :?>
                    <?php foreach($records as $record) :?>
                    <tr id="<?php echo $record['id']; ?>">
                        <td scope="row" id="payment_at"><?= $record["payment_at"] ?></td>
                        <!-- <td scope="row" id="type_name"><?= $record["type_name"] ?></td> -->
                        <?php if($record["type_id"] === 1) :?>
                            <td><i class="fa-solid fa-arrow-up-long" style="color: red; font-size:24px;"></i></td>
                        <?php else :?>
                            <td><i class="fa-solid fa-arrow-down-long" style="color: green; font-size:24px;"></i></td>
                        <?php endif ;?>
                        <td scope="row" id="title"><?= mb_strimwidth($record["title"], 0, 25,'…') ?></td>
                        <td scope="row" id="category_name"><?= $record["category_name"] ?></td>
                        <td scope="row" id="payment"><?= $record["payment"] ?>円</td>
                        <td scope="row" id="memo"><?= mb_strimwidth($record["memo"], 0, 25,'…') ?></td>
                        <td scope="row" id="user_name"><?= $record["user_name"] ?></td>
                        <td scope="row" id="updated_at"><?= $record["updated_at"] ?></td>
                        <td scope="row" id="created_at"><?= $record["created_at"] ?></td>
                        <td><button type="button" class="btn btn-info edit-btn" name="edit-record">編集</button></td>
                        <td><button type="button" class="btn btn-danger delete-btn" name="delete-id">削除</button></td>
                    </tr>
                    <?php endforeach ;?>
                <?php endif ;?>
            </tbody>
        </table>
    <div>

<!-- モーダルウィンドウ -->
<div class="modal">
    <div class="modal_form">
    <h2 class="post_title">編集</h2>
    <form method="post" action="../updateRegistory.php" enctype="multipart/form-data">
    <input type="hidden" id="record_id" name="record_id">
    <input type="hidden" id="type_id" name="type_id">
    <div class="pb-2">
            <label>日付</label>
            <input type="date" id="edit_payment_at" class="form-control" name="payment_at" required>
        </div>
        <div class="pb-2">
            <label>タイトル</label>
            <input type="text" id="edit_title" class="form-control"  name="title" required>
        </div>
        <div class="pb-2">
            <label>カテゴリ</label>
            <div id="modal_categories">
            <!-- モーダル表示する際にJavaScriptでhtmlを追加する -->
            </div>
        </div>
        <div class="amount pb-2">
                <label>金額</label>
                <input type="text" id="edit_payment" onblur="addComma(this);" 
                    pattern="^((([1-9]\d*)(,\d{3})*)|0)$" class="form-control" 
                    name="payment" maxlength="12" min="1" required>
            </div>
            <div class="pb-2">
            <div>
                <label>メモ</label>
            </div>
            <textarea name="content" id="edit_memo" class="form-control" cols="40" rows="5"></textarea><br>
        </div>
        <button class="btn btn-primary" type="submit" name="update" id="update">更新</button>
        <button class="btn btn-danger" id="close" type="button">キャンセル</button>
    </form>
    </div>
</div>

<script src="../stylesheet/js/registory.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
 
</body>
</html>