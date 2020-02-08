<?php
	$file = fopen("data.csv", "w") or die("Unable to open file!");
	fwrite($file, "test,test\n");
	fclose($file);

	$file = fopen("data.csv", "r") or die("Unable to open file!");
	echo fread($file, filesize("data.csv"));
	flose($file);
?>
