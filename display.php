<?php
	// Connection details
	$configs = include('config.php');
        $conn_string = 'pgsql:host='.$configs['host'].';dbname='.$configs['dbname'];

	try {
		$pdo = new PDO($conn_string, $configs['username'], $configs['password']);

		$stmt = $pdo->prepare("SELECT * FROM sensors;");

		echo "<table>";

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
