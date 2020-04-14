<?php
	// Connection details
	$configs = include('config.php');
	$conn_string = 'pgsql:host='.$configs['host'].';dbname='.$configs['dbname'];

	// Decode json
	$raw_post = file_get_contents('php://input');
	$json_data = json_decode($raw_post, TRUE);
	foreach($json_data['data'] as $sensorData) {
		echo $sensorData['Acceleration']['X'];
	}
?>
