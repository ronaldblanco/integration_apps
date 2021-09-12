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

function redirect($url)
{
    //Header("HTTP 302 Found");
	//if (isset($_REQUEST['code'])) $url = APP_REG_URL;
    Header("Location: ".$url);
    die();
}

require_once (__DIR__.'/crest/crest.php');

//Functions#######################
//#################################
//Add user and the contacts count
function setcount($arr, $x) {
	global $myresult;
	$count = 0;
    for($i = 0; $i < sizeof($arr); $i++) {
        if($arr[$i]["ASSIGNED_BY_ID"] == $x) $count = $count + 1;
    }
	if($count > 0){
		array_push($myresult, ['user' => $x,'count' => $count]);
	} 
    return 1;
}

//Search user ID in final arr
function search($arr, $x) {
	global $myresult;
    for($i = 0; $i < sizeof($arr); $i++) {
		//var_dump($arr[$i]["user"]);
		//var_dump($x);
        if($arr[$i]["user"] == $x) return $i;
    }
    return false;
}

//Search user ID in arr of out users
function searchout($arr, $x) {
	//global $myresult;
    for($i = 0; $i < sizeof($arr); $i++) {
		//var_dump($arr[$i]["user"]);
		//var_dump($x);
        if($arr[$i]["ID"] == $x) return $i;
    }
    return false;
}

//Select users with less count of contacts
function select($arr) {
	global $selectuser;
	$count = 100;
    for($i = 0; $i < sizeof($arr); $i++) {
		//var_dump($arr[$i]["user"]);
		//var_dump($x);
        if($arr[$i]["count"] < $count){
			$count = $arr[$i]["count"];
			$selectuser = $arr[$i]["user"];
		}
    }
    return $selectuser;
}
//#################################


$step =2; //default 1

if (isset($_REQUEST['config'])) $step = 0;
if (isset($_REQUEST['portal'])) $step = 1;
if (isset($_REQUEST['code']))$step = 2;
if (isset($_GET['file'])) {
	$file = $_GET['file'];
} else $file = "";

//if(file_exists(__DIR__ . '/config.json')){
if(isset($env['app_id'])){
	//$config = json_decode(file_get_contents(__DIR__ . '/config.json'),true);
/*Vars***********************************/
define('APP_ID', $env['app_id']); // take it from Bitrix24 after adding a new application
define('APP_SECRET_CODE', $env['app_secret']); // take it from Bitrix24 after adding a new application
define('APP_REG_URL', $env['app_redirect_url']); // the same URL you should set when adding a new application in Bitrix24
$domain = $env['bitrix_domain'];
//$userID = $config['user_id'];
$server_domain = $domain;
$savetime = 20; //seconds to be sure that access_token it is valid
/*End Vars*******************************/
} else {
	$step = 0;
}
//$domain = isset($_REQUEST['portal']) ? $_REQUEST['portal'] : ( isset($_REQUEST['domain']) ? $_REQUEST['domain'] : 'empty');

$btokenRefreshed = null;

$arScope = array('user');

switch ($step) {
    case 1:
       
			//requestCode($domain);
		    
        break;

    case 2:
      
			//$arAccessParams = requestAccessToken($_REQUEST['code'], $_REQUEST['server_domain']);
					
		/*Execute Rest APIS
		**
		**
		*/
		
        break;
    default:
        break;
}

/*Execute Rest APIS
		**
		**
		*/
//$arCurrentB24User = executeREST($arAccessParams['client_endpoint'], 'user.current', array(),$arAccessParams['access_token']);

