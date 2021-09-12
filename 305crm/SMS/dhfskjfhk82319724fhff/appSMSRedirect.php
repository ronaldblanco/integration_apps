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

require_once (__DIR__.'/crest/crest.php');

//Only for TESTs!
//$myfile = fopen("log.txt", "a") or die("Unable to open file!");
//echo '**************************\n';
//echo date("Y.m.d G:i:s")."\n";
//var_dump($_GET);
//var_dump($_POST); //from bitrix24

$timeline = ( CRest :: call (
    'crm.timeline.comment.add' ,
   	[
		'fields' =>
           [
               "ENTITY_ID" => $_GET['contactID'],
               "ENTITY_TYPE" => "contact",
               "COMMENT" => "A SMS with text '" . $_GET['message'] . "' was send to this contact from the Coordinator!",
           ]
   	])
	);
//var_dump($timeline);

	$setmessage = ( CRest :: call (
    	'im.notify' ,
   		[
			"to" => $_GET['coordinatorID'],
         	"message" => "You send a SMS with text '" . $_GET['message'] . "' to contat ID ".$_GET['contactID']."!",
         	"type" => 'SYSTEM',
   		])
	);
//var_dump($setmessage);
//Only for tests!
//fwrite($myfile, file_put_contents("log.txt", ob_get_flush()));
//fclose($myfile);

if(isset($timeline['error']) && isset($setmessage['error'])){
	echo 'ERROR FOUND: '. $timeline['error']." - > ".$timeline['error_description'];
	echo 'ERROR FOUND: '. $setmessage['error']." - > ".$setmessage['error_description'];
} else {
	sleep(2);
	header('Location: https://'.$env['integrationdomain'].'/305crm/SMS/dhfskjfhk82319724fhff/indexSMS.php');
}

?>