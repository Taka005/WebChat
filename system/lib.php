<?php
function is_animated($image){
	$ext = substr($image, 0, 2);
	if($ext == "a_"){
		return ".gif";
	}else{
		return ".png";
	}
}

function createId($n){
    $random = substr(str_shuffle("01234567890123456789"),0,$n);
    return $random;
}

function mb_wordwrap($string, $width, $break, $cut){
    if(!$cut){
        $regexp = "#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){".$width.",}\b#U";
    }else{
        $regexp = "#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){".$width."}#";
    }
    $string_length = mb_strlen($string,"UTF-8");
    $cut_length = ceil($string_length / $width);
    $i = 1;
    $return = "";
    while ($i < $cut_length){
        preg_match($regexp, $string, $matches);
        $new_string = (!$matches ? $string : $matches[0]);
        $return .= $new_string.$break;
        $string = substr($string, strlen($new_string));
        $i++;
    }
    return $return.$string;
}

//システム
//ユーザー

/**
 * @return Array ユーザー配列
 */
function getUsers(){
	return glob("./data/server/*.json");
}

/**
 * @param String $id ユーザーID
 * @return Object ユーザーデータ
 */
function getUser($id){
	$user = file_get_contents("./data/user/".$id.".json");
	if(!$user) return false;
	return json_decode($user,true);
}

/**
 * @param Object $user ユーザーデータ
 */
function createUser($user){
	file_put_contents("../data/user/".$user["id"].".json",json_encode(array(
		"id" => $user["id"],
		"name" => $user["username"],
		"discriminator" => $user["discriminator"],
		"avatar" => "https://cdn.discordapp.com/avatars/".$user["id"]."/".$user["avatar"].is_animated($user["avatar"])."?size=1024",
		"color" => $user["accent_color"],
		"time" => time()
	),JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PARTIAL_OUTPUT_ON_ERROR));
}

//サーバー

/**
 * @return Array サーバーID一覧
 */
function getServers(){
	return glob("./data/server/*",GLOB_ONLYDIR);
}

/**
 * @param Object $id サーバーID
 * @return Object サーバー情報
 */
function getServer($id){
	$server = file_get_contents("./data/server/".$id."/setting.json");
	if(!$server) return false;
	return json_decode($server,true);
}

/**
 * @param String $user ユーザーID
 * @param String $name サーバー名
 */
function createServer($user,$name){
	$id = createId(12);
	mkdir("./data/server/".$id);

	file_put_contents("./data/server/".$id."/setting.json",json_encode(array(
		"id" => $id,
		"name" => $name,
		"owner" => $user,
        "time" => time()
	),JSON_UNESCAPED_SLASHES|JSON_PARTIAL_OUTPUT_ON_ERROR));
	
	file_put_contents("./data/server/".$id."/message.json",json_encode(array(),JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PARTIAL_OUTPUT_ON_ERROR));

	header("Location: ./app?server=".$id);
}

/**
 * @param String $user ユーザーID
 * @param String $id サーバーID
 * @return Boolean 成功か失敗か
 */
function deleteServer($user,$id){
	$setting = json_decode(file_get_contents("./data/server/".$id."/setting.json"),true);

	if($setting["owner"] == $user){
		unlink("./data/server/".$id."/setting.json");
		unlink("./data/server/".$id."/message.json");

		return rmdir("./data?server=".$id);
	}else{
		return false;
	}
}

//メッセージ

/**
 * @param String $id サーバーID
 * @return Array メッセージ配列
 */
function getMessages($id){
	$message = file_get_contents("./data/server/".$id."/message.json");
	if(!$message) return false;
	return json_decode($message,true);
}

/**
 * @param String $server サーバーID
 * @param String $id メッセージID
 * @return Object メッセージデータ
 */
function getMessage($server,$id){
	$message = json_decode(file_get_contents("./data/server/".$id."/message.json"),true);
	return array_search($id,array_column($message,"id"));
}

/**
 * @param String $user ユーザーID
 * @param String $server サーバーID
 * @param String $text メッセージ
 */
function createMessage($user,$server,$text){
	$message = json_decode(file_get_contents("./data/server/".$server."/message.json"),true);

	array_push($message,array(
		"id" => createId(12),
		"user" => $user,
		"server" => $server,
		"text" => $text,
		"time" => time()
	));
	
	file_put_contents("./data/server/".$server."/message.json",json_encode($message,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PARTIAL_OUTPUT_ON_ERROR));

	header("Location: ./app?server=".$server);
}

/**
 * @param String $user ユーザーID
 * @param String $server サーバーID
 * @param String $id メッセージID
 * @return Boolean 成功か失敗か
 */
function deleteMessage($user,$server,$id){
	$messages = json_decode(file_get_contents("./data/server/".$server."/message.json"),true);
	$message = array_search($id,array_column(json_decode(file_get_contents("./data/server/".$server."/message.json"),true),"id"));

	if($message["user"] == $user){
		return array_splice($messages,$message);
	}else{
		return false;
	}
}
?>