<?php
	function displayForm() {
		echo '<html>';
		echo '<body>';
		echo '<h2>ConnectedHealth Data</h2>';
		echo '<form method="post" action=\"<?php echo htmlspecialchars($_SERVER[\" PHP_SELF \"]);?>\">';
		echo '<table>';
		echo '<tr><td>Session id: </td><td><input type="text" name="session_id"></td></tr>';
		echo '<tr><td>Name: </td><td><input type="text" name="name"></td></tr>';
		echo '<tr><td>Workout: </td><td><input type="text" name="workout"></td></tr>';
		echo '<tr><td><input type="submit" name="action" value="Display"></td>';
		echo '<td><input type="submit" name="action" value="Download"></td></tr>';
		echo '</table>';
		echo '</form>';
	}

	if($_SERVER["REQUEST_METHOD"] == "GET") {
	} else if($_SERVER["REQUEST_METHOD"] == "POST") {
		// Defining variables
		$session_id = $name = $workout = $action = "";

        	// Get post data
		$session_id = test_input($_POST["session_id"]);
		$name = test_input($_POST["name"]);
		$workout = test_input($_POST["workout"]);
		$action = test_input($_POST["action"]);

		// Removing the redundant HTML characters if any exist.
		function test_input($data) {
			$data = trim($data);
			$data = stripslashes($data);
			$data = htmlspecialchars($data);
			return $data;
		}

		// Connection details
		$configs = include('config.php');
		$conn_string = 'pgsql:host='.$configs['host'].';dbname='.$configs['dbname'];
	
		try {
			$pdo = new PDO($conn_string, $configs['username'], $configs['password']);

			$column_stmt = $pdo->prepare("SELECT column_name FROM information_schema.columns WHERE table_name = 'sensors';");
		
			$values_stmt = $pdo->prepare("SELECT * FROM sensors WHERE name LIKE :name AND workout LIKE :workout;");
			$values_stmt->bindValue(":name", "%{$name}%");
			$values_stmt->bindValue(":workout", "%{$workout}%");
			
			if($action === "Display") {
				echo "<table>";
				if($column_stmt->execute()) {
					echo "<tr>";
					while($row = $column_stmt->fetch()){
						echo "<th>" . $row[0] . "</th>";
					}
					echo "</tr>";
				}
				
				if($values_stmt->execute()) {
					while($row = $values_stmt->fetch()){
						echo "<tr>";
						for($i = 0; $i < count($row); $i++) {
							echo "<td>" . $row[$i] . "</td>";
						}
						echo "</tr>";
					}
				}
				
				echo "</table>";
			} else if($action === "Download") {
				$delimiter = ",";
				$filename = "SensorData_" . date('Y-m-d') . ".csv";
				$f = fopen('php://memory', 'w');

				if($column_stmt->execute()) {
					$columns = array();
					while($row = $column_stmt->fetch()){
						array_push($columns, $row[0]);
					}
					fputcsv($f, $columns, $delimiter);
				}
				
				if($values_stmt->execute()) {
					while($row = $values_stmt->fetch(PDO::FETCH_NUM)){
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
		} catch (PDOException $e) {
			echo "Error: ".$e->getMessage();
		}
	}
?>
