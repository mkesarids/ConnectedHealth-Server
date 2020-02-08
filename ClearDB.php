<?php
	// Connection details
	$host = "ec2-184-72-236-57.compute-1.amazonaws.com";
	$db_name = "d9eig3e03gvtfj";
	$conn_string = "pgsql:host=".$host.";dbname=".$db_name;

	// Connect
	$user = "bbvtmoyxqqsrbk";
	$password = "40fe9b6dc6a9b090eb05c5b17b5a9e0be4896301ce9b8a52b169e710db66a758";

	try {
		$myPDO = new PDO($conn_string, $user, $password);

		// Drop sensors table
		$myPDO->query('DROP TABLE sensors');

		echo "Table dropped successfully!";

	} catch (PDOException $e) {
		echo "Error: ".$e->getMessage();
	}
?>
