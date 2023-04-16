<?php

//Get ENV
	$env = file_get_contents('../../../.env', true);
	$env = explode("\n",$env);
	$getEnv = [];
	foreach($env as $data){
		$data = explode("=",$data);
		$getEnv[$data[0]] = $data[1];
	}
	$env = $getEnv;
	unset($getEnv);

if($_GET['KEY'] == $env['bitrixkey'] && $_SERVER['REMOTE_ADDR'] == $env['merchantip']){
	
	//var_dump($_GET);
//require_once (__DIR__.'/crest/crest.php');
	
$_GET['NAME'] = str_replace(" ", "%20", $_GET['NAME']);
$_GET['LAST_NAME'] = str_replace(" ", "%20", $_GET['LAST_NAME']);
	
$url = $env['merchanturl']."?firstname=".$_GET['NAME']."&lastname=".$_GET['LAST_NAME']."&phonenumber=".$_GET['PHONE']."&email=".$_GET['EMAIL'];
$accessToken = $env['merchantaccesstoken'];

$curl = curl_init($url);
//error_log(var_export($curl));

curl_setopt($curl, CURLOPT_HTTPHEADER, array(
    'authtoken: ' . $accessToken,
	'Content-Type: application/json'
));

$response = curl_exec($curl);
//$result = json_decode($response,true);
	
if($response === false) $result = "Destination server did not responded correctly! curl error:".curl_error($curl);
else $result = "Destination server responded correctly!";

curl_close($curl);

//log information:
$content = date("F j, Y, g:i a")."||".$url."||".$result."||".$_SERVER['REMOTE_ADDR']."\n";
//$content["all_inbound_info"] = $all_info;
file_put_contents("log_add_customer_to_merchant.txt", print_r($content, true), FILE_APPEND);

} else echo "Access Denied!";

?>