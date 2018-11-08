<?php
	
	
	//?mesec=05&leto=2018
	
	
	if(isset($_GET['mesec'])&&isset($_GET['leto'])) {
		$mesec=$_GET['mesec'];
		$leto=$_GET['leto'];
	} else {
		echo("NAPAKA v URLju!");
		exit(1);
	}
	
	/*
	echo($mesec);
	echo("<br/>");
	echo($leto);
	echo("<br/>");
	*/
	
	
	$servername = "localhost";
	$username = "root";
	$password = "ife";
	$dbname = "burjaIO";
	
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	} 
	
	if($mesec=="Povprecje")
		$sql = "SELECT * FROM Povprecja where Leto = $leto ";//
	else{
		$sql = "SELECT * FROM Podatki where YEAR(TimeStamp) = $leto AND MONTH(TimeStamp) = $mesec ";
	}
	$result = $conn->query($sql);
	
	$rows = array();
	while($r = mysqli_fetch_assoc($result)) {
		$rows[] = $r;
	}
	echo( json_encode($rows) );
	
	$conn->close();
	
?>