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

$task = ( CRest :: call (
    'tasks.task.add' ,
   	[
		'fields' =>
           [
               "TITLE" => $_GET['fields']['TITLE'],
               "RESPONSIBLE_ID" => $_GET['fields']['RESPONSIBLE_ID'],
           ]
   	])
);
//var_dump($timeline);

if(isset($task['error']) && isset($task['error_description'])){
	echo 'ERROR FOUND: '. $task['error']." - > ".$task['error_description'];
} else {
	sleep(2);
	header('Location: '.$env['redirectlocation']);
}

?>