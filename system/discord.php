<?php
require_once __DIR__."/lib.php";

session_start();

function gen_state(){
    $_SESSION["state"] = bin2hex(openssl_random_pseudo_bytes(12));
    return $_SESSION["state"];
}

function url($clientid,$redirect,$scope){
    return "https://discordapp.com/oauth2/authorize?response_type=code&client_id=".$clientid."&redirect_uri=".$redirect."&scope=".$scope."&state=".gen_state();
}

function login($redirect_url,$client_id,$client_secret){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, "https://discord.com/api/oauth2/token");
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query(array(
        "client_id" => $client_id,
        "client_secret" => $client_secret,
        "grant_type" => "authorization_code",
        "code" => $_GET["code"],
        "redirect_uri" => $redirect_url
    )));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $results = json_decode(curl_exec($curl), true);
    curl_close($curl);
    $_SESSION["access_token"] = $results["access_token"];
}

function get_user(){
    $curl = curl_init();
    curl_setopt($curl,CURLOPT_URL, "https://discord.com/api/users/@me");
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl,CURLOPT_HTTPHEADER,array(
        "Content-Type: application/x-www-form-urlencoded",
        "Authorization: Bearer ".$_SESSION["access_token"]
    ));
    $res = json_decode(curl_exec($curl),true);
    curl_close($curl);

    $_SESSION["user"] = $res;
    $_SESSION["username"] = $res["username"];
    $_SESSION["discriminator"] = $res["discriminator"];
    $_SESSION["id"] = $res["id"];
    $_SESSION["avatar"] = "https://cdn.discordapp.com/avatars/".$res["id"]."/".$res["avatar"].is_animated($res["avatar"])."?size=1024";
    if($res["accent_color"]) $_SESSION["accent_color"] = $res["accent_color"];

    if(!empty($res["id"])){
        createUser($res);
    }
}
?>