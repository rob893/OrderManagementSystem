<?php
    $servername="localhost";
    $user="other";
    $password="password";
    $database="orderManagementSystem";
    $conn = new mysqli($servername, $user, $password, $database);
    if($conn->connect_error){
        die("Connection failed: " .$conn->connect_error);
    } /*else {
		echo 'Connection successful.';
	}*/
?>
