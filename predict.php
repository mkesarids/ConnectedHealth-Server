<?php
	// Connection details
	$configs = include('config.php');
	$conn_string = 'pgsql:host='.$configs['host'].';dbname='.$configs['dbname'];

	// Decode json
	$raw_post = file_get_contents('php://input');
	$json_data = json_decode($raw_post, TRUE);
	$json_data['status'] = "Good";
	foreach($json_data['data'] as $sensorData) {
		if($sensorData['Rotation']['Y'] > 5.0 || $sensorData['Rotation']['Y'] < -5.0)
			$json_data['status'] = "Arm is not level";
	}

	$response = json_encode($json_data);
	echo $response;
?>
