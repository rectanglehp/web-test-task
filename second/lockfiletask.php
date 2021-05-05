<?php
$filename = 'test.txt';

function lockFile($filename) {
	do {
		$fs = fopen($filename, 'a+');
		if($fs) {
			flock($fs, LOCK_EX);
			return $fs;
		} else {
			sleep(1);
		}
	} while(!$fs);
}

function unlockFile($fs) {
	flock($fs, LOCK_UN);
	fclose($fs);
}

function handleFileContent($fs) {
	global $filename;
	$contents = explode(PHP_EOL, fread($fs,filesize($filename)));
	$n = 0;
	foreach($contents as $num) {
		$n += intval($num);
	}
	fwrite($fs,$n.PHP_EOL);
}

echo("TRYING TO LOCK FILE".PHP_EOL);
$fs = lockFile($filename);
echo("FILE LOCKED!".PHP_EOL);

sleep(10); /* some job imitation */

handleFileContent($fs);
unlockFile($fs);
echo("FILE UNLOCKED!".PHP_EOL);
?>
