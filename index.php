
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

			$column_stmt = $pdo->prepare("SELECT column_name FROM information_schema.columns WHERE table_name = 'sensors';");
			
			$values_stmt = $pdo->prepare("SELECT * FROM sensors WHERE name LIKE :name AND workout LIKE :workout;");
			$values_stmt->bindValue(":name", "%{$name}%");
			$values_stmt->bindValue(":workout", "%{$workout}%");
			
		} catch (PDOException $e) {
			echo "Error: ".$e->getMessage();
		}
        ?>
</body> 

</html>
