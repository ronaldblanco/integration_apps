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

//////////////////////////////////////////////////////////////
$servername = $env['servername'];
$username = $env['username'];
$password = $env['password'];
$dbname = $env['dbname'];

$domains = json_decode(file_get_contents('https://' . $env['pbx_domains_url_info'] . "?key=" . $env['pbx_domains_url_info_key']), true);
$domainRows = $domains['data']['domains'];

function getDomainDescription($domainPara, $domainRows)
{
    //var_dump($domainRows);
    $result = '';
    foreach ($domainRows as $row) {
        //var_dump($row['domain_description']);
        if ($domainPara == $row['domain_name']) {
            $result = $row['domain_description'];
            //var_dump($domain['domain_description']);
        }
    }

    return $result;
}

$day = date("Y-m-d");
$month = date("Y-m");

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
//////////////////////////////////////////////////////////////

//Daily report///////////////////////////////////////////////
/*Execute task*/
$sql = "SELECT count(id) as count,domain,max(date_time) as date FROM `execute_on_limit` where date_time LIKE '" . $day . "%' and CHAR_LENGTH(execute_on_limit.from_num) > 6 and from_num not LIKE 'line_%' group by domain order by count desc";
$result = $conn->query($sql);
$daily_report = array();
if ($result == TRUE) {
    //echo "Successfully";

    if ($result->num_rows > 0) {
        // output data of each row
        while ($row = $result->fetch_assoc()) {
            //echo "Count: " . $row["count"] . " Domain " . $row["domain"] . "<br>";
            array_push($daily_report, ["count" => $row["count"], "domain" => $row["domain"], "date" => $row["date"]]);
        }
    } else {
        //echo "0 results";
    }
} else {
    $alert = 1;
    $message = "Error: " . $sql . "<br>" . $conn->error;
}

//$conn->close();

$daylyTable = '<center><h4>Summary today</h4><table style="font-family: arial, sans-serif;border-collapse: collapse;width: 100%;">
            <tr>
                <th style="border: 1px solid #dddddd;text-align: left;padding: 8px;background-color: #dddddd">Date</th>
                <th style="border: 1px solid #dddddd;text-align: left;padding: 8px;background-color: #dddddd">Limited Calls</th>
                <th style="border: 1px solid #dddddd;text-align: left;padding: 8px;background-color: #dddddd">Domain</th>
            </tr>';
foreach($daily_report as $row){
	$daylyTable = $daylyTable.'<tr>
                    <th style="border: 1px solid #dddddd;text-align: left;padding: 8px;">'.date("Y-m-d", strtotime($row['date'])).'</th>
                    <td style="border: 1px solid #dddddd;text-align: left;padding: 8px;">'.$row['count'].'</td>
                    <td style="border: 1px solid #dddddd;text-align: left;padding: 8px;"><a href="https://integration.swaypc.com/execute_on_limit/ajsbchcbkwdhcwdhbwhbcw_details.php?1=1&day='.date("Y-m-d", strtotime($row['date'])) . '&domain=' . $row['domain'].'">'.getDomainDescription($row['domain'], $domainRows) . '</a></td>
                </tr>';
}

$daylyTable = $daylyTable."</tbody></table></center>";
/////////////////////////////////////////////////////////////

//Monthly report///////////////////////////////////////////////
/*Execute task*/
$sql = "SELECT count(id) as count,domain,max(date_time) as date FROM `execute_on_limit` where date_time LIKE '" . $month . "%' and CHAR_LENGTH(execute_on_limit.from_num) > 6 and from_num not LIKE 'line_%' group by domain order by count desc";
$result = $conn->query($sql);
$monthly_report = array();
if ($result == TRUE) {
    //echo "Successfully";

    if ($result->num_rows > 0) {
        // output data of each row
        while ($row = $result->fetch_assoc()) {
            //echo "Count: " . $row["count"] . " Domain " . $row["domain"] . "<br>";
            array_push($monthly_report, ["count" => $row["count"], "domain" => $row["domain"], "date" => $row["date"]]);
        }
    } else {
        //echo "0 results";
    }
} else {
    $alert = 1;
    $message = "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();

$monthlyTable = '<center><h4>Summary of this month</h4><table style="font-family: arial, sans-serif;border-collapse: collapse;width: 100%;">
            <tr>
                <th style="border: 1px solid #dddddd;text-align: left;padding: 8px;background-color: #dddddd">Max Date</th>
                <th style="border: 1px solid #dddddd;text-align: left;padding: 8px;background-color: #dddddd">Limited Calls</th>
                <th style="border: 1px solid #dddddd;text-align: left;padding: 8px;background-color: #dddddd">Domain</th>
            </tr>';
foreach($monthly_report as $row){
	$monthlyTable = $monthlyTable.'<tr>
                    <th style="border: 1px solid #dddddd;text-align: left;padding: 8px;">'.date("Y-m-d", strtotime($row['date'])).'</th>
                    <td style="border: 1px solid #dddddd;text-align: left;padding: 8px;">'.$row['count'].'</td>
                    <td style="border: 1px solid #dddddd;text-align: left;padding: 8px;"><a href="https://integration.swaypc.com/execute_on_limit/ajsbchcbkwdhcwdhbwhbcw_details_monthly.php?1=1&month='. $month . '&domain=' . $row['domain'].'">'.getDomainDescription($row['domain'], $domainRows) . '</a></td>
                </tr>';
}

$monthlyTable = $monthlyTable."</tbody></table></center>";
/////////////////////////////////////////////////////////////

$message = "<body>";

// Step 1 - Enter your email address below. Send to!
$email = (isset($_GET['sendto'])) ? $_GET['sendto'] : $env['sendtoemail'];
// Step 1 - Enter your email address below. Send from!
$emailfrom = $env['emailfrom'];
$namefrom = $env['namefrom'];

$email = explode(",",$email);

// If the e-mail is not working, change the debug option to 2 | $debug = 2;
$debug = 2;

// If contact form don't has the subject input change the value of subject here
$subject = ( isset($_GET['subject']) ) ? $_GET['subject'] : 'Integration of execute on limit notification!';

$day = date("Y-m-d");
$getmessage = ( isset($_GET['message']) ) ? $_GET['message'] : 'Integration of execute on limit notification messages!';
$message =  '<h4>You can access here for domain summary: '.''.$env['reporturl'].'.<br></br> For domain summary by month: '.$env['reporturlmonthly'].'.</h4><br></br>';
$message = "<h3>We have ".$getmessage." callers limited for today!</h3><br></br>".$daylyTable ."<br></br>".$monthlyTable."<br></br>".$message;

$message = $message . "</body>";

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

	foreach($email as $addr){
		if($addr != "") $mail->AddAddress($addr);	 						       // Add another recipient
	}
	
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

