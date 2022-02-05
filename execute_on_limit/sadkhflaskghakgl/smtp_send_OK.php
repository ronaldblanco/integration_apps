<?php

echo '12345';

//Get ENV
	$env = file_get_contents('../../../.env', true);
	$env = explode("\n",$env);
	$getEnv = [];
	foreach($env as $data){
		$data = explode("=",$data);
		$getEnv[$data[0]] = $data[1];
		if(count($data) > 2) $getEnv[$data[0]] = $getEnv[$data[0]] . '='. $data[2];
	}
	$env = $getEnv;
	unset($getEnv);
//return $env;
//var_dump($env);
//namespace PortoContactForm;

session_cache_limiter('nocache');
header('Expires: ' . gmdate('r', 0));

header('Content-type: application/json');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'php-mailer/src/PHPMailer.php';
require 'php-mailer/src/SMTP.php';
require 'php-mailer/src/Exception.php';

// Step 1 - Enter your email address below. Send to!
$email = (isset($_GET['sendto'])) ? $_GET['sendto'] : $env['sendtoemail'];
// Step 1 - Enter your email address below. Send from!
$emailfrom = $env['emailfrom'];
$namefrom = $env['namefrom'];

// If the e-mail is not working, change the debug option to 2 | $debug = 2;
$debug = 2;

// If contact form don't has the subject input change the value of subject here
$subject = ( isset($_GET['subject']) ) ? $_GET['subject'] : 'Integration of execute on limit notification!';

$day = date("Y-m-d");
$getmessage = ( isset($_GET['message']) ) ? $_GET['message'] : 'Integration of execute on limit notification messages!';
$message = 'We have '.$getmessage.' calls limited for today! You can access here: '.$env['reportdetailsurl'].'?day='.$day.' or '.$env['reportalldata'].'?day='.$day.'. For domain summary by day: '.$env['reporturl'].'. For domain summary by month: '.$env['reporturlmonthly'].'.';

$mail = new PHPMailer(true);

try {

	$mail->SMTPDebug = $debug;                                 // Debug Mode

	// Step 2 (Optional) - If you don't receive the email, try to configure the parameters below:

	$mail->IsSMTP();                                         // Set mailer to use SMTP
	$mail->Host = 'smtp.gmail.com';				       // Specify main and backup server
	$mail->SMTPAuth = true;                                  // Enable SMTP authentication
	$mail->Username = $env['mailUsername'];                    // SMTP username
	$mail->Password = $env['mailPassword'];                              // SMTP password
	$mail->SMTPSecure = 'tls';                               // Enable encryption, 'ssl' also accepted
	$mail->Port = 587;   							       // TCP port to connect to

	$mail->AddAddress($email);	 						       // Add another recipient

	//$mail->AddAddress('person2@domain.com', 'Person 2');     // Add a secondary recipient
	//$mail->AddCC('person3@domain.com', 'Person 3');          // Add a "Cc" address. 
	//$mail->AddBCC('person4@domain.com', 'Person 4');         // Add a "Bcc" address. 

	// From - Name
	//$fromName = ( isset($_POST['name']) ) ? $_POST['name'] : 'Website User Ronald';
	$mail->SetFrom($emailfrom, $namefrom);

	// Repply To
	$mail->AddReplyTo($emailfrom, $namefrom);
	
	$mail->IsHTML(true);                                       // Set email format to HTML

	$mail->CharSet = 'UTF-8';

	$mail->Subject = $subject;
	$mail->Body    = $message;
	var_dump($message);
	$mail->Send();
	$arrResult = array ('response'=>'success');

} catch (Exception $e) {
	$arrResult = array ('response'=>'error','errorMessage'=>$e->errorMessage());
} catch (\Exception $e) {
	$arrResult = array ('response'=>'error','errorMessage'=>$e->getMessage());
}

if ($debug == 0) {
	echo json_encode($arrResult);
}

