<?php
require_once __DIR__ . '/init.php';
require(__DIR__.'\class\DbConnectorMain.php');

try {
    DbConnector::connectDB();

    /********** ユーザー・グループ情報の処理 **********/
    // 画面上部に表示したりpostしたりする用
    $user_id = $_SESSION['id']; 
    $group_id = $_SESSION['group_id'];

    $goal = DbConnectorUserGroups::fetchGoal($group_id);
    $total_balance = DbConnectorMain::fetchBalance($group_id);
    $difference = $goal - $total_balance;

    /********** 表示する期間を決める処理 **********/
    // "yyyymm"の形でpostされた日付を、"yyyymmdd" (dd = '01')に直して変数に格納する
    $date_dd = '01';
    if (isset($_POST['date'])) {
        $target_date = new DateTime($_POST['date'] . $date_dd);
    } else {
        $target_date = new DateTime();
    }
    // echo $target_date->format('Ym');

    // カテゴリごとの合計額を取り出す
    $categorized_list = DbConnectorMain::fetchCategorizedList(
        group_id: $group_id,
        target_date: $target_date->format('Ymd'),
    );

    // 合計額を支出と収入に分ける
    $record_exists = count($categorized_list) > 0;
    $categorized_outgo_list = $categorized_list[0];
    $categorized_income_list = $categorized_list[1];

    //  支出と収入それぞれでレコードが存在するか
    $outgo_record_exists = count($categorized_outgo_list) > 0;
    $income_record_exists = count($categorized_income_list) > 0;

    // グラフの上に出力する日付
    $displayed_year_month = $target_date->format('Y年n月');


    /********** 日付のselect-option用のデータを用意する **********/
    // 最も購入日付が古いレコードの日付を取得する（無ければ当日が取得される）
    $registration_date = DbConnectorMain::fetchOldestDate($group_id);
    $registration_date = new DateTime($registration_date);

    // 現在日時を取得する
    $carrent_date = new DateTime();

    // 登録日から最新月までの月のリストを作成する
    while ($registration_date <= $carrent_date->modify('last day of')) {
        $past_dates[] = array(
            'year' => $registration_date->format('Y'),
            'month' => $registration_date->format('n'),
            'year_month' => $registration_date->format('Ym')
        );
        // echo $registration_date->format('Ymd')."<br>";
        $registration_date->modify('+1 months');
    }
    // print_r($past_dates);
    // 降順に変更する
    $past_dates = array_reverse($past_dates);

    // scriptタグに埋め込むために下記のようなjsonに変換する
    /* e.g.
    *   "[{ year : '2021', month : 12, year_month : 20211201},
    *     { year : '2022', month : 1,  year_month : 20220101},... ]"
    */

    // javascript内に埋め込める形に直す
    $jsonized_past_dates = json_encode($past_dates);


    /********** 円グラフ用のデータを用意する **********/
    // DBから取得したデータをグラフに使える形に直す
    foreach ($categorized_outgo_list as $row) {
        $to_json["labels"][] = $row['category_name'];
        $to_json["datasets"]["data"][] = $row['payment'];
    }
    
    // パステルカラーで統一
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
    
    $jsonized_outgo_list =  json_encode($to_json, JSON_UNESCAPED_UNICODE);
    DbConnector::disconnectDB();

} catch (Exception $e) {
    // 接続失敗時にエラー画面を読み込む
    $error_code = $e->getCode();
    $error_message = Config::getErrorMessage($error_code);

    // echo $error_code;
    // echo $e->getMessage();

    include(__DIR__.'/view/error.php');
    die();
}


