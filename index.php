
<!DOCTYPE html> 
<html>

<head>
</head>

<body>
    <?php
        // Defining variables
	$session_id = $name = $workout = $action = "";

        // Checking for a POST request
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$session_id = test_input($_POST["session_id"]);
		$name = test_input($_POST["name"]);
		$workout = test_input($_POST["workout"]);
		$action = test_input($_POST["action"]);
        }

        // Removing the redundant HTML characters if any exist.
        function test_input($data) {
          $data = trim($data);
          $data = stripslashes($data);
          $data = htmlspecialchars($data);
          return $data;
        }
        ?>

        <h2>ConnectedHealth Data Download</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER[" PHP_SELF "]);?>">
		<table>
			<tr><td>Session id: </td><td><input type="text" name="session_id"></td></tr>
			<tr><td>Name: </td><td><input type="text" name="name"></td></tr>
			<tr><td>Workout: </td><td><input type="text" name="workout"></td></tr>
			<tr><td><input type="submit" name="action" value="Display"></td>
			<td><input type="submit" name="action" value="Download"></td></tr>
		</table>
        </form>

        <?php
		// Connection details
		$configs = include('config.php');
		$conn_string = 'pgsql:host='.$configs['host'].';dbname='.$configs['dbname'];
	
		try {
			$pdo = new PDO($conn_string, $configs['username'], $configs['password']);

			
			$col_stmt = $pdo->prepare("SELECT column_name FROM information_schema.columns WHERE table_name = 'sensors';");

			$stmt_string = "SELECT * FROM sensors WHERE name LIKE :name AND workout LIKE :workout;";
			$stmt = $pdo->prepare($stmt_string);
			$stmt->bindValue(":name", "%{$name}%");
			$stmt->bindValue(":workout", "%{$workout}%");
			if($stmt->execute()) {
				if($action === "Display") {
					
					echo "<table>";
					if($col_stmt->execute()) {
						echo "<tr>";
						while($row = $stmt->fetch()){
							echo "<th>" . $row[0] . "</th>";
						}
						echo "</tr>";
					}
					
					while($row = $stmt->fetch()){
						echo "<tr>";
						for($i = 0; $i < count($row); $i++) {
							echo "<td>" . $row[$i] . "</td>";
						}
						echo "</tr>";
					}
					
					echo "</table>";
				} else if($action === "Download) {
					$delimiter = ",";
					$filename = "SensorData_" . date('Y-m-d') . ".csv";
					$f = fopen('php://memory', 'w');
					
					if($col_stmt->execute()) {
						$columns = array();
						while($row = $stmt->fetch()){
							array_push($columns, $row[0]);
						}
						fputcsv($f, $columns, $delimiter);
					}
		
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
				}
			}
		} catch (PDOException $e) {
			echo "Error: ".$e->getMessage();
		}
        ?>
</body> 

</html>
