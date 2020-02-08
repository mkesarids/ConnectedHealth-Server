<?php
	//$conn_string = "host=ec2-184-72-236-57.compute-1.amazonaws.com port=5432 dbname=d9eig3e03gvtfj user=bbvtmoyxqqsrbk password=40fe9b6dc6a9b090eb05c5b17b5a9e0be4896301ce9b8a52b169e710db66a758"
	//$db = pg_connect($conn_string) or die("Connection to database failed!");

	$conn_string = "pgsql:host=ec2-184-72-236-57.compute-1.amazonaws.com;dbname=d9eig3e03gvtfj";
	$user = "bbvtmoyxqqsrbk";
	$password = "40fe9b6dc6a9b090eb05c5b17b5a9e0be4896301ce9b8a52b169e710db66a758";
	$myPDO = new PDO($conn_string, $user, $password);

	$raw_post = hexToStr($_GET["data"]);
	$lines = explode('\n', $raw_post);
	foreach($lines as $json_line) {
		$line = json_decode($json_line);
	}
	echo "test";

	function hexToStr($hex){
		$string='';
		for ($i=0; $i < strlen($hex)-1; $i+=2){
			$string .= chr(hexdec($hex[$i].$hex[$i+1]));
		}
		return $string;
	}
?>
