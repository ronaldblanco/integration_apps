<?php

//Get ENV
	$env = file_get_contents('../../.env', true);
	$env = explode("\n",$env);
	$getEnv = [];
	foreach($env as $data){
		$data = explode("=",$data);
		$getEnv[$data[0]] = $data[1];
	}
	$env = $getEnv;
	unset($getEnv);

if($_SERVER['REMOTE_ADDR'] == $env['merchantip']){

	require_once (__DIR__.'/crest/crest.php');

	$upload = ( CRest :: call (
    	'disk.folder.uploadfile' ,
   		[
			'id' => 408174,
			'data' =>
           	[
               "NAME" => $_GET['NAME'],
           	],
			'fileContent' => base64_encode(file_get_contents("https://".$_GET['LINK']))
   		])
	);

	echo json_encode($upload);
	
} else echo "Denied Access!";

?>