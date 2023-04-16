<?php

//Get ENV
	$env = file_get_contents('/var/www/vhosts/integration.swaypc.com/.env', true);
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

$day = date("Y-m-d");
//if(isset($_POST['day'])) $day = $_POST['day'];
//if(isset($_GET['day'])) $day = $_GET['day'];

$alert = 0;
$message = "";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  	//die("Connection failed: " . $conn->connect_error);
	$alert = 1;
	$message = "Connection failed: " . $conn->connect_error;
} else {
	$message = "Connected successfully to Database!";
}


//Execute task

$sql = "SELECT count(id) as count,from_num,domain,max(date_time) as date FROM `execute_on_limit` where date_time LIKE '".$day."%' and CHAR_LENGTH(execute_on_limit.from_num) > 6 and from_num not LIKE 'line_%' group by execute_on_limit.from_num order by count desc";
$result = $conn->query($sql);
$daily_report = array();
if ($result == TRUE) {
  //echo "Successfully";
  
	if ($result->num_rows > 0) {
		file_get_contents($env['sendemailhandler'].'?sendto='.$env['destinyemail'].'&message='.$result->num_rows);
  	
		echo "limit calls number is ".$result->num_rows."! an email was send!";
	} else {
		
		echo "limit calls number is "."0"."! no email send!";
	}
	
} else {
	$alert = 1;
  	$message = "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();

?>