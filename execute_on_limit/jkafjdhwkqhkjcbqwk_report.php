<?php

//Get ENV
$env = file_get_contents('../../.env', true);
$env = explode("\n", $env);
$getEnv = [];
foreach ($env as $data) {
    $data = explode("=", $data);
    $getEnv[$data[0]] = $data[1];
}
$env = $getEnv;
unset($getEnv);

$allowedHost = $env['ALLOWED_HOSTS'];
if(strpos($allowedHost, $_SERVER['REMOTE_ADDR']) === false && ((isset($_SERVER['HTTP_X_FORWARDED_FOR']) && strpos($allowedHost, $_SERVER['HTTP_X_FORWARDED_FOR']) === false) || !isset($_SERVER['HTTP_X_FORWARDED_FOR']))) {
	echo "access denied for ".$_SERVER['REMOTE_ADDR'];
	exit;
}

$servername = $env['servername'];
$username = $env['username'];
$password = $env['password'];
$dbname = $env['dbname'];

$domains = json_decode(file_get_contents('https://' . $env['pbx_domains_url_info'] . "?key=" . $env['pbx_domains_url_info_key']), true);
$domainRows = $domains['data']['domains'];
//var_dump($domainRows);

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
if (isset($_POST['day'])) $day = $_POST['day'];
$width = "";
if (isset($_GET['width'])) $width = $_GET['width'];

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

/*
Execute task
*/
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

$conn->close();

//fwrite($myfile, file_put_contents("log.txt", ob_get_flush()));
//fclose($myfile);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Count of limit calls! Quick Reports. Local server-side application</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="/resources/demos/style.css">

    <style>
        h2 {
            color: blue;
        }

        p {
            color: green;
        }
    </style>
</head>

<body>
    <?php if ($alert == 0) { ?>
        <div class="alert alert-primary" role="alert">
            <?php echo $message; ?>
        </div>
    <?php } else { ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $message; ?>
        </div>
    <?php } ?>

    <center>
        <h2>Limited calls domain/day!</h2>

        <form action=<?php echo $env['reporturl'].($width != ""?"?width=".$width:""); ?> method="POST">
			
            <p>DAY: <input type="text" id="datepicker" name="day" value="<?php echo $day ?>"></p>
            <input type="submit" value="Update" class="btn btn-outline-primary">
			<input type="button" value="Go to Month!" onclick="window.location.href = 'jkafjdhwkqhkjcbqwk_report_monthly.php';" class="btn btn-outline-secondary">
			
        </form>
    </center><br />

    <?php

    ?>

    <h4>Summary limited Calls domain/day: <?php echo $alltotal; ?></h4>

	<div class="table-responsive">
    <table class="table table-striped table-bordered table-hover">
        <thead class="thead-dark">
            <tr>
                <th scope="col">Date</th>
                <th scope="col">Limited Calls</th>
                <th scope="col">Domain</th>

            </tr>
        </thead>
        <tbody>
            <?php foreach ($daily_report as $row) : array_map('htmlentities', $row); ?>
                <tr class="<?php echo ($row['count']>10?"table-danger":($row['count']>5?"table-warning":""));?>">
                    <th scope="row"><?php echo date("Y-m-d", strtotime($row['date'])); ?></th>
                    <td><?php echo $row['count']; ?></td>
                    <td><a href="<?php echo $env['reportdetailsurl']; ?>?day=<?php echo date("Y-m-d", strtotime($row['date'])) . '&domain=' . $row['domain']; ?>"><?php echo getDomainDescription($row['domain'], $domainRows) . ($width > 800?" (" . $row['domain'] . ")":""); ?></a></td>

                </tr>
            <?php endforeach; ?>


        </tbody>
    </table>
		</div>


    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <script>
        $(function() {
            $("#datepicker").datepicker({
                dateFormat: 'yy-mm-dd'
            });
        });
    </script>
	
	 <script type="text/javascript">

        if(window.innerHeight > window.innerWidth){
            var landscapeOrPortrait = 'portrait';
        } else {
            var landscapeOrPortrait = 'landscape';
        }

        var width = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
		//console.log("width",width);
        var height = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;

		if("<?php echo $width;?>" == "" || "<?php echo $width;?>" != width) window.location.replace('jkafjdhwkqhkjcbqwk_report.php?width='+width); 
        //window.location.replace('jkafjdhwkqhkjcbqwk_report.php?width='+width+'&height='+height+'&landscapeOrPortrait='+landscapeOrPortrait);            
    </script>   


</body>

</html>