$contacts = ( CRest :: call (
    'crm.contact.list' ,
   		[
 	 	 //'FILTER' => ['UF_CRM_1594061489' => $arCurrentB24User["result"]["ID"]], //Coordinator
		 'FILTER' => ['UF_CRM_1594061489' => '22'], //Coordinator
	 	 'SELECT' => ['ID','NAME','LAST_NAME','PHONE','EMAIL','ASSIGNED_BY_ID','UF_CRM_1594061489'], //UF_CRM_1594061489 -> coordinator, UF_CRM_1594061559 -> 3rd responsable
	 	 //'EMAIL'=> [['VALUE' => 'lola@yea.com', 'VALUE_TYPE' => 'WORK']] ,
	 	 //'PHONE'=> [['VALUE' => '123458', 'VALUE_TYPE' => 'WORK']] ,
    	])
);


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quick start. Local server-side application in Bitrix24</title>
	<link rel="stylesheet" href="css/timeline.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
		
	<style>
  		h2 {color:blue;}
  		p {color:green;}
	</style>
</head>
<body>
<?php
	if ($step == 0) {
?>
	<div class="container-fluid">
	<div class="alert alert-primary" role="alert">
		<h2>It is posible your aplication it is not configure to work whit Bitrix24 yet:</h2>
	</div>
    <form action="config.php" method="post" styles>
		<div class="form-group">
			<label>APP_ID:</label>
        	<input type="text" class="form-control" name="app_id" placeholder="APP_ID" value='<?php echo $config['app_id']; ?>'>
			<small id="ID" class="form-text text-muted">ID de la aplicacion en Bitrix24.</small>
		</div>
		<div class="form-group">
			<label>APP_SECRET:</label><br>
			<input type="text" class="form-control" name="app_secret" placeholder="APP_SECRET" value='<?php echo $config['app_secret']; ?>'>
			<small id="SECRET" class="form-text text-muted">Secret de la aplicacion en Bitrix24.</small>
		</div>
		<div class="form-group">
			<label>APP_REDIRECT_URL:</label><br>
			<input type="text" class="form-control" name="app_redirect_url" placeholder="APP_REDIRECT_URL" value='<?php echo $config['app_redirect_url']; ?>'>
			<small id="URL" class="form-text text-muted">URL de redireccion en Bitrix24.</small>
		</div>
		<div class="form-group">
			<label>BITRIX_DOMAIN.COM:</label><br>
			<input type="text" class="form-control" name="bitrix_domain" placeholder="BITRIX_DOMAIN.COM" value='<?php echo $config['bitrix_domain']; ?>'>
			<small id="DOMAIN" class="form-text text-muted">Domain of Bitrix24 server.</small>
		</div>
        <input type="submit" class="btn btn-primary" value="Submit">
    </form>
	</div>
<?php
} elseif ($step == 1) {
	echo '<div class="alert alert-primary" role="alert">';
	echo 'step 1 (redirecting to Bitrix24):<br/>';
	echo '</div>';
} elseif ($step == 2){
	echo '<div class="alert alert-primary" role="alert">';
	//echo "Logged User: " . $arCurrentB24User["result"]["NAME"] . " " . $arCurrentB24User["result"]["LAST_NAME"] . ' <br/>';
	echo '</div>';
	echo '<div class="alert alert-success" role="alert">';
	echo 'Bellow you will find your contacts for coordination:<br/>';
	//print_r($contacts['result']);
	echo '</div>';
	//var_dump($arCurrentB24User["result"]);
	$timelines = array();
?>

<table class="table">
	<tr>
	  <td>ID</td>
	  <td>NAME</td>
	  <td>LAST NAME</td>
	  <td>PHONE</td>
	  <td>EMAIL</td>
	  <td>RESPONSABLE</td>
    </tr>	
	<?php foreach ($contacts['result'] as $row): array_map('htmlentities', $row);
		
	$responsable = ( CRest :: call (
    'user.get' ,
   		[
 	 	 'FILTER' => ['ID' => $row['ASSIGNED_BY_ID']],
	 	 'SELECT' => ['ID','NAME','LAST_NAME'], //UF_CRM_1594061489 -> coordinator, UF_CRM_1594061559 -> 3rd responsable
    	])
	);
		
	$timeline = ( CRest :: call (
    'crm.timeline.comment.list' ,
   		[
 	 	 'FILTER' => ['ENTITY_ID' => $row['ID'], "ENTITY_TYPE" => "contact"],
	 	 //'SELECT' => ['COMMENT','EMAIL','TASK','SMS','CALL','FILES','CREATION','ID'], //UF_CRM_1594061489 -> coordinator, UF_CRM_1594061559 -> 3rd responsable
    	])
	);	
		
	array_push($timelines, array('contactid' => $row['ID'], 'contactname' => $row['NAME']. " " . $row['LAST_NAME'] ,'IDresponsable' => $row['UF_CRM_1594061489'],'contactphone' => $row['PHONE'][0]['VALUE'], $timeline['result']));
		//var_dump($timeline);
	?>
    <tr>
	  <td><a href="https://<?php echo $env['bitrix_domain'];?>/crm/contact/details/<?php echo $row['ID']?>/"><?php echo $row['ID']?></a></td>
      <td><?php echo $row['NAME']; ?></td>
	  <td><?php echo $row['LAST_NAME'];?></td>
	  <td><?php echo "<a href='callto:" . $row['PHONE'][0]['VALUE'] . "'>" . $row['PHONE'][0]['VALUE'] . "</a>";?></td>
	  <td><?php echo "<a href='mailto:" . $row['EMAIL'][0]['VALUE'] . "'>" . $row['EMAIL'][0]['VALUE'] . "</a>";?></td>
	  <td><?php echo $responsable['result'][0]['NAME'] . ' ' . $responsable['result'][0]['LAST_NAME'];?></td>
    </tr>
	<?php endforeach; ?>
</table>
	


<ul class="nav nav-tabs" id="myTab" role="tablist">
	<?php $active = false; ?>
	<?php foreach ($timelines as $row): array_map('htmlentities', $row);
		if($active == false){
			$class = "nav-link active";
			$arial = "true";
			$active = true;
		} else {
			$class = "nav-link";
			$arial = "false";
		} 
	?>
	
  <li class="nav-item">
    <a class="<?php echo $class;?>" id="<?php echo $row['contactid'];?>-tab" data-toggle="tab" href="#<?php echo $row['contactid'];?>" role="tab" aria-controls="<?php echo $row['contactid'];?>"
      aria-selected="<?php echo $arial;?>"><?php echo $row['contactid'].":".$row['contactname'];?></a>
  </li>
	<?php endforeach; ?>
  
</ul>
	
<div class="tab-content" id="myTabContent">
	
	<?php $active = false; ?>
	<?php foreach ($timelines as $row): array_map('htmlentities', $row);
		if($active == false){
			$class = "tab-pane fade show active";
			$active = true;
		} else $class = "tab-pane fade";
	?>
	
  <div class="<?php echo $class;?>" id="<?php echo $row['contactid'];?>" role="tabpanel" aria-labelledby="<?php echo $row['contactid'];?>-tab">
	   
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
					
					<?php //foreach ($timelines as $row): array_map('htmlentities', $row);
						date_default_timezone_set('America/New_York');
/*$ch = curl_init();
 
//Set the URL that you want to GET by using the CURLOPT_URL option.
curl_setopt($ch, CURLOPT_URL, 'http://google.com');
 
//Set CURLOPT_RETURNTRANSFER so that the content is returned as a variable.
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
//Set CURLOPT_FOLLOWLOCATION to true to follow redirects.
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
 
//Execute the request.
$data = curl_exec($ch);
 
//Close the cURL handle.
curl_close($ch);
 
//Print the data out onto the page.
echo $data;*/
		
					?>
					
				<ul class="nav nav-tabs" id="myTab" role="tablist">	
					
					
					<li class="nav-item">
    <a class="nav-link" id="SMS-tab" data-toggle="tab" href="#SMS" role="tab" aria-controls="SMS"
      aria-selected="false">SMS</a>
  </li>
					
					
					</br>
					</br>
					
					
					<div class="tab-content" id="myTabContent">
						
				
				<div class="tab-pane fade show active" id="SMS" role="tabpanel" aria-labelledby="SMS-tab"></br></br>
					<form action="upload.php" method="post" enctype="multipart/form-data">
						<?php echo $file;?><br/>
  						Select image to upload:
  						<input type="file" name="fileToUpload" id="fileToUpload">
  						<input type="submit" value="Upload Image" name="submit">
					</form>
					<form action="<?php echo $env['smshandler'];?>" method="post" styles>
						<div class="form-group">
							<label>SMS:</label>
							<input type="" class="form-control" name="message_to" value='<?php echo $row['contactphone'];?>'>
							<input type="hidden" class="form-control" name="bindings[0][OWNER_ID]" value='<?php echo $row['IDresponsable'];?>'>
							<input type="hidden" class="form-control" name="auth[domain]" value='<?php echo $env['bitrix_domain'];?>'>
							<input type="hidden" class="form-control" name="auth[member_id]" value='<?php echo $env['member_id'];?>'>
							<input type="hidden" class="form-control" name="auth[application_token]" value='<?php echo $env['application_token'];?>'>
							<input type="hidden" class="form-control" name="redirect" value='<?php echo $env['integrationrediredurl'];?>appSMSRedirect.php?contactID=<?php echo $row['contactid'];?>&coordinatorID=<?php echo $row['IDresponsable'];?>'>
							<textarea class="form-control" name="message_body" placeholder="SMS text" value='' rows="5" style="width: 100%"><?php if($file != "") echo "Attachemnt -> https://".$env['integrationdomain']."/305crm/SMS/dhfskjfhk82319724fhff/".$file?></textarea>
							<small id="SMS" class="form-text text-muted">SMS Text to send!</small>
						</div>
        				<input type="submit" class="btn btn-primary" value="Send">
    				</form></br>
				</div>
				
				
				
						
					</div>
					
				
				</ul>	
					
                    <h6 class="card-title">Time Line for: <?php echo $row['contactname'] . ", ID: " . $row['contactid'];?></h6>
                    <div id="content">
                        <ul class="timeline">
							
							<?php 
								$reverse = array_reverse($row[0]);
								foreach ($reverse as $subrow): array_map('htmlentities', $subrow);
							
							?>
							
                            <li class="event" data-date="<?php echo date('M/d/Y H:m:s', strtotime($subrow["CREATED"]) - 60 * 60 * 7);?>">
                                <h3>ID:<?php echo $subrow["ID"];?></h3>
                                <p><?php echo $subrow["COMMENT"];?></p>
                            </li>
                          
						<?php endforeach; ?>
						
						</ul>
                    </div>
                
					<?php //endforeach; ?>
				
				</div>
            </div>
        </div>
    </div>
</div>
	  
	  	
	</div>
	
	<?php endforeach; ?>
 
</div>	
	
<?php
	//var_dump($timelines);	
}
?>
	
	<!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
	
</body>
</html>

<?php

function executeHTTPRequest ($queryUrl, array $params = array()) {
    $result = array();
    $queryData = http_build_query($params);

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_POST => 1,
        CURLOPT_HEADER => 0,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $queryUrl,
        CURLOPT_POSTFIELDS => $queryData,
    ));

    $curlResult = curl_exec($curl);
    curl_close($curl);

    if ($curlResult != '') $result = json_decode($curlResult, true);

    return $result;
}

function requestCode ($domain) {
    $url = 'https://' . $domain . '/oauth/authorize/' .
        '?client_id=' . urlencode(APP_ID);
    redirect($url);
}

function requestAccessToken ($code, $server_domain) {
    $url = 'https://' . $server_domain . '/oauth/token/?' .
        'grant_type=authorization_code'.
        '&client_id='.urlencode(APP_ID).
        '&client_secret='.urlencode(APP_SECRET_CODE).
        '&code='.urlencode($code);
    return executeHTTPRequest($url);
}

function executeREST ($rest_url, $method, $params, $access_token) {
    $url = $rest_url.$method.'.json';
    return executeHTTPRequest($url, array_merge($params, array("auth" => $access_token)));
}

?>