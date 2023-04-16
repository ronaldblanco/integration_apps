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

$timeline = ( CRest :: call (
    'crm.timeline.comment.add' ,
   	[
		'fields' =>
           [
               "ENTITY_ID" => $_GET['fields']['ENTITY_ID'],
               "ENTITY_TYPE" => "contact",
               "COMMENT" => $_GET['fields']['COMMENT'],
           ]
   	])
);
//var_dump($timeline);

if(isset($timeline['error']) && isset($timeline['error_description'])){
	echo 'ERROR FOUND: '. $timeline['error']." - > ".$timeline['error_description'];
} else {
	sleep(2);
	header('Location: https://'.$env['bitrix_domain'].'/marketplace/app/15/');
}

?>