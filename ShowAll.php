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

		echo "<table>";

		if($stmt->execute()) {
			while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
				echo "<tr><td>" . $row[0] . "</td><td>" . $row['Timestamp'] . "</td></tr>";
			}
		}

		echo "</table>";

	} catch (PDOException $e) {
		echo "Error: ".$e->getMessage();
	}

?>
