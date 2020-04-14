<?php
	// Connection details
	$configs = include('config.php');
	$conn_string = 'pgsql:host='.$configs['host'].';dbname='.$configs['dbname'];

	try {
		$myPDO = new PDO($conn_string, $configs['username'], $configs['password']);

		// Drop sensors table
		$myPDO->query('DROP TABLE sensors');

		echo "Table dropped successfully!";

	} catch (PDOException $e) {
		echo "Error: ".$e->getMessage();
	}
?>
