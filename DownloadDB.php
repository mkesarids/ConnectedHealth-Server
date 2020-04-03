<?php
	// Connection details
	$host = "ec2-18-206-84-251.compute-1.amazonaws.com";
	$db_name = "d33bcpvj60ocm8";
	$conn_string = "pgsql:host=".$host.";dbname=".$db_name;

	// Connect
	$user = "tljymawvctthhl";
	$password = "686e4e04d04863fa587a3843cdf69e943f3cd77ce5a12d17c10221a61f0e320b";

	try {
		$pdo = new PDO($conn_string, $user, $password);

		$stmt = $pdo->prepare("SELECT * FROM sensors;");

		if($stmt->execute()) {
			$delimiter = ",";
			$filename = "SensorData_" . date('Y-m-d') . ".csv";
      
			$f = fopen('php://memory', 'w');
      
			$fields = array('record_id', 'timestamp', 'name', 'accel_x', 'accel_y', 'accel_z', 'quat_x', 'quat_y', 'quat_z', 'quat_w', 'workout');
			fputcsv($f, $fields, $delimiter);
    
			while($row = $stmt->fetch(PDO::FETCH_NUM)){
				$lineData = array();
				for($i = 0; $i < count($row); $i++) {
					array_push($lineData, $row[$i]);
				}
				fputcsv($f, $lineData, $delimiter);
			}
      
			fseek($f, 0);
			
			header('Content-Type: text/csv');
			header('Content-Disposition: attachment; filename="' . $filename . '";');
      
			fpassthru($f);
		}

	} catch (PDOException $e) {
		echo "Error: ".$e->getMessage();
	}

?>
