<h2>Connected Health Data Form</h2>
<form method="post" action="<?php echo htmlspecialchars($_SERVER[" PHP_SELF "]);?>">
	<table>
		<tr><td>Password: </td><td><input type="password" name="password"></td></tr>
		<tr><td>Query: </td><td><textarea name="query" rows="4" cols="50"></textarea></td></tr>
		<td><input type="submit" name="action" value="Execute"></td></tr>
	</table>
</form>
<hr/>

<?php
	if($_SERVER["REQUEST_METHOD"] == "POST") {
		// Connection details
		$configs = include('config.php');
		$conn_string = 'pgsql:host='.$configs['host'].';dbname='.$configs['dbname'];

		try {
			$myPDO = new PDO($conn_string, $configs['username'], $_POST['password']);
			$myPDO->exec($_POST['query']);
		} catch (PDOException $e) {
			echo "Error";
		}
	}
?>
