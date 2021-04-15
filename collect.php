<?php
	// Connection details
	$configs = include('config.php');
	$conn_string = 'pgsql:host='.$configs['host'].';dbname='.$configs['dbname'];

	try {
		$myPDO = new PDO($conn_string, $configs['username'], $configs['password']);

		// Create sensors table, 
		// Computer rotation: orient_x/y/z/w
		// Human rotation in degrees rotate_x/y/z
		$myPDO->exec('CREATE TABLE sensors (timestamp bigint, session_id bigint, name text, age bigint, gender text, height bigint, workout text, workout_description text, heart_rate smallint, accel_x real, accel_y real, accel_z real, euler_orient_x real, euler_orient_y real, euler_orient_z real, quat_orient_x real, quat_orient_y real, quat_orient_z real, quat_orient_w real, rotation_x real, rotation_y real, rotation_z real)');

		$insert_string = 'INSERT INTO sensors (timestamp, session_id, name, age, gender, height, workout, workout_description, heart_rate, accel_x, accel_y, accel_z, euler_orient_x, euler_orient_y, euler_orient_z, quat_orient_x, quat_orient_y, quat_orient_z, quat_orient_w, rotation_x, rotation_y, rotation_z) VALUES (:timestamp, :session_id, :name, :age, :gender, :height, :workout, :workout_description, :heart_rate, :accel_x, :accel_y, :accel_z, :euler_orient_x, :euler_orient_y, :euler_orient_z, :quat_orient_x, :quat_orient_y, :quat_orient_z, :quat_orient_w, :rotation_x, :rotation_y, :rotation_z)';
		$insert = $myPDO->prepare($insert_string);

		// Decoding JSON and collecting the data
		$raw_post = file_get_contents('php://input');
		$json_data = json_decode($raw_post, TRUE);
		foreach($json_data['data'] as $sensorData) {
			$insert->bindParam(':timestamp',$sensorData['Timestamp']);
			$insert->bindParam(':session_id',$sensorData['session_id']);
			$insert->bindParam(':name',$sensorData['name']);
			$insert->bindParam(':age',$sensorData['age']);
			$insert->bindParam(':gender',$sensorData['gender']);
			$insert->bindParam(':height',$sensorData['height']);
			$insert->bindParam(':workout',$sensorData['workout']);
			$insert->bindParam(':workout_description',$sensorData['workout_description']);
			$insert->bindParam(':heart_rate',$sensorData['HeartRate']);
			$insert->bindParam(':accel_x',$sensorData['Acceleration']['X']);
			$insert->bindParam(':accel_y',$sensorData['Acceleration']['Y']);
			$insert->bindParam(':accel_z',$sensorData['Acceleration']['Z']);
			$insert->bindParam(':euler_orient_x',$sensorData['Euler_Orientation']['X']);
			$insert->bindParam(':euler_orient_y',$sensorData['Euler_Orientation']['Y']);
			$insert->bindParam(':euler_orient_z',$sensorData['Euler_Orientation']['Z']);
			$insert->bindParam(':quat_orient_x',$sensorData['Quaternion_Orientation']['X']);
			$insert->bindParam(':quat_orient_y',$sensorData['Quaternion_Orientation']['Y']);
			$insert->bindParam(':quat_orient_z',$sensorData['Quaternion_Orientation']['Z']);
			$insert->bindParam(':quat_orient_w',$sensorData['Quaternion_Orientation']['W']);
			$insert->bindParam(':rotation_x',$sensorData['Rotation']['X']);
			$insert->bindParam(':rotation_y',$sensorData['Rotation']['Y']);
			$insert->bindParam(':rotation_z',$sensorData['Rotation']['Z']);

			$insert->execute();
		}

		echo "New records created successfully!";

		// Check capacity and notify me
		$values_stmt = $myPDO->prepare("SELECT * FROM sensors;");
		$values_stmt->execute();
		$count = $values_stmt->rowCount();
		$remainder = $count % 500;
		
		if(remainder < 30) {
			file_get_contents("https://maker.ifttt.com/trigger/storage_warning/with/key/laXZALmNMF9xjRKG0_OTM0r-NF05QZk5hZ8UI6w2-W5?value1=$count");
		}
		
	} catch (PDOException $e) {
		echo "Error: ".$e->getMessage();
	}
?>
