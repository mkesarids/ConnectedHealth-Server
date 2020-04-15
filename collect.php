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
		
		// Convert hex->data->json and then execute with prepared statement
		$raw_post = file_get_contents('php://input'); // hexToStr($_GET['data']);
		echo $raw_post . "<br>";
		$json_data = json_decode($raw_post, TRUE);
		foreach($json_data['data'] as $line) {
			$insert->bindParam(':timestamp',$line['timestamp']);
			$insert->bindParam(':session_id',$line['session_id']);
			$insert->bindParam(':record_id',$line['record_id']);
			$insert->bindParam(':name',$line['name']);
			$insert->bindParam(':workout',$line['workout']);
			$insert->bindParam(':accel_x',$line['accel_x']);
			$insert->bindParam(':accel_y',$line['accel_y']);
			$insert->bindParam(':accel_z',$line['accel_z']);
			$insert->bindParam(':orient_x',$line['orient_x']);
			$insert->bindParam(':orient_y',$line['orient_y']);
			$insert->bindParam(':orient_z',$line['orient_z']);
			$insert->bindParam(':orient_w',$line['orient_w']);
			$insert->bindParam(':rotate_x',$line['rotate_x']);
			$insert->bindParam(':rotate_y',$line['rotate_y']);
			$insert->bindParam(':rotate_z',$line['rotate_z']);

			$insert->execute();
		}

		echo "New records created successfully!";

	} catch (PDOException $e) {
		echo "Error: ".$e->getMessage();
	}


	function hexToStr($hex){
		$string='';
		for ($i=0; $i < strlen($hex)-1; $i+=2){
			$string .= chr(hexdec($hex[$i].$hex[$i+1]));
		}
		return $string;
	}
?>
