<?php
	// Connection details
	$host = "ec2-18-235-97-230.compute-1.amazonaws.com";
	$db_name = "d3j59l1am40bi2";
	$conn_string = "pgsql:host=".$host.";dbname=".$db_name;

	// Connect
	$user = "flcdhpgwtajhxu";
	$password = "486d455e66df445d34924aefad87c58170ff1893a9188bc87042c7d31de519b5";

	try {
		$pdo = new PDO($conn_string, $user, $password);

		$stmt = $pdo->prepare("SELECT * FROM sensors;");

		if($stmt->execute()) {
      $delimiter = ",";
      $filename = "SensorData_" . date('Y-m-d') . ".csv";
      
      $f = fopen('php://memory', 'w');
      
      $fields = array('record_id', 'timestamp', 'name', 'accel_x', 'accel_y', 'accel_z', 'quat_x', 'quat_y', 'quat_z', 'quat_w', 'workout');
      fputcsv($f, $fields, $delimiter);
    
			while($row = $stmt->fetch()){
        $lineData = array();
				for($i = 0; $i < count($row); $i++) {
					array_push($lineData, $row[$i]);
				}
        fputcsv($f, $lineData, $delimiter);
			}
      
      header('Content-Type: text/csv');
      header('Content-Disposition: attachment; filename="' . $filename . '";');
      
      fpassthru($f);
		}

	} catch (PDOException $e) {
		echo "Error: ".$e->getMessage();
	}

?>
