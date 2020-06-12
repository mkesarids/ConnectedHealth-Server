<?php
	// Connection details
	$configs = include('config.php');
	$conn_string = 'pgsql:host='.$configs['host'].';dbname='.$configs['dbname'];

	try {
		$myPDO = new PDO($conn_string, $configs['username'], $_GET['password']);
		$stmt_string = $_GET['sql'];
		$stmt = $myPDO->prepare($stmt_string);
    
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
