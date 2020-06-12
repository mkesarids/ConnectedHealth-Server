<?php
	// Connection details
	$configs = include('config.php');
        $conn_string = 'pgsql:host='.$configs['host'].';dbname='.$configs['dbname'];

	try {
		$pdo = new PDO($conn_string, $configs['username'], $configs['password']);

		
		
		echo "<table>";
		$stmt_string = "SELECT column_name FROM information_schema.columns WHERE table_name = 'sensors';";
		$stmt = $pdo->prepare($stmt_string);
		if($stmt->execute()) {
			echo "<tr>";
			while($row = $stmt->fetch()){
				echo "<th>" . $row[0] . "</th>";
			}
			echo "</tr>";
		}
		
		$stmt_string = "SELECT * FROM sensors;";
		$stmt = $pdo->prepare($stmt_string);
		if($stmt->execute()) {
			while($row = $stmt->fetch()){
				echo "<tr>";
				for($i = 0; $i < count($row); $i++) {
					echo "<td>" . $row[$i] . "</td>";
				}
				echo "</tr>";
			}
		}
		echo "</table>";

	} catch (PDOException $e) {
		echo "Error: ".$e->getMessage();
	}
?>
