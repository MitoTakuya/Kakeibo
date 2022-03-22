<?php
include('../dashboard_process.php');
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
        <title>ログイン</title>
    </head>

    <body>
        <div id="dashboard">

        <header>
            <nav class="navbar navbar-dark bg-dark">
                <a href="#" class="navbar-brand"><?= $kakeibo_name ?></a> 
                <div class="btn-group" role="group">
                    <button id="header_menu" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Menu
                    </button>
                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                    <a class="dropdown-item" href="#">記帳</a>
                    <a class="dropdown-item" href="#">ログアウト</a>
                    </div>
                </div>
            </nav>
        </header>

        <!-- 内容部分 -->
        <div class="container">
            <div class="mx-auto">
                <div class="card card_property">
                    <svg class="card-img-top" width="320" height="75" xmlns="http://www.w3.org/2000/svg" focusable="false" role="img" >
                        <rect width="100%" height="100%" fill="#87CEEB"/>
                        <text class="mx-auto h3" x="50%" y="50%" fill="#ffffff" dy=".5em" text-anchor="middle"><?= $displayed_year ?>年 <?= $displayed_month ?>月 の支出</text>
                    </svg> <!-- フォントを後で変えたい -->
                        <div class="style={direction: rtl;}"> <!-- 可能なら右に寄せたい -->

                        <!-- 日付選択 -->
                        <form action="#" method="post" id="selecting_date" :ref="selecting_date">
                            <select class="w-25 form-control select_period" name="date" v-model="selected_date" @input="selfPostDate">
                                <option disabled value="">--月を選択--</option> <!-- 最新月をデフォルト値にする -->
                                <option v-for="date in selectable_dates" :value="date.year_month">{{date.year}}年{{date.month}}月</option>
                            </select>
                        </form>
                        </div>
                        <!-- 支出割合グラフ -->
                        <div class="outgo_chart card-body mx-auto" style="width:600px">
                                <canvas id="outgo_rate" style="width:600px"></canvas>
                        </div>
                        
                    </div>
            </div>

            <div class="mx-auto">
                <div class="card card_property">
                        <div class="outgo_chart card-body mx-auto">
                                <table class="table">
                                    <?php if (count($categorized_outgo_list) === 0):?>
                                        <!-- もしレコードが存在しなければ「記録がありません」 -->
                                            <p>記録がありません</p>
                                    <?php else:?> 
                                        <!-- 後で colの属性を指定する -->
                                        <col width="500">
                                        <thead>
                                            <th>カテゴリー</th>
                                            <th>支出額</th>
                                            <!-- <th>前月比とか？</th> -->
                                        </thead>
                                            <?php foreach($categorized_outgo_list as $outgo): ?>
                                            <tr>
                                                <td> <!-- 別タブで開くように設定予定 getメソッドで送る-->
                                                    <a href="" target="_blank" rel="noopener noreferrer"><?= $outgo['category_name'] ?></a>
                                                </td>
                                                <td><?= $outgo['payment'] ?></td>
                                                <!-- <td>なにかしらのデータ</td> -->
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
        <!-- Vue-Chart.js -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js"></script>
        <script src="https://unpkg.com/vue-chartjs/dist/vue-chartjs.min.js"></script>
        <!-- axios -->
        <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
        <script>
            // vuejsでの実装方法を確認中のため、仮置き
            var ctx = document.getElementById('outgo_rate').getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'pie',
                data: {
                        labels: ['Red', 'Green', 'Blue'],
                        datasets: [{
                            data: [10, 20, 30],
                            backgroundColor: ['#f88', '#484', '#48f'],
                            weight: 100,
                        }],
                    },
            });
            console.log(ctx);

            let app = new Vue({
            el :'#dashboard',
            data : {
                selected_date : '', // phpから直に最新月の直接を代入する

                selectable_dates : <?= $jsonized_past_dates ?>, //php から読み込む
                selecting_date : "selecting_date",

                chart_area : null,
                outgo_list : <?= $jsonized_outgo_list ?>
            },
            methods : {
                // displayChart : async function(event) {
                //     // 仮置きしている非同期処理用メソッド
                //     let params = new URLSearchParams();
                //     let path = '../ajax_dashbord.php'
                //     // params.append('param_name', );
                //     axios
                //     .post(path, params)
                //     .then(response => {
                //         console.log(response.data);
                //         return response.data
                //     });
                // },
                
                selfPostDate : function (event) {
                    //$ref['selecting_date'].submit();
                    console.log(this.$refs['selecting_date']);
                    this.$refs['selecting_date'].submit();
                }
            }// ,   後で修正
            // mounted: function(){
            //     console.log(this.outgo_list);
            //     this.chart_area = this.$refs['outgo_chart'];
            //     console.log(this.$refs);
            //     // this.outgo_chart = new Chart(this.chart_area, {
            //     //     type: 'pie',
            //     //     data: 
            //     // });
            // }
        })
        </script>
    </body>
</html>