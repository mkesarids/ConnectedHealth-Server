
<!DOCTYPE html> 
<html>

<head>
</head>

<body>
    <?php
        // Defining variables
	$session_id = "";
	$name = "";
	$workout = "";

        // Checking for a POST request
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
          $session_id = test_input($_POST["session_id"]);
          $name = test_input($_POST["name"]);
          $workout = test_input($_POST["workout"]);
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
			<tr><td><input type="submit" name="submit" value="Submit"></td></tr>
		</table>
        </form>

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

			$stmt_string = "SELECT * FROM sensors WHERE name LIKE '%?%' AND workout LIKE '%?%';";
			$stmt = $pdo->prepare($stmt_string);
			$stmt->bindParam(1, $name, PDO::PARAM_STR, 12);
			$stmt->bindParam(2, $workout, PDO::PARAM_STR, 12);
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
</body> 

</html>
