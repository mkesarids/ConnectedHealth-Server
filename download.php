<?php
	// Connection details
	$configs = include('config.php');
	$conn_string = 'pgsql:host='.$configs['host'].';dbname='.$configs['dbname'];

	try {
		$pdo = new PDO($conn_string, $configs['username'], $configs['password']);
		
		$delimiter = ",";
		$filename = "SensorData_" . date('Y-m-d') . ".csv";
		$f = fopen('php://memory', 'w');
		
		$stmt = $pdo->prepare("SELECT column_name FROM information_schema.columns WHERE table_name = 'sensors';");
		if($stmt->execute()) {
			$columns = array();
			while($row = $stmt->fetch()){
				array_push($columns, $row[0]);
			}
			fputcsv($f, $columns, $delimiter);
		}
		
		$stmt = $pdo->prepare("SELECT * FROM sensors;");
		if($stmt->execute()) {
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
