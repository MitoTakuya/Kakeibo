<!doctype html>
    <html lang="ja">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
            <link rel="stylesheet" href="../stylesheet/css/user.css">
            <link rel="stylesheet" href="../stylesheet/css/dashboard.css">
        <!-- FontAwesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <title>ログイン</title>
    </head>

    <body>
        <header>
            <nav class="navbar navbar-dark bg-dark">
                <a href="#" class="navbar-brand">家計簿(グループ名)</a> 
                <div class="btn-group" role="group">
                    <button id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Menu
                    </button>
                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                    <a class="dropdown-item" href="#">記帳</a>
                    <a class="dropdown-item" href="#">ログアウト</a>
                    </div>
                </div>
            </nav>
        </header>
    
        <div class="container">
            <div class="mx-auto">
                <div class="card card_property">
                    <svg class="card-img-top" width="auto" height="50" xmlns="http://www.w3.org/2000/svg" focusable="false" role="img" >
                        <rect width="100%" height="100%" fill="#87CEEB"/>
                        <text class="mx-auto" x="50%" y="50%" fill="#ffffff" dy=".5em" text-anchor="middle">何か文字入れられる</text>
                    </svg>
                    <p>(ここに「〇月の支出」と表示する)</p>
                    <div class="outgo_chart card-body mx-auto">
                            <img src="https://www.illustkit.com/wp/wp-content/uploads/2021/05/IK-2021-0228-69.png" width="500" height="500">
                    </div>
                    <p>(これは仮置きのグラフ)</p>
                    </div>
            </div>
            <div class="mx-auto">
                <div class="card card_property">
                    <p>ここにカテゴリーごとにまとめた支出のテーブルを作る</p>
                        <div class="outgo_chart card-body mx-auto">
                                <img src="https://shop.woodworks-marutoku.com/kanri/wp-content/uploads/2019/09/190926_ta_top.jpg" width="720" height="400">
                        </div>
                    <p>(これは仮置きの画像)</p>
                </div>
            </div>
        </div> <!-- container の終わり -->

        <footer class="footer text-light bg-dark">
            <nav class="navbar navbar-dark bg-dark">
                <a href="#" class="navbar-brand ">家計簿(グループ名) *後で位置を修正</a> 
            </nav>
        </footer>
        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    </body>
</html>