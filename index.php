<?php
	// Connection details
	$host = "ec2-52-23-14-156.compute-1.amazonaws.com";
	$db_name = "d85kjmio5bil9";
	$conn_string = "pgsql:host=".$host.";dbname=".$db_name;

	// Connect
	$user = "zvlpgcwvbqmith";
	$password = "0be450dccaf7fd1b604d04d30d9da56bdb3221cbac51997bc1285b7dc5c58dea";

	try {
		$myPDO = new PDO($conn_string, $user, $password);

		// Create sensors table
		$myPDO->query('CREATE TABLE IF NOT EXISTS sensors (record_id bigint, user text, accel_x real, accel_y real, accel_z real, quat_x real, quat_y real, quat_z real, quat_w real, workout text)');

		// Prepare insertion statement
		$insert_string = 'INSERT INTO sensors (record_id, user, accel_x, accel_y, accel_z, quat_x, quat_y, quat_z, quat_w, workout) VALUES (:record_id, :user, :accel_x, :accel_y, :accel_z, :quat_x, :quat_y, :quat_z, :quat_w, :workout)';
		$insert = $myPDO->prepare($insert_string);


		// Convert hex->data->json and then execute with prepared statement
		$raw_post = hexToStr($_GET['data']);
		$json_data = json_decode($raw_post, TRUE);
		foreach($json_data['data'] as $line) {
			$insert->bindParam(':record_id',$line['record_id']);
			$insert->bindParam(':user',$line['user']);
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
