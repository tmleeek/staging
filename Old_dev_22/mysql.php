<?php

$servername = "87.98.135.129";
$username = "dataazb_liveazb";
$password = "c2vvr19k8who";
$dbname = "dataazb_azboutique";

// Create connection
$conn = mysql_connect($servername, $username, $password);
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
echo "Connected successfully";
exit;
?> 