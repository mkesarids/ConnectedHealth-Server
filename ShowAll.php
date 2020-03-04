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

		$result = $myPDO->query("SELECT * FROM sensors;");

    echo "<table>";
 
    while($row = mysql_fetch_array($result)){
      echo "<tr><td>" . $row['record_id'] . "</td><td>" . $row['user'] . "</td></tr>";
    }

    echo "</table>";

	} catch (PDOException $e) {
		echo "Error: ".$e->getMessage();
	}

?>
