<?php
require_once __DIR__."/discord.php";
require_once __DIR__."/../config.php";

login($redirect_url,$client_id,$secret_id);
get_user();

header("Location: ../");
exit;
?>