<?php
require_once __DIR__.'/../class/Config.php';
Config::not_ie()
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../stylesheet/css/ie.css">
    <title>Error</title>
</head>
<body>
    <div id="header-bar">
        <div id="header-inner">このブラウザでは動作保証対象外となります。 
            <br>引き続きWEBサイトを閲覧する場合は、サポートされているブラウザに切り替えてください。 
            <br>2021年以降、Windows10で閲覧・動作推奨するブラウザは下記になります。
            <br>
            <br>
            <span clas="edde" style="background: White;padding: 5px 15px;">
            <a href ="https://www.microsoft.com/ja-jp/edge" style="text-decoration: underline; color:#999;">Microsoft Edge</a>
            </span>
            <span clas="chrome" style="background: White;padding: 5px 15px;">
            <a href="https://www.google.com/chrome/" style="text-decoration: underline; color:#999;">Google Chrome</a>
            </span>
            <br>
            <br>ダウンロードとインストール方法などにつきましては、ブラウザ提供元へお問い合せください。
        </div>
    </div>
</body>
</html>