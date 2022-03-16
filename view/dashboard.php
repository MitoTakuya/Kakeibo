<!DOCTYPE html>
<html lang="jp">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <!-- flat-ui -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flat-ui/2.3.0/css/flat-ui.min.css">
    <link rel="stylesheet" href="../stylesheet/css/dashboard.css">
    <title>Document</title>
</head>
<body>
    
    <section class="layout">
    <!-- 画面上部分 -->
    <header class="header">
        <nav class="navbar navbar-dark bg-dark">
        <a href="#" class="navbar-brand">家計簿(グループ名)</a>
        <a href="#" class="navbar-brand text-right">ログアウト</a>
        </nav>
    </header>

    <!-- 左余白部分 -->
        <div class="leftSide">test</div>
    <!-- 右余白部分 -->
        <div class="rightSide">test</div>

        <!-- メイン部分（グラフとカテゴリーごとの支出テーブル） -->
        <section class="body_layout">
            <!-- グラフ部 -->
            <div class="body">
                <div class="graph_area jumbotron">
                    <p>(ここにグラフを作成する)</p>
                    <div class="card">
                            <svg class="bd-placeholder-img card-img-top" width="320" height="50" xmlns="http://www.w3.org/2000/svg" focusable="false" role="img" >
                                <rect width="100%" height="100%" fill="#87CEEB"/>
                                <text class="mx-auto" x="50%" y="50%" fill="#ffffff" dy=".5em" text-anchor="middle">何か文字入れられる</text>
                            </svg>
                            <div class="outgo_chart card-body mx-auto">
                                    <img src="https://www.illustkit.com/wp/wp-content/uploads/2021/05/IK-2021-0228-69.png" width="500" height="500">
                            </div>
                            <p>(これは仮置きの画像)</p>
                    </div>
                </div>

                <!-- テーブル部 -->
                <div class="outgo_area jumbotron">
                    <div class="card">
                        <p>ここにカテゴリーごとにまとめた支出のテーブルを作る</p>
                            <div class="outgo_chart card-body mx-auto">
                                    <img src="https://shop.woodworks-marutoku.com/kanri/wp-content/uploads/2019/09/190926_ta_top.jpg" width="720" height="400">
                            </div>
                        <p>(これは仮置きの画像)</p>
                    </div>
                </div>
            </div>
        </section>
        

        <!-- 画面下部分 -->
        <footer class="footer text-light bg-dark">
            (footer部)
        </footer>

    </section>
    
</body>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flat-ui/2.3.0/js/flat-ui.min.js" integrity="sha512-GG/1z6B4MVJdQOw35lE4otrbjd2WYV+zhXgjUR+DTeaAc7s/ijgWsexEScSOIo8J4RlhC28CVerDjYQSH89ekQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flat-ui/2.3.0/js/vendor/jquery.min.js" integrity="sha512-ju6u+4bPX50JQmgU97YOGAXmRMrD9as4LE05PdC3qycsGQmjGlfm041azyB1VfCXpkpt1i9gqXCT6XuxhBJtKg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flat-ui/2.3.0/js/vendor/respond.min.js" integrity="sha512-qWVvreMuH9i0DrugcOtifxdtZVBBL0X75r9YweXsdCHtXUidlctw7NXg5KVP3ITPtqZ2S575A0wFkvgS2anqSA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flat-ui/2.3.0/css/vendor/bootstrap/css/bootstrap.min.css" integrity="sha512-cp9JSDyi0CDCvBfFKYLWXevb3r8hRv5JxcxLkUq/LEtAmOg7X0yzR3p0x/g+S3aWcZw18mhxsCXyelKWmXgzzg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</html>