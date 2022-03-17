<?php

$error_messages = array();

require_once __DIR__.'/../class/DB_Connector_main.php';

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
    <!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flat-ui/2.3.0/css/flat-ui.min.css"> -->

    <title>registory</title>
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <a href="#" class="navbar-brand">ログイン</a>
        <a href="#" class="navbar-brand text-right">新規登録</a>
    </nav>
    <!-- 記帳登録フォーム -->
    <div class="container">
        <div class="tab-panel">
            <!-- tab -->
            <ul class="tab-group">
                <li class="tab tab-A is-active">支出</li>
                <li class="tab tab-B">収入</li>
            </ul>
            <div class="panel-group">
                <!-- 支出用記帳フォーム -->
                <div class="panel tab-A is-show">
                    <form action="../from_registory.php?type_id=1" method="post" class="mt-5">
                        <div class="pb-2">
                            <label>日付</label>
                            <input type="date" class="form-control" name="payment_at" required>
                        </div>
                        <div class="pb-2">
                            <label>タイトル</label>
                            <input type="text" class="form-control"  name="title" required>
                        </div>
                        <div class="pb-2">
                            <label>カテゴリ</label>
                            <select  class="form-control" name="category_id" style="width:100px;">
                                <?php foreach($category_outgoes as $key => $category_outgo) :?>
                                    <option value="<?= $key + 1 ?>"><?= $category_outgo ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="amount pb-2">
                            <label>金額</label>
                            <input type="text" onblur="addComma(this);" pattern="^((([1-9]\d*)(,\d{3})*)|0)$" class="mt-5 form-control" name="payment" maxlength="13" min="1" required>
                        </div>
                        <div class="pb-2">
                        <div>
                            <label>メモ</label>
                        </div>
                            <textarea name="content" class="form-control" cols="40" rows="10"></textarea><br>
                        </div>
                        <input type="submit" class="btn btn-primary mb-3" name="entry" value="登録する">
                    </form>
                </div>

                <!-- 収入用記帳フォーム -->
                <div class="panel tab-B">
                    <form action="../from_registory.php?type_id=2" method="post" class="mt-5">
                        <div class="pb-2">
                            <label>日付</label>
                            <input type="date" class="form-control" name="payment_at" required>
                        </div>
                        <div class="pb-2">
                            <label>タイトル</label>
                            <input type="text" class="form-control"  name="title" required>
                        </div>
                        <div class="pb-2">
                            <label>カテゴリ</label>
                            <select  class="form-control" name="category_id" style="width:100px;">
                            <?php foreach($category_incomes as $key => $category_income) :?>
                                <option value="<?= $key + 101 ?>"><?= $category_income ?></option>
                            <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="amount pb-2">
                            <label>金額</label>
                            <input type="text" onblur="addComma(this);" pattern="^((([1-9]\d*)(,\d{3})*)|0)$" class="mt-5 form-control" name="payment" maxlength="12" min="1" required>
                        </div>
                        <div class="pb-2">
                        <div>
                            <label>メモ</label>
                        </div>
                            <textarea name="content" class="form-control" cols="40" rows="10"></textarea><br>
                        </div>
                        <input type="submit" class="btn btn-primary mb-3" name="entry" value="登録する">
                    </form>
                </div>

            </div>        
        </div>
    </div>

    <div class="container mt-5">
        <h3>記帳内容の一覧表示</h3>
        <table class="table table-striped border border-5 border">
            <tbody>
                <!-- 一覧の項目名 -->
                <tr>
                    <td scope="row" id="payment_at">日付</td> 
                    <td scope="row" id="type_name">収支</td> 
                    <td scope="row" id="title">タイトル</td> 
                    <td scope="row" id="category_name">カテゴリー</td> 
                    <td scope="row" id="payment">金額</td> 
                    <td scope="row" id="memo">メモ</td> 
                    <td scope="row" id="user_name">ユーザ名</td> 
                    <td scope="row" id="updated_at">更新日</td> 
                    <td scope="row" id="created_at">登録日</td> 
                    <td scope="row" id="edit-btn">編集</td>          
                    <td scope="row" id="delete-btn">削除</td>          
                </tr>
                <?php foreach($records as $record) :?>
                <tr id="<?php echo $record['id']; ?>">
                    <td scope="row"><?php echo $record["payment_at"] ?></td>
                    <td scope="row"><?php echo $record["type_name"] ?></td>
                    <td scope="row"><?php echo mb_strimwidth($record["title"], 0, 25,'…') ?></td>
                    <td scope="row"><?php echo $record["category_name"] ?></td>
                    <td scope="row"><?php echo $record["payment"] ?>円</td>
                    <td scope="row"><?php echo mb_strimwidth($record["memo"], 0, 25,'…') ?></td>
                    <td scope="row"><?php echo $record["user_name"] ?></td>
                    <td scope="row"><?php echo $record["updated_at"] ?></td>
                    <td scope="row"><?php echo $record["created_at"] ?></td>
                    <td><button type="button" class="btn btn-info edit-btn" name="edit-record">編集</button></td>
                    <td><button type="button" class="btn btn-danger delete-btn" name="delete-id">削除</button></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <div>

<!-- モーダルウィンドウ -->
<div class="modal">
    <div class="post_process">
    <h2 class="post_title">編集</h2>
    <form method="post" action="../modal_registory.php?type_id=2" enctype="multipart/form-data">
    <div class="pb-2">
            <label>日付</label>
            <input type="date" class="form-control" name="payment_at" required>
        </div>
        <div class="pb-2">
            <label>タイトル</label>
            <input type="text" class="form-control"  name="title" required>
        </div>
        <div class="pb-2">
            <label>カテゴリ</label>
            <select  class="form-control" name="category_id" style="width:100px;">
            <?php foreach($category_incomes as $key => $category_income) :?>
                <option value="<?= $key + 101 ?>"><?= $category_income ?></option>
            <?php endforeach; ?>
            </select>
        </div>
        <div class="amount pb-2">
                <label>金額</label>
                <input type="text" onblur="addComma(this);" pattern="^((([1-9]\d*)(,\d{3})*)|0)$" class="mt-5 form-control" name="payment" maxlength="12" min="1" required>
            </div>
            <div class="pb-2">
            <div>
                <label>メモ</label>
            </div>
            <textarea name="content" class="form-control" cols="40" rows="10"></textarea><br>
        </div>
        <button class="btn btn-primary" type="submit" name="update" id="post">更新</button>
        <button class="btn btn-danger" id="close" type="button">キャンセル</button>
    </form>
    </div>
</div>

    <script src="../stylesheet/js/registory.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flat-ui/2.3.0/js/flat-ui.min.js" integrity="sha512-GG/1z6B4MVJdQOw35lE4otrbjd2WYV+zhXgjUR+DTeaAc7s/ijgWsexEScSOIo8J4RlhC28CVerDjYQSH89ekQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flat-ui/2.3.0/js/vendor/jquery.min.js" integrity="sha512-ju6u+4bPX50JQmgU97YOGAXmRMrD9as4LE05PdC3qycsGQmjGlfm041azyB1VfCXpkpt1i9gqXCT6XuxhBJtKg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flat-ui/2.3.0/js/vendor/respond.min.js" integrity="sha512-qWVvreMuH9i0DrugcOtifxdtZVBBL0X75r9YweXsdCHtXUidlctw7NXg5KVP3ITPtqZ2S575A0wFkvgS2anqSA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

</body>
</html>