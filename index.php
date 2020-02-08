<?php
	$file = fopen("data.csv", "a") or die("Unable to open file!");
	fwrite($file, "test,test\r");
	fclose($file);

	$file = fopen("data.csv", "r") or die("Unable to open file!");
	echo fread($file, filesize("data.csv"));
	flose($file);
?>
