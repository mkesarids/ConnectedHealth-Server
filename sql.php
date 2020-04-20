<?php
	// Connection details
	$configs = include('config.php');
	$conn_string = 'pgsql:host='.$configs['host'].';dbname='.$configs['dbname'];

	try {
		$myPDO = new PDO($conn_string, $configs['username'], $configs['password']);
		$insert_string = $_GET['sql'];
		$insert = $myPDO->prepare($insert_string);
		$insert->execute();

	} catch (PDOException $e) {
		echo "Error: ".$e->getMessage();
	}
?>
