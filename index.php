<?php if($_SERVER["REQUEST_METHOD"] == "GET" | $_POST["action"] === "Display"): ?>
	<h2>Connected Health Data Form</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER[" PHP_SELF "]);?>">
		<table>
			<tr><td>Session id: </td><td><input type="text" name="session_id"></td></tr>
			<tr><td>Name: </td><td><input type="text" name="name"></td></tr>
			<tr><td>Workout: </td><td><input type="text" name="workout"></td></tr>
			<tr><td><input type="submit" name="action" value="Display"></td>
			<td><input type="submit" name="action" value="Download"></td></tr>
		</table>
        </form>
	<hr/>
<?php endif; ?>
<?php 
	// Removing the redundant HTML characters if any exist.
	function test_input($data) {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}

	if($_SERVER["REQUEST_METHOD"] == "POST") {
		// Defining variables
		$session_id = $name = $workout = $action = "";

        	// Get post data
		$session_id = test_input($_POST["session_id"]);
		$name = test_input($_POST["name"]);
		$workout = test_input($_POST["workout"]);
		$action = test_input($_POST["action"]);


		// Connection details
		$configs = include('config.php');
		$conn_string = 'pgsql:host='.$configs['host'].';dbname='.$configs['dbname'];

		try {
			$pdo = new PDO($conn_string, $configs['username'], $configs['password']);

			$column_stmt = $pdo->prepare("SELECT column_name FROM information_schema.columns WHERE table_name = 'sensors';");

			$values_stmt = $pdo->prepare("SELECT * FROM sensors WHERE " . (!empty($session_id) ? "session_id = :session_id AND " : "") . "name LIKE :name AND workout LIKE :workout ORDER BY name, session_id, timestamp;");
			$values_stmt->bindValue(":session_id", (int)$session_id);
			$values_stmt->bindValue(":name", "%{$name}%");
			$values_stmt->bindValue(":workout", "%{$workout}%");

			if($action === "Display") {
				$columns = array();
				
				echo "<table>";
				if($column_stmt->execute()) {
					echo "<tr>";
					echo "<th>date</th>";
					echo "<th>time</th>";
					while($row = $column_stmt->fetch()){
						echo "<th>" . $row[0] . "</th>";
						array_push($columns, $row[0]);
					}
					echo "<th>elapsed</th>";
					echo "</tr>";
				}

				if($values_stmt->execute()) {
					$start = 0;
					$session_id = 0;
					
					while($row = $values_stmt->fetch(PDO::FETCH_ASSOC)){
						echo "<tr>";
						
						// Add date and time
						echo "<td>" . gmdate("m-d-Y", $row['timestamp']/1000) . "</td>";
						echo "<td>" . gmdate("H:i:s", $row['timestamp']/1000) . "</td>";
						
						foreach($columns as $col) {
							echo "<td>" . strip_tags($row[$col]) . "</td>";
						}
						
						// Isolate session
						if($row['session_id'] != $session_id) {
							$start = $row['timestamp'];
							$session_id = $row['session_id'];
						}
						
						echo "<td>" . ($row['timestamp'] - $start) . "</td>"; // Add elapsed time
						echo "</tr>";
					}
				}

				echo "</table>";
			} else if($action === "Download") {
				$delimiter = ",";
				$filename = "SensorData_" . date('Y-m-d') . ".csv";
				$f = fopen('php://memory', 'w');
				$columns = array();

				if($column_stmt->execute()) {
					while($row = $column_stmt->fetch()){
						array_push($columns, $row[0]);
					}
					
					array_push($columns, "elapsed"); // Patch on the elapsed time column
					fputcsv($f, $columns, $delimiter);
				}

				if($values_stmt->execute()) {
					$start = 0;
					$session_id = 0;
					
					while($row = $values_stmt->fetch(PDO::FETCH_ASSOC)){
						$lineData = array();
						
						// Add date and time
						array_push($lineData, gmdate("m-d-Y", $row['timestamp']/1000));
						array_push($lineData, gmdate("H:i:s", $row['timestamp']/1000));
						
						foreach($columns as $col) {
							array_push($lineData, $row[$col]);
						}
						
						// Isolate session
						if($row['session_id'] != $session_id) {
							$start = $row['timestamp'];
							$session_id = $row['session_id'];
						}
						
						array_push($lineData, $row['timestamp'] - $start); // Add elapsed time
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
