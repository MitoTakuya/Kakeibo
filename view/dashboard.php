<?php
include(__DIR__.'\../dashboard_process.php');
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
            <link rel="stylesheet" href="../stylesheet/css/dashboard.css">
        <!-- FontAwesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <title>dashboard - (家計簿名)</title>
    </head>

    <body>
        <div id="dashboard">

        <header>
            <nav class="navbar navbar-dark bg-dark">
                <a href="#" class="navbar-brand"><?= $kakeibo_name ?></a> 
                <div class="btn-group" role="group">
                    <button id="header_menu" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Menu(仮置き)
                    </button>
                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                    <a class="dropdown-item" href="./registory.php">記帳</a>
                    <a class="dropdown-item" href="#">ログアウト</a>
                    </div>
                </div>
            </nav>
        </header>

        <!-- 内容部分 -->
        <div class="container">
            <div class="mx-auto">

            <!-- カード開始部 -->
                <div class="card card_property">

                    <!-- カードheader部分 -->
                    <div class="row">
                        <svg class="card-img-top" width="320" height="75" xmlns="http://www.w3.org/2000/svg" focusable="false" role="img" >
                            <rect width="100%" height="100%" fill="#87CEEB"/>
                            <text class="mx-auto h3" x="50%" y="50%" fill="#ffffff" dy=".5em" text-anchor="middle"><?= $displayed_year ?>年 <?= $displayed_month ?>月 の支出</text>
                        </svg>
                    </div>

                    <!-- 日付選択欄 -->
                    <div>
                        <form action="#" method="post" id="selecting_date" :ref="selecting_date">
                            <select class="w-25 form-control select_period text-left float-right" name="date" v-model="selected_date" @input="selfPostDate">
                                <option disabled value="">--月を選択--</option>
                                <option v-for="date in selectable_dates" :value="date.year_month">{{date.year}}年{{date.month}}月</option>
                            </select>
                        </form>
                    </div>

                    <div class="row">
                        <!-- 目標貯金額表示 -->
                        <div class="goal_and_diff float-left w-25 col-4">
                            <h4 class="h4">目標貯金額</h4><br>
                            <h4 class="display-5"><?= number_format($goal) ?> 円</h4><br>
                            <h4 class="h4">目標まで</h4><br>
                            <h4 class="display-5"><?= number_format($difference) ?> 円</h4>
                        </div>

                        <!-- グラフ部分 -->
                        <div class="outgo_chart_area w-100 float-right col-6">
                            <?php if (count($categorized_outgo_list) === 0):?>
                                <!-- もしレコードが存在しなければ「記録がありません」 -->
                                <p>記録がありません</p>
                            <?php else:?> 
                                <!-- 支出割合グラフ -->
                                <div class="outgo_chart card-body mx-auto text-center">
                                    <canvas id="outgo_rate_chart" :ref="outgo_rate_chart"></canvas>
                                </div>
                            <?php endif;?>
                        </div>

                    </div>

                    <div class="outgo_chart card-body">
                        <table class="table w-100">
                            <?php if (count($categorized_outgo_list) === 0):?>
                                <!--
                                    グラフ部分で「記録がありません」と表示するので
                                    レコードが存在しなければこちらには何も表示しない
                                -->
                            <?php else:?> 
                                <!-- 後で colの属性を指定する -->
                                <thead>
                                    <th scope="col">カテゴリー</th>
                                    <th scope="col">支出額</th>
                                </thead>
                                    <?php foreach($categorized_outgo_list as $outgo): ?>
                                        <tr>
                                            <!-- カテゴリー名、詳細リンク -->
                                            <td scope="row">
                                                <form action="./show_category.php" method="get">
                                                    <input type="submit" value="<?= $outgo['category_name'] ?>" class="btn btn-link">
                                                    <input type="hidden" name="id" value=<?= $outgo['category_id'] ?>>
                                                </form>
                                            </td>

                                            <!-- 支出金額 -->
                                            <td scope="row"><?= number_format($outgo['payment']) ?> 円</td>
                                        </tr>
                                    <?php endforeach; ?>
                            <?php endif;?>
                        </table>
                    </div>
                </div>
            </div>
        </div> <!-- container の終わり -->
        </div> <!-- #app の終わり-->

        <footer class="footer text-light bg-dark">
            <nav class="navbar navbar-dark bg-dark">
                <a href="#" class="navbar-brand "><?= $kakeibo_name ?></a> 
            </nav>
        </footer>
        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
        <!-- Vue.js -->
        <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>
        <!-- Chart.js -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.1/chart.min.js" integrity="sha512-QSkVNOCYLtj73J4hbmVoOV6KVZuMluZlioC+trLpewV8qMjsWqlIQvkn1KGX2StWvPMdWGBqim1xlC8krl1EKQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <!-- axios -->
        <script src="https://unpkg.com/axios/dist/axios.min.js"></script>

        <script>
            /********** Canvasのサイズ自動調整 **********/
            var w = $('.outgo_chart').width();
            var h = $('.outgo_chart').height();
            $('#outgo_rate_chart').attr('width', w);
            $('#outgo_rate_chart').attr('height', h);


            /********** グラフ描画、更新処理 **********/
            let app = new Vue({
            el :'#dashboard',
            data : {
                selected_date : '', // phpから直に最新月の直接を代入する

                selectable_dates : <?= $jsonized_past_dates ?>, //php から読み込む
                selecting_date : "selecting_date",  // selectタグのdomを格納する

                outgo_rate_chart : "outgo_rate_chart", // canvasタグのdomを格納する
                chart_data : <?= $jsonized_outgo_list ?>, //グラフ用の
                outgo_chart : null // chart.jsのインスタンスを格納する
            },
            methods : {
                // ページを更新する
                selfPostDate : function (event) {
                    //$ref['selecting_date'].submit();
                    //console.log(this.$refs['selecting_date']);
                    this.$refs['selecting_date'].submit();
                }
            },
            mounted: function(){
                //console.log(this.chart_data);
                chart_data = JSON.stringify(<?= $jsonized_outgo_list ?>);
                chart_data = JSON.parse(chart_data);
                console.log(chart_data);
                this.outgo_chart = new Chart(this.$refs['outgo_rate_chart'], {
                    type: 'pie',
                    data: {
                        labels: chart_data.labels,
                        datasets: [{
                            data: chart_data.datasets.data,
                            backgroundColor: chart_data.datasets.backgroundColor,
                            weight: 100,
                        }],
                    },
                });
            }
        })
        </script>
    </body>
</html>