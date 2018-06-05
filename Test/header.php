<?php
//ini_set('display_errors', true);
require_once('dbconnection.php');
?>

<!DOCTYPE html>
<html lang="en-US">
	<head>
		 <style>
		   #map {
			height: 400px;
			width: 100%;
		   }
		</style>
		<!-- Required meta tags -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

		<!-- Bootstrap CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
		<title>Carry Manager</title>
	</head>
	<body>
		<div class="jumbotron">
			<h1>Carry Manager</h1>
			<a href="index.php" class='btn btn-info' role='button'>Open Orders</a>
			<a href="completedOrders.php" class='btn btn-info' role='button'>Completed Orders</a>
			<a href="canceledOrders.php" class='btn btn-info' role='button'>Canceled Orders</a>
			<a href="services.php" class='btn btn-info' role='button'>Add Service</a>
			<a href="addOrder.php" class='btn btn-info' role='button'>Add Order</a>
			<a href="addBuyer.php" class='btn btn-info' role='button'>Add Buyer</a>
			<br>
		</div>
		
		<div class="container-fluid">
