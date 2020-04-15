<?php
	// Connection details
	$configs = include('config.php');
	$conn_string = 'pgsql:host='.$configs['host'].';dbname='.$configs['dbname'];

	try {
		$myPDO = new PDO($conn_string, $configs['username'], $configs['password']);

		// Create sensors table, 
		// Computer rotation: orient_x/y/z/w
		// Human rotation in degrees rotate_x/y/z
		$myPDO->exec('CREATE TABLE sensors (timestamp bigint, session_id bigint, record_id bigint, name text, workout text, accel_x real, accel_y real, accel_z real, orient_x real, orient_y real, orient_z real, orient_w real, rotate_x real, rotate_y real, rotate_z real)');

		$insert_string = 'INSERT INTO sensors (timestamp, session_id, record_id, name, workout, accel_x, accel_y, accel_z, orient_x, orient_y, orient_z, orient_w, rotate_x, rotate_y, rotate_z) VALUES (:timestamp, :session_id, :record_id, :name, :workout, :accel_x, :accel_y, :accel_z, :orient_x, :orient_y, :orient_z, :orient_w, :rotate_x, :rotate_y, :rotate_z)';
		$insert = $myPDO->prepare($insert_string);
		
		// Decoding JSON and collecting the data
		$raw_post = file_get_contents('php://input');
		$json_data = json_decode($raw_post, TRUE);
		foreach($json_data['data'] as $sensorData) {
			$insert->bindParam(':timestamp',$sensorData['timestamp']);
			$insert->bindParam(':session_id',$sensorData['session_id']);
			$insert->bindParam(':record_id',$sensorData['record_id']);
			$insert->bindParam(':name',$sensorData['name']);
			$insert->bindParam(':workout',$sensorData['workout']);
			$insert->bindParam(':accel_x',$sensorData['Acceleration']['X']);
			$insert->bindParam(':accel_y',$sensorData['Acceleration']['Y']);
			$insert->bindParam(':accel_z',$sensorData['Acceleration']['Z']);
			$insert->bindParam(':orient_x',$sensorData['Orientation']['X']);
			$insert->bindParam(':orient_y',$sensorData['Orientation']['Y']);
			$insert->bindParam(':orient_z',$sensorData['Orientation']['Z']);
			$insert->bindParam(':orient_w',$sensorData['Orientation']['W']);
			$insert->bindParam(':rotate_x',$sensorData['Rotation']['X']);
			$insert->bindParam(':rotate_y',$sensorData['Rotation']['Y']);
			$insert->bindParam(':rotate_z',$sensorData['Rotation']['Z']);

			$insert->execute();
		}

		echo "New records created successfully!";

	} catch (PDOException $e) {
		echo "Error: ".$e->getMessage();
	}
?>
