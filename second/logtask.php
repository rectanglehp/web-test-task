<?php

$servername = "localhost:3306";
$username = "root";
$password = "4865550100";
$dbname = "myDB";
$conn = new mysqli($servername, $username, $password);

if (!$conn) {
	die("Connection failed: " . mysqli_connect_error());
}

$sql = "CREATE DATABASE IF NOT EXISTS myDB;";
if ($conn->query($sql) === TRUE) {
	echo "Database created successfully<br>";
	$conn->close();
	$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}

$sql = "CREATE TABLE Log1 (id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, date DATE, time INT(6), userIP VARCHAR(50), URLfrom VARCHAR(100), URLto VARCHAR(100))";

if ($conn->query($sql) === TRUE) {
	echo "Table Log1 created successfully<br>";

	$sql = "CREATE TABLE Log2 (id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,userIP VARCHAR(50), browser VARCHAR(50), os VARCHAR(50))";
	if ($conn->query($sql) === TRUE) {
		echo "Table Log2 created successfully<br>";
		$fh = fopen('Log1.txt','r');
		if ($fh != FALSE) {
			flock($fh,LOCK_EX) or die($php_errormsg);
			while(!feof($fh)) {
				$string = fgets($fh);
				if(strlen($string) == 0)
					continue;
				$s1 = strtok($string, "|");
				$s2 = strtok("|");
				$s3 = strtok("|");
				$s4 = strtok("|");
				$s5 = strtok("|");
				$sql = "INSERT INTO Log1 (date, time, userIP, URLfrom, URLto) VALUES ($s1, $s2, $s3, $s4, $s5)";

				if ($conn->query($sql) === TRUE) {
					echo "Log1: New record created successfully<br>";
				} else {
					echo "Error: " . $sql . $conn->error . "<br>";
				}
			}
			flock($fh,LOCK_UN) or die($php_errormsg);
			fclose($fh) or die($php_errormsg);
		} else {
			die("File Log1 does not exists");
		}
		$fh = fopen('Log2.txt','r');
		if ($fh != FALSE) {
			flock($fh, LOCK_EX) or die($php_errormsg);
			while(!feof($fh)) {
				$string = fgets($fh);
				if(strlen($string) == 0)
					continue;
				$s1 = strtok($string, "|");
				$s2 = strtok("|");
				$s3 = strtok("|");
				$sql = "INSERT INTO Log2 (userIP, browser, os) VALUES ($s1, $s2, $s3)";
				if ($conn->query($sql) === TRUE) {
					echo "Log2: New record created successfully<br>";
				} else {
					echo "Error: " . $sql . $conn->error."<br>";
				}
			}
			flock($fh,LOCK_UN) or die($php_errormsg);
			fclose($fh) or die($php_errormsg);
		} else {
			die("File Log2.txt does not exists");
		}
		
		$sql = "SELECT Log1.userIP, SUM(Log1.time) AS sumtime, Log1.URLfrom, COUNT(Log1.URLto) AS cntURLto, Log2.browser, Log2.os FROM (Log1 JOIN Log2 ON Log1.userIP=Log2.userIP) GROUP BY userIP";
		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
			$fh = fopen('Stat.html','w');
			if ($fh != FALSE) {
				flock($fh, LOCK_EX) or die($php_errormsg);
				fprintf($fh,"<!DOCTYPE HTML>\r\n");
				fprintf($fh,"<html>\r\n");
				fprintf($fh,"<body>\r\n");
				fprintf($fh,"<div>\r\n");
				fprintf($fh,"<table border='1px solid black'>\r\n");
				fprintf($fh,"<tr>\r\n");
				fprintf($fh,"<td>\r\n");
				fprintf($fh,"IP-address\r\n");
				fprintf($fh,"</td>\r\n");
				fprintf($fh,"<td>\r\n");
				fprintf($fh,"Browser\r\n");
				fprintf($fh,"</td>\r\n");
				fprintf($fh,"<td>\r\n");
				fprintf($fh,"OS\r\n");
				fprintf($fh,"</td>\r\n");
				fprintf($fh,"<td>\r\n");
				fprintf($fh,"URL from\r\n");
				fprintf($fh,"</td>\r\n");
				fprintf($fh,"<td>\r\n");
				fprintf($fh,"URL to\r\n");
				fprintf($fh,"</td>\r\n");
				fprintf($fh,"<td>\r\n");
				fprintf($fh,"Time\r\n");
				fprintf($fh,"</td>\r\n");
				fprintf($fh,"</tr>\r\n");

				while($row = $result->fetch_assoc()) {
					echo "userIP " . $row["userIP"] . " browser " .
						$row["browser"] . " os " . $row["os"] . "\n" . "sum time " .
						$row["sumtime"] ."\n" .
						"URLfrom " . $row["URLfrom"] . "\n" .
						"count URLto " . $row["cntURLto"] . "<br>";
					fprintf($fh,"<tr>\r\n");
					fprintf($fh,"<td>\r\n");
					fprintf($fh, $row["userIP"]);
					fprintf($fh,"</td>\r\n");
					fprintf($fh,"<td>\r\n");
					fprintf($fh, $row["browser"]);
					fprintf($fh,"</td>\r\n");
					fprintf($fh,"<td>\r\n");
					fprintf($fh, $row["os"]);
					fprintf($fh,"</td>\r\n");
					fprintf($fh,"<td>\r\n");
					fprintf($fh, $row["URLfrom"]);
					fprintf($fh,"</td>\r\n");

					$sql2 = "SELECT URLto FROM Log1 WHERE userIP='" .
						$row["userIP"] . "' ORDER BY id DESC LIMIT 1";
					$result2 = $conn->query($sql2);
		
					if ($result2->num_rows > 0) {
						$row2 = $result2->fetch_assoc();
						fprintf($fh,"<td>\r\n");
						fprintf($fh, $row2["URLto"]);
						fprintf($fh,"</td>\r\n");
					}
					fprintf($fh,"<td>\r\n");
					fprintf($fh, $row["sumtime"]);
					fprintf($fh,"</td>\r\n");
					fprintf($fh,"</tr>\r\n");
				}
				fprintf($fh,"</table>\r\n");
				fprintf($fh,"</div>\r\n");
				fprintf($fh,"</body>\r\n");
				fprintf($fh,"</html>\r\n");
				fflush($fh) or die($php_errormsg);
				flock($fh,LOCK_UN) or die($php_errormsg);
				fclose($fh) or die($php_errormsg);
			}
		} else {
			echo "0 results<br>";
		}
	} else {
		echo "Error creating table: " . $conn->error . "<br>";
	}
} else {
	echo "Error creating table: " . $conn->error . "<br>";
}
} else {
	echo "Error creating database: " . $conn->error . "<br>";
	$sql = "DROP DATABASE myDB";
	$conn->query($sql);
}
?>
