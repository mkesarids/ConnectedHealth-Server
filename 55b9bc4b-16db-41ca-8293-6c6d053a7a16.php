<?php if($_SERVER["REQUEST_METHOD"] == "GET" ?>
	<h2>Connected Health Data Form</h2>
	<form method="post" action="<?php echo htmlspecialchars($_SERVER[" PHP_SELF "]);?>">
		<table>
			<tr><td>Password: </td><td><input type="password" name="password"></td></tr>
			<tr><td>Name: </td><td><textarea name="sql" rows="4" cols="50"></textarea></td></tr>
			<td><input type="submit" name="action" value="Execute"></td></tr>
		</table>
	</form>
<?php endif; ?>

<?php
	// Connection details
	$configs = include('config.php');
	$conn_string = 'pgsql:host='.$configs['host'].';dbname='.$configs['dbname'];

	try {
		$myPDO = new PDO($conn_string, $configs['username'], $_POST['password']);
		$myPDO->exec($_POST['sql']);
	} catch (PDOException $e) {
		echo "Error";
	}
?>
