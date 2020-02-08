<?php
	$host = "ec2-184-72-236-57.compute-1.amazonaws.com";
	$db_name = "d9eig3e03gvtfj";
	$conn_string = "pgsql:host=".$host.";dbname=".$db_name;

	$user = "bbvtmoyxqqsrbk";
	$password = "40fe9b6dc6a9b090eb05c5b17b5a9e0be4896301ce9b8a52b169e710db66a758";
	$myPDO = new PDO($conn_string, $user, $password);

	$myPDO->query('CREATE TABLE IF NOT EXISTS sensors (accel_x real, accel_y real, accel_z real, quat_x real, quat_y real, quat_z real, quat_w real)');

	$insert_string = 'INSERT INTO sensors (accel_x, accel_y, accel_z, quat_x, quat_y, quat_z, quat_w) VALUES (?, ?, ?, ?, ?, ?, ?)';
	$insert = $myPDO->prepare($insert_string);


	$raw_post = hexToStr($_GET['data']);
	$json_data = json_decode($raw_post, TRUE);
	foreach($json_data['data'] as $line) {
		$data = array($line['accel_x'],$line['accel_y'],$line['accel_z'],$line['quat_x'],$line['quat_y'],$line['quat_z'],$line['quat_w']);
		$insert->execute($data);
	}

	echo "Success!"

	function hexToStr($hex){
		$string='';
		for ($i=0; $i < strlen($hex)-1; $i+=2){
			$string .= chr(hexdec($hex[$i].$hex[$i+1]));
		}
		return $string;
	}
?>
