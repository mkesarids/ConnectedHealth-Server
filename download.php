<?php
	// Connection details
	$configs = include('config.php');
	$conn_string = 'pgsql:host='.$configs['host'].';dbname='.$configs['dbname'];

	try {
		$pdo = new PDO($conn_string, $configs['username'], $configs['password']);

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
