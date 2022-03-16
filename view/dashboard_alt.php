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
                <a href="#" class="navbar-brand">家計簿(グループ名)phpで実装</a> 
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
                        <text class="mx-auto h3" x="50%" y="50%" fill="#ffffff" dy=".5em" text-anchor="middle">{{selected_period.month}}月の支出</text>
                    </svg> <!-- フォントを後で変えたい -->
                        <div class="style={direction: rtl;}"> <!-- 可能なら右に寄せたい -->
                        
                        <select class="w-25 form-control select_period" name="year_month" v-model="selected_period">
                            <option disabled value="">--月を選択--</option> <!-- 最新月をデフォルト値にする -->
                            <!-- vue.jsで実装する -->
                            <option value="dog">登録月から</option>
                            <option v-for="date in sample_dates" :value="date">{{date.year}}年{{date.month}}月</option>
                        </select>
                        </div>
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
                                <p>(これは仮置きの画像)</p>
                                <p>(下記のような表を作成するテーブル)</p>
                                <table class="table">
                                    <!-- 後で colの属性を指定する -->
                                    <thead>
                                        <th v-for="columns in outgo_table_columns">{{columns}}</th>
                                    </thead>
                                    <tr v-for="category in sample_category_array">
                                        <td> <!-- 別タブで開くように設定予定 getメソッドで送る-->
                                            <a :href="detail_link + category.category_id" target="_blank" rel="noopener noreferrer">{{category.category_name}}</a>
                                        </td>
                                        <td>{{category.outgo}}</td>
                                        <td>なにかしらのデータ</td>
                                    </tr>
                                </table>
                        </div>
                </div>
            </div>
        </div> <!-- container の終わり -->
        </div> <!-- #app の終わり-->

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
        <!-- Vue.js下書き -->
        <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>
        <script>
            // サンプルデータ
            let sample_data;


            let app = new Vue({
            el :'#dashboard',
            data : {
                selected_period : '', // phpから直に最新月の直接を代入する
                detail_link : 'outgo_detail.php?ctg=',
                outgo_table_columns : ["カテゴリー",  "支出額", "(前月比とか？)"],
                sample_dates : [// phpで先に読み込ませておく データは降順
                    { year : '2021', month : 12, year_month : 20211201},
                    { year : '2022', month : 1,  year_month : 20220101},
                    { year : '2022', month : 2,  year_month : 20220201},
                    { year : '2022', month : 3,  year_month : 20220301}
                ], 
                sample_category_array :// phpで先に読み込ませておく データは昇順
                    [{
                        category_name : "食費",
                        outgo : 50000,
                        category_id : 1
                    },
                    {
                        category_name : "交通費",
                        outgo : 30000,
                        category_id : 2
                    },
                    {
                        category_name : "娯楽費",
                        outgo : 10000,
                        category_id : 3
                    }],
                    // sample_category_array : [["食費", 50000], ["交通費", 30000]],
                target_flat_score_url : null
            },
            methods : {
                add_video : function(event) {
                    //該当phpファイルに動画のurlを送り 動画IDを抽出させるメソッド。
                    let target_video_url = this.target_video_url;
                    let params = new URLSearchParams();
                    params.append('target_video_url', this.target_video_url);
                    axios
                    .post('Data/analyse_video_url.php', params)
                    .then(response => (
                        this.video_id = response.data
                    ));
                    this.add_vid_btn = "btn btn-success";
                    //console.log("-----end of function shorten_url-----------------------------");
                },
                reset_add_vid_btn : function (event) {
                    if (this.target_video_url == "") {
                        this.add_vid_btn = "btn btn-primary";
                    }
                },
                reset_add_score_btn : function (event) {
                    if (this.target_flat_score_url == "") {
                        this.add_score_btn = "btn btn-primary";
                    }
                },
                test_input : function (event) {
                    this.test = "";
                }
            }, mounted : function(){
                // 読み込み時の処理を書く
                // 
            }
        })
        </script>
    </body>
</html>