<?php
	
	$link = new mysqli("127.0.0.1","root","ife","burjaIO");
	
	if(!$link){
		
		echo "Error: Ni se mogoce povezati z MariaDB bazo.".PHP_EOL;
		echo "Debug errno: ".mysqli_connect_errno().PHP_EOL;
		echo "Debug error: ".mysqli_connect_error().PHP_EOL;
		exit;
	}
	
	if(
		isset($_GET["lokacija"]) &&
		isset($_GET["lat"]) &&
		isset($_GET["longt"]) &&
		isset($_GET["naprava"]) &&
		isset($_GET["temperatura"]) &&
		isset($_GET["hitrost"])
	){
		$lokacija = $_GET["lokacija"];
		$lat = $_GET["lat"];
		$longt = $_GET["longt"];
		$naprava=$_GET["naprava"];
		$temperatura = $_GET["temperatura"];
		$hitrost = $_GET["hitrost"];
		
		$sql = "INSERT INTO Podatki (Lokacija, Latitude, Longtitude, Naprava, Temperatura, HitrostVetra) VALUES ('$lokacija','$lat','$longt','$naprava','$temperatura','$hitrost')";
		echo $sql;
		$link->query($sql);
		echo $link->error;
		$link->close();
	}
	else{
		echo "Podatki niso podani!!";
		$link->close();
	}
	
	
	
	
	
?>