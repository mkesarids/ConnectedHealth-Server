<?php
	// Connection details
	$host = "ec2-18-235-97-230.compute-1.amazonaws.com";
	$db_name = "d3j59l1am40bi2";
	$conn_string = "pgsql:host=".$host.";dbname=".$db_name;

	// Connect
	$user = "flcdhpgwtajhxu";
	$password = "486d455e66df445d34924aefad87c58170ff1893a9188bc87042c7d31de519b5";

	try {
		$myPDO = new PDO($conn_string, $user, $password);

		// Create sensors table
		$myPDO->exec('CREATE TABLE sensors (record_id text, user text, accel_x real, accel_y real, accel_z real, quat_x real, quat_y real, quat_z real, quat_w real, workout text)');
		
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
