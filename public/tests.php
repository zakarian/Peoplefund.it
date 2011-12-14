
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>Automated functional tests</title>
</head>
<body>
<h1>Automated functional tests</h1>
<?php
$password = 'testp789';

if (isset($_POST['password'])) {
	if ( $_POST['password'] == $password ) {
		run_tests();
	} else {
		$message = 'The password is wrong.';
		show_form( $message );
	}
} else {
	show_form();
}

function show_form($message = '') {
?>
	<div class="message"><?php if (!empty($message)) { echo $message; }?></div>
	<form action="tests.php" method="post">
		<label>Choose environment</label><br />
		<input type="checkbox" name="test-suites" value="Development" checked="checked"> Development<br>
		<input type="checkbox" name="test-suites" value="Live" checked="checked"> Live<br>
		<label>Enter your password </label>
		<input type="password" name="password" />
		<input type="submit" name="submit" value="submit" />
	</form>
<?php }
function run_tests() {
	$logfile = 'logs/selenium.log';

	unset($output);
	$logstring = '';
	
	//$command = 'python ../tests/suite.py 2>&1'; // Local command
	$command = 'xvfb-run /usr/bin/python ../tests/suite.py 2>&1'; // Server command
	$result = exec($command, $output);

	echo '<h3>Output:</h3> <br>';
	$logstring .= PHP_EOL . date('m.d.y H:i:s') . PHP_EOL;
	foreach ( $output as $out ) {
		echo $out. '<br>';
		$logstring .= $out . PHP_EOL;
	}
	$logstring .= PHP_EOL;
	// write logs
	
	if (!write_log($logstring, $logfile)) 
		echo 'Logging was not successful.';	
}
function write_log($log, $file) {
	if ($fh = fopen( $file, "a+")) {
		fputs( $fh, $log, strlen($log) );
		fclose( $fh );
		return( TRUE );
	}
	else
	{
		return( false );
	}
}

?>
</body>
</html>