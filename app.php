<?php
require_once __DIR__."/system/oauth.php";
require_once __DIR__."/system/lib.php";
require_once __DIR__."/config.php";

date_default_timezone_set("Asia/Tokyo");

if(!isset($_SESSION["user"])){
    header("Location: ".url($client_id,$redirect_url,$scopes));
}

if(isset($_POST["createServer"])){
    createServer($_SESSION["id"],htmlspecialchars($_POST["createServer"]));
}

$server = preg_replace("/[^0-9]/","",$_GET["server"]);
if(!empty($server)){
    $server = getServer($server);
    if($server){
        if(isset($_POST["createMessage"])){
            createMessage($_SESSION["id"],$server["id"],htmlspecialchars($_POST["createMessage"]));
        }
        $messages = getMessages($server["id"]);
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>WebChat</title>

        <link rel="apple-touch-icon" sizes="180x180" href="./assets/img/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="./assets/img/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="./assets/img/favicon-16x16.png">
        <link rel="manifest" href="./assets/img/site.webmanifest">
        <link rel="mask-icon" href="./assets/img/safari-pinned-tab.svg" color="#5bbad5">
        <link rel="shortcut icon" href="./assets/img/favicon.ico">
        <meta name="msapplication-TileColor" content="#da532c">
        <meta name="msapplication-config" content="./assets/img/browserconfig.xml">
        <meta name="theme-color" content="#ffffff">

        <head prefix="og: https://ogp.me/ns# fb: https://ogp.me/ns/ fb# prefix属性: https://ogp.me/ns/ prefix属性#">
        <meta property="og:url" content="https://chat.takadev.tk/" />
        <meta property="og:type" content="website" />
        <meta property="og:title" content="チャット" />
        <meta property="og:description" content="Web上で使えるオープンチャットツール" />
        <meta property="og:site_name" content="WebChat" />
        <meta property="og:image" content="https://chat.takadev.tk/assets/img/icon.png" />

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="stylesheet" href="./assets/css/main.css">
    </head>
    <body>
        <header>
            <nav class="navbar navbar-expand-md navbar-light bg-light fixed-top">
                <div class="container-fluid">
                    <a class="navbar-brand text-darl" href="./">
                        <img src="./assets/img/icon.png" alt="アイコン" width="30" height="30" class="d-inline-block align-text-top">
                        WebChat
                    </a>
                    <form class="d-flex">
                        <div class="dropdown">
                            <button class="btn btn-outline-success dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                <?= $_SESSION["username"] ?>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                <li><a class="dropdown-item" href="./account">アカウント</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="./system/logout">ログアウト</a></li>
                                <li><a class="dropdown-item text-primary" href="<?= url($client_id,$redirect_url,$scopes) ?>">データ同期</a></li>
                            </ul>
                        </div>
                    </form>
                </div>
            </nav>
        </header>
	    <main>
            <div class="app">
                <?php if(!empty($server)){ ?>
                    <h1 class="title"><?= $server["name"] ?></h1>
                    <form id="messageForm" class="row g-3" action="./app?server=<?= $server["id"] ?>" method="post">
                        <div class="col-auto">
                            <input id="messageInput" name="createMessage" type="text" class="form-control" placeholder="メッセージを送信" autocomplete="off">
                        </div>
                    </form>
                    <?php foreach(array_reverse($messages) as $message){ ?>
                        <div id="<?= $message["id"] ?>">
                            <div id="manageMessage" class="btn-group">
                                <a class="btn btn-outline-secondary btn-sm dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false"></a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" onclick="linkCopy('<?= $server['id'] ?>','<?= $message['id'] ?>')">リンクをコピー</a></li>
                                    <li><a class="dropdown-item">メッセージを削除</a></li>
                                </ul>
                            </div>
                            <h6 class="messageUser"><?= getUser($message["user"])["name"] ?>・<?= date("Y/m/d H:i",$message["time"]) ?></h6>
                            <p class="messageText"><?= mb_wordwrap($message["text"],20,"<br/>",true) ?></p>
                        </div>
                    <?php } ?>
                <?php }else{ ?>
                    <h1 class="title">サーバー一覧</h1>
                    <form id="serverForm" class="row g-3" action="./app" method="post">
                        <div class="col-auto">
                            <input id="serverInput" name="createServer" type="text" class="form-control" placeholder="サーバーを作成" autocomplete="off">
                        </div>
                    </form>
                    <ul class="list-group">
                        <?php 
                            foreach(getServers() as $server){ 
                                $server = getServer(basename($server));
                        ?>  
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                            <a href="./app?server=<?= $server["id"] ?>"><?= $server["name"] ?></a>
                                <span class="badge bg-secondary"><?= count(getMessages($server["id"])) ?></span>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } ?>
            </div>
	    </main>
        <script src="./assets/js/checkServer.js"></script>
        <script src="./assets/js/checkMessage.js"></script>
        <script src="./assets/js/linkCopy.js"></script>
        <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
    </body>
</html>