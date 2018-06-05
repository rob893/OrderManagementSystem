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
		<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
			<a class='navbar-brand' href='index.php'>
				<img src="guildLogo.JPG" width="30" height="30" class="d-inline-block align-top" alt="">
				Carry Manager
			</a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarNavAltMarkup">
				<div class="navbar-nav">
					<a href="index.php" class='nav-item nav-link'>Open Orders</a>
					<a href="completedOrders.php" class='nav-item nav-link'>Completed Orders</a>
					<a href="canceledOrders.php" class='nav-item nav-link'>Canceled Orders</a>
					<a href="services.php" class='nav-item nav-link'>Add Service</a>
					<a href="addBuyer.php" class='nav-item nav-link'>Add Buyer</a>
					<a href="addOrder.php" class='nav-item nav-link'>Add Order</a>
				</div>
			</div>
		</nav>
		<br>
		<br>
		<div class="container-fluid">
