<?php
	// Connection details
	$host = "ec2-18-206-84-251.compute-1.amazonaws.com";
	$db_name = "d33bcpvj60ocm8";
	$conn_string = "pgsql:host=".$host.";dbname=".$db_name;

	// Connect
	$user = "tljymawvctthhl";
	$password = "686e4e04d04863fa587a3843cdf69e943f3cd77ce5a12d17c10221a61f0e320b";

	try {
		$myPDO = new PDO($conn_string, $user, $password);

		// Create sensors table
		$myPDO->exec('CREATE TABLE sensors (record_id text, timestamp bigint, name text, accel_x real, accel_y real, accel_z real, quat_x real, quat_y real, quat_z real, quat_w real, workout text)');

		$insert_string = 'INSERT INTO sensors (record_id, timestamp, name, accel_x, accel_y, accel_z, quat_x, quat_y, quat_z, quat_w, workout) VALUES (:record_id, :timestamp, :name, :accel_x, :accel_y, :accel_z, :quat_x, :quat_y, :quat_z, :quat_w, :workout)';
		$insert = $myPDO->prepare($insert_string);
		
		// Convert hex->data->json and then execute with prepared statement
		$raw_post = file_get_contents('php://input'); // hexToStr($_GET['data']);
		echo $raw_post . "<br>";
		$json_data = json_decode($raw_post, TRUE);
		foreach($json_data['data'] as $line) {
			$insert->bindParam(':record_id',$line['record_id']);
			$insert->bindParam(':timestamp',$line['timestamp']);
			$insert->bindParam(':name',$line['name']);
			$insert->bindParam(':accel_x',$line['accel_x']);
			$insert->bindParam(':accel_y',$line['accel_y']);
			$insert->bindParam(':accel_z',$line['accel_z']);
			$insert->bindParam(':quat_x',$line['quat_x']);
			$insert->bindParam(':quat_y',$line['quat_y']);
			$insert->bindParam(':quat_z',$line['quat_z']);
			$insert->bindParam(':quat_w',$line['quat_w']);
			$insert->bindParam(':workout',$line['workout']);

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
