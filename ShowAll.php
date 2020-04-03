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
