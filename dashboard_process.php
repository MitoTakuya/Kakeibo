<?php
require(__DIR__.'\class\DB_Connector_main.php');

if (DB_Connector::connectDB()) {
    $DB_connector = new DB_Connector_main();

    /********** ユーザー・グループ情報の処理 **********/
    // 画面上部に表示したりpostしたりする用
    $kakeibo_name = '家計簿(仮)'; // = $_SESSION['group_name'];
    $group_id = 1; // = $_SESSION['group_id'];
    $user_name; // = $_SESSION['user_name']; 


    /********** 表示する期間を決める処理 **********/
    // "yyyymm"の形でpostされた日付を、"yyyymmdd" (dd = '01')に直して変数に代入する
    $date_dd = '01';
    if (isset($_POST['date'])) {
        $target_date = new DateTime($_POST['date'] . $date_dd);
    } else {
        $target_date = new DateTime();
    }
    // echo $target_date->format('Ymd');

    // カテゴリごとの支出を取り出す
    $categorized_outgo_list = $DB_connector->fetchFilteredOutgoList(
                                group_id: $group_id,
                                target_date: $target_date->format('Ymd'),
                            );

    // グラフの上に出力する
    $displayed_year = $target_date->format('Y');
    $displayed_month = $target_date->format('n');
    //print_r($categorized_outgo_list);


    /********** 日付のselect-option用のデータを用意する **********/
    // 登録日を取得する *DBC_users にメソッド追加要相談
    $registration_date = new DateTime('20190601');

    // 現在日時を取得する
    $carrent_date = new DateTime();

    // 登録日から最新月までの月のリストを作成する
    while ($registration_date <= $carrent_date) {
        $past_dates[] = array(
                            'year' => $registration_date->format('Y'),
                            'month' => $registration_date->format('n'),
                            'year_month' => $registration_date->format('Ym')
        );
        $registration_date->modify('+1 months');
    }
    // 降順に変更する
    $past_dates = array_reverse($past_dates);

    // scriptタグに埋め込むために下記のようなjsonに変換する
    /* e.g.
    *   "[{ year : '2021', month : 12, year_month : 20211201},
    *     { year : '2022', month : 1,  year_month : 20220101},... ]"
    */
    $jsonized_past_dates = json_encode($past_dates);


    /********** 円グラフ用のデータを用意する **********/
    // var_dump($categorized_outgo_list);
    foreach ($categorized_outgo_list as $row) {
        $to_json["labels"][] = $row['category_name'];
        $to_json["datasets"]["data"][] = $row['payment'];
    }
    
    $to_json["datasets"]["backgroundColor"] = [
        "#ffb3cc",  //薄い赤
        "#ffccee",  //ピンク
        "#eeccff",  //薄紫
        "#ccddff",  //薄青紫
        "#b3ccff",  //薄い青
        "#ccffff",  //空色
        "#ccffee",  //薄黄緑
        "#b3ffe6",  //ライム色
        "#ccffcc",  //黄緑
        "#ffffb3",  //明るい黄色
        "#ffffcc",  //薄黄色
        "#ffeecc",  //薄オレンジ
        "#ffe6b3",  //オレンジ
    ];
    

    // echo "<br>-----<br>";
    $jsonized_outgo_list =  json_encode($to_json, JSON_UNESCAPED_UNICODE);
    // var_dump($jsonized_outgo_list);
    DB_Connector::disconnectDB();
} else {
    // 接続失敗時にエラー画面を読み込む
    // $error = DB_Connector::$connect_error *$connect_errorはpublicな定数にしてしまう
    include(__DIR__.'/view/error.php');
    die();
}


