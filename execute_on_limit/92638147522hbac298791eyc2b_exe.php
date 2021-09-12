<?php

//Only for TESTs!
$myfile = fopen("log.txt", "a") or die("Unable to open file!");
//echo '**************************\n';
var_dump($_GET);

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

$servername = $env['servername'];
$username = $env['username'];
$password = $env['password'];
$dbname = $env['dbname'];

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  	die("Connection failed: " . $conn->connect_error);
} else {
	echo "Connected successfully";
}

/*
Execute task
*/
$sql = "INSERT INTO execute_on_limit (from_num, to_num, domain)
VALUES ('".$_GET['from']."', '".$_GET['to']."', '".$_GET['domain']."')";

if ($conn->query($sql) === TRUE) {
  echo "New record created successfully";
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();

/*
array(3) {
  ["from"]=>
  string(11) "13056001217"
  ["to"]=>
  string(11) "17863342521"
  ["data"]=>
  string(215) "{attachments:[{content-size:359019,content-type:image/jpeg,content-url:https://mmmsg.acrobits.net/9qmwxqmrrztoyu1f0rmbvka2d5x65hn0c,encryption-key:56401F490463F6B264EEB14029419F55,hash:2539213394}],body:Uuuuuuuuuuu}"
}
*/
//var_dump($_POST);

//Reduce picture function
/*function picture($ext) {
    if($ext == 'image/jpeg' || $ext == 'image/gif' || $ext == 'image/png' || $ext == 'image/jpg'){
		//$output = shell_exec("convert ".$source." -resize 1024x768\> ".$source);
    	return true;
	} else {
		//echo "No valid extension to compress!";
		return false;
	}
}*/

//$d = compress($source_img, $destination_img, 90);

/*$sender = isset($_GET['from']) ? trim(str_replace("+","",base64_decode($_GET["from"]))) : '';
$recipient = isset($_GET['to']) ? trim(str_replace("+","",base64_decode($_GET["to"]))) : '';
$message = isset($_GET['body']) ? trim(str_replace("___"," ",base64_decode($_GET['body']))) : '';
$data = isset($_GET['data']) ? trim(base64_decode($_GET['data'])) : '';
$domain = isset($_GET['domain']) ? trim(base64_decode($_GET['domain'])) : '';*/
//$data = isset($_GET['data']) ? trim(str_replace(":https:",":'https:",$_GET['data'])) : '';
/*$data = str_replace(",encryption-key:","',encryption-key:'",$data);
$data = str_replace(",hash:","',hash:",$data);
$data = str_replace("content-type:","content-type:'",$data);
$data = str_replace(",content-url:","',content-url:",$data);*/
/*$data = str_replace("https://","",$data);
$datapieces = explode(",", $data);
$datapieces[0] = str_replace("{attachments:[{content-size:","content-size:",$datapieces[0]);
str_replace("body:","",str_replace("}","",str_replace("___"," ",$datapieces[count($datapieces) - 1])));
//var_dump($datapieces[count($datapieces) - 1]);
//var_dump(str_replace("body:","",str_replace("}","",$datapieces[count($datapieces) - 1])));
var_dump($message);*/

/*$data = array();
$count = 0;
for($i = 0;$i<count($datapieces) - 1;$i++){
	$datatemp = explode(":", $datapieces[$i]);
	$datatemp[0] = str_replace("{","",$datatemp[0]);
	if(isset($data[$count][$datatemp[0]])) $count = $count + 1;
	$data[$count][$datatemp[0]] = str_replace("}","",str_replace("}]","",$datatemp[1]));
}*/

//var_dump($data);

/*$json[] = "{'attachments':[{'content-size':359019,'content-type':'image/jpeg','content-url':'https://mmmsg.acrobits.net/9qmwxqmrrztoyu1f0rmbvka2d5x65hn0c','encryption-key':'56401F490463F6B264EEB14029419F55','hash':2539213394}],'body':'Uuuuuuuuuuu'}";
foreach ($json as $string) {
    echo 'Decoding: ' . $string;
    json_decode($string);

    switch (json_last_error()) {
        case JSON_ERROR_NONE:
            echo ' - No errors';
        break;
        case JSON_ERROR_DEPTH:
            echo ' - Maximum stack depth exceeded';
        break;
        case JSON_ERROR_STATE_MISMATCH:
            echo ' - Underflow or the modes mismatch';
        break;
        case JSON_ERROR_CTRL_CHAR:
            echo ' - Unexpected control character found';
        break;
        case JSON_ERROR_SYNTAX:
            echo ' - Syntax error, malformed JSON';
        break;
        case JSON_ERROR_UTF8:
            echo ' - Malformed UTF-8 characters, possibly incorrectly encoded';
        break;
        default:
            echo ' - Unknown error';
        break;
    }

    echo PHP_EOL;
}*/

//$data = json_decode($data,true);

//var_dump($data);

//$data['attachments'][0];
//$data['attachments'][0]['content-type'];
//$data['attachments'][0]['content-url'];
//$data['attachments'][0]['encryption-key'];
//$data['attachments'][0]['hash'];

/*$SMSuser = 'api_bitrix24_crm1';
$SMSpass = 'Zxcqwe123';
$iv = '00000000000000000000000000000000';
$encfile = "";
$decfile ="";
$i = 0;
$files = array();

$total = 0;
$img = false;
for($j = 0; $j < count($data); $j++){
	$total = $total + intval($data[$j]['content-size']);
	if (picture($data[$i]['content-type']) == true) $img = true;
	//var_dump(intval($data[$j]['content-size']));
}*/
//var_dump($total);
/*if($total < 2048000 || ($total > 2048000 && $img == true)) {

for($i = 0; $i < count($data); $i++){
	if((intval($data[$i]['content-size']) > 2048000 && picture($data[$i]['content-type']) == true) || (intval($data[$i]['content-size']) < 2048000 && picture($data[$i]['content-type']) == false) || (intval($data[$i]['content-size']) < 2048000)){
	
		echo "For for ".$i." until ".count($data);
		$url = "https://".$data[$i]['content-url'];
		$key = $data[$i]['encryption-key'];
		$ext = explode('/', $data[$i]['content-type']);
		$encfile = "encfile".$data[$i]['hash'].".".$ext[1];
		$decfile = "decfile".$data[$i]['hash'].".".$ext[1];
	
		//var_dump($encfile);
		//var_dump($decfile);
	
		$myencfile = fopen("temp/".$encfile, "a") or die("Unable to open file!");
		fwrite($myencfile, file_put_contents("temp/".$encfile, file_get_contents($url)));
		//echo "<br/>openssl enc -aes-128-ctr -d -K ".$key." -iv ".$iv." -nopad -in temp/".$encfile." -out temp/".$decfile;
		$output = shell_exec("openssl enc -aes-128-ctr -d -K ".$key." -iv ".$iv." -nopad -in temp/".$encfile." -out temp/".$decfile);
		if(intval($data[$i]['content-size']) > 512000){
			//$compress_result = mycompress("temp/".$decfile, $ext);
			if($ext[1] == 'jpeg' || $ext[1] == 'gif' || $ext[1] == 'png' || $ext[1] == 'jpg'){
				$outputconvert = shell_exec("convert "."temp/".$decfile." -resize 1024x768 "."temp/".$decfile);
				echo $outputconvert;
    			//return $output;
			} else {
				echo "No valid extension to compress!";
				//return false;
			}
			//if (!unlink("temp/".$decfile)) echo ("$decfile cannot be deleted due to an error");
			//$decfile = "com_" . $decfile;
		}
		//if(!isset($output)) echo ;
		//sleep for 3 seconds
		//sleep(5);
		$files[$i] = array("FileName"=>$decfile,"FileContent"=>file_get_contents("temp/".$decfile));
		//array_push($files,"MMSFile"=>["FileName"=>$decfile,"FileContent"=>base64_decode(file_get_contents("temp/".$decfile))]);
		// Use unlink() function to delete a file  
		if (!unlink("temp/".$encfile)) echo ("$encfile cannot be deleted due to an error");  
		if (!unlink("temp/".$decfile)) echo ("$decfile cannot be deleted due to an error");  
		
	} else {
		echo "File it is to big to be send; action skiped; only less of 2 mb allowed!";
		$files[$i] = array("FileName"=>"Attachemnt_error.png","FileContent"=>file_get_contents("lib/pictures/error.png"));
	}
}

} else {
	echo "The attachemnts are bigger than the allowed size!; Operation Canceled!";
	$files[0] = array("FileName"=>"Attachemnt_error.png","FileContent"=>file_get_contents("lib/pictures/error.png"));
	$message = "The attachemnts are bigger than the allowed size!; Operation Canceled!; Maximun it is 2 mb! -> " . $message;
}*/

//var_dump($files);
//$url = "https://mmmsg.acrobits.net/tdc1bgevz2j6efpm7x7nf1u9kkb6g721q";
//$key = "BD6F03693EEB05479E1327012E40AFEC";
//$message = "test";

/*try{
$soapclient = new SoapClient('https://backoffice.voipinnovations.com/Services/APIService.asmx?wsdl');
$param=array('login'=>$SMSuser,'secret'=>$SMSpass,'sender'=>$sender,'recipient'=>$recipient,'message'=>$message, 'files'=>$files);
	
	$response =$soapclient->SendMMS($param);
	$result = json_encode($response);
	echo $result;
	$smsresult = $response->SendMMSResult->responseMessage;
	if($smsresult != 'Success' && $smsresult != 'Invalid sender' && $smsresult != 'Invalid TN. Sender and recipient must be valid phone numbers and include country code.') $smsresult = 'MMS Error';
	
	$comment = "The MMS Service for recipient " . $recipient . " from " . $sender . " responded: " . $smsresult . "!";
	
	if($domain == "210586dd-68ab-41e6-9a81-6146eae740d2"){ //305 PBX domain ID only
		$comment = str_replace(" ","___",$comment);
	$notify_bitrix = file_get_contents("https://integration.swaypc.com/groundwire/SMS/3287gdeyhbjhBitrix305_notification_sendSMS.php?from=" . $sender . "&to=" . $recipient . "&key=".$domain."&text=".$comment); //Key equibalen to 305 domain on the pbx
	}
			
}catch(Exception $e){
	echo $e->getMessage();
}*/

fwrite($myfile, file_put_contents("log.txt", ob_get_flush()));
fclose($myfile);

/**/

?>