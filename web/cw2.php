<?php

date_default_timezone_set('Asia/Tokyo');
$tdate = date("YmdHis");

//環境変数の取得
$db_host =  getenv('DB_HOST');
$db_name =  getenv('DB_NAME');
$db_pass =  getenv('DB_PASS');
$db_user =  getenv('DB_USER');
$workspace_id = getenv('CVS_WORKSPASE_ID');
$workspace_id_shi = getenv('CVS_WORKSPASE_ID_SHI');
$username = getenv('CVS_USERNAME');
$password = getenv('CVS_PASS');

//DB接続
$conn = "host=".$db_host." dbname=".$db_name." user=".$db_user." password=".$db_pass;
$link = pg_connect($conn);

$param = $_POST['param'];
$g1meisho= $_POST['g1meisho'];
$g2meisho= $_POST['g2meisho'];
$sword= $_POST['sword'];

$data = "";

error_log("★★★★★★★★★★★★★★★★★★g1meisho:".$g1meisho." g2meisho:".$g2meisho." param:".$param." sword:".$sword);

switch($param) {
	case 'intentSearch':
		intentSearch();
		break;
	case 'intentUpdate':
		intentUpdate();
		break;
	case 'intentDelete':
		intentDelete();
		break;
	case 'entitySearch':
		entitySearch();
		break;
	case 'entityUpdate':
		entityUpdate();
		break;
	case 'entityDelete':
		entityDelete();
		break;
	default:
		continue;
}

function intentSearch(){
	global $url,$g1meisho,$workspace_id_shi;
	$url = "https://gateway.watsonplatform.net/conversation/api/v1/workspaces/".$workspace_id_shi."/intents/".$g1meisho."/examples?version=2017-05-26&export=true";
	$jsonString = callWatson2();
	$json = json_decode($jsonString, true);
	$arr = array();
	foreach ($json["examples"] as $value){
		array_push($arr,$value["text"]);
	}
	echo json_encode($arr);
}

function intentUpdate(){
	global $url,$g1meisho,$workspace_id_shi,$sword,$data;
	$url = "https://gateway.watsonplatform.net/conversation/api/v1/workspaces/".$workspace_id_shi."/intents/".$g1meisho."/examples?version=2017-05-26";
	$data = array("text" => $sword);
	$jsonString = callWatson();
	$json = json_decode($jsonString, true);
	if($json["text"] == $sword){
		echo json_encode("OK");
	}else{
		echo json_encode("NG");
	}
}

function intentDelete(){
	global $url,$g1meisho,$workspace_id_shi,$sword,$data;
	$url = "https://gateway.watsonplatform.net/conversation/api/v1/workspaces/".$workspace_id_shi."/intents/".$g1meisho."/examples/".urlencode($sword)."?version=2017-05-26";
	$result = callWatson3();
	error_log($result);
	if($result == "200"){
		echo json_encode("OK");
	}else{
		echo json_encode("NG");
	}
}

function entitySearch(){
	global $url,$g1meisho,$g2meisho,$workspace_id_shi;
	$url = "https://gateway.watsonplatform.net/conversation/api/v1/workspaces/".$workspace_id_shi."/entities/".$g1meisho."/values/".urlencode($g2meisho)."/synonyms?version=2017-05-26";
	$jsonString = callWatson2();
	$json = json_decode($jsonString, true);
	$arr = array();
	foreach ($json["synonyms"] as $value){
		array_push($arr,$value["synonym"]);
	}
	echo json_encode($arr);
}

function entityUpdate(){
	global $url,$g1meisho,$g2meisho,$workspace_id_shi,$sword,$data;
	$url = "https://gateway.watsonplatform.net/conversation/api/v1/workspaces/".$workspace_id_shi."/entities/".$g1meisho."/values/".urlencode($g2meisho)."/synonyms?version=2017-05-26";
	$data = array("synonym" => $sword);
	$jsonString = callWatson();
	$json = json_decode($jsonString, true);
	if($json["synonym"] == $sword){
		echo json_encode("OK");
	}else{
		echo json_encode("NG");
	}
}

function entityDelete(){
	global $url,$g1meisho,$g2meisho,$workspace_id_shi,$sword,$data;
	$url = "https://gateway.watsonplatform.net/conversation/api/v1/workspaces/".$workspace_id_shi."/entities/".$g1meisho."/values/".urlencode($g2meisho)."/synonyms/".urlencode($sword)."?version=2017-05-26";
	$result = callWatson3();
	error_log($result);
	if($result == "200"){
		echo json_encode("OK");
	}else{
		echo json_encode("NG");
	}
}

function callWatson(){
	global $curl, $url, $username, $password, $data, $options;
	$curl = curl_init($url);

	$options = array(
			CURLOPT_HTTPHEADER => array(
					'Content-Type: application/json',
			),
			CURLOPT_USERPWD => $username . ':' . $password,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => json_encode($data),
			CURLOPT_RETURNTRANSFER => true,
	);

	curl_setopt_array($curl, $options);
	return curl_exec($curl);
}

function callWatson2(){
	global $curl, $url, $username, $password, $data, $options;
	$curl = curl_init($url);

	$options = array(
			CURLOPT_HTTPHEADER => array(
					'Content-Type: application/json',
			),
			CURLOPT_USERPWD => $username . ':' . $password,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_RETURNTRANSFER => true,
	);

	curl_setopt_array($curl, $options);
	return curl_exec($curl);
}

function callWatson3(){
	global $curl, $url, $username, $password, $data, $options;
	$curl = curl_init($url);

	$options = array(
			CURLOPT_HTTPHEADER => array(
					'Content-Type: application/json',
			),
			CURLOPT_USERPWD => $username . ':' . $password,
			CURLOPT_CUSTOMREQUEST => 'DELETE',
			CURLOPT_RETURNTRANSFER => true,
	);

	curl_setopt_array($curl, $options);
	curl_exec($curl);
	return curl_getinfo($curl, CURLINFO_HTTP_CODE);
}
?>

