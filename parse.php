<?php

$link = new mysqli("127.0.0.1","root","ife","burjaIO");

if(!$link){
	echo "Error: Ni se mogoce povezati z MariaDB bazo.".PHP_EOL;
	echo "Debug errno: ".mysqli_connect_errno().PHP_EOL;
	echo "Debug error: ".mysqli_connect_error().PHP_EOL;
	exit;
}

if(isset($_GET['veter']) &&
	 isset($_GET['sunek']) && 
	 isset($_GET['lokacija']) )
{
	$lokacija=$_GET['lokacija'];
	$hitrost_sunki=$_GET['sunek']/100;
	$hitrost=$_GET['veter']/100;
	
	$sql = "SELECT DISTINCT(Latitude) as Latitude, Longitude FROM 		Podatki WHERE Lokacija='$lokacija' "; 
	$result=$link->query($sql);
	
	$naprava="Na lokaciji :".$lokacija;
	$row = $result->fetch_array(MYSQLI_ASSOC);
	
	$longt = $row['Longitude'];
	$lat = $row['Latitude'];
	
	$month=date('n');
	$year=date("Y");

	$sql ="SELECT * FROM Podatki WHERE MONTH(TimeStamp)='$month' AND Lokacija='$lokacija' AND YEAR(TimeStamp)='$year'";
	
	if($result=$link->query($sql)){
		$r=$result->num_rows;
		$result->close();
		//ce ga ni v bazi, potem dodaj novega
		if($r<=0){
			$sql = "INSERT INTO Podatki (Lokacija, Latitude, Longitude, Naprava, Sunki, HitrostVetra) VALUES ('$lokacija','$lat','$longt','$naprava','$hitrost_sunki','$hitrost')";
			
			$link->query($sql);
		}
		//ce je v bazi, potem updejtej
		else{
			$sql = "SELECT HitrostVetra, Sunki FROM Podatki WHERE MONTH(TimeStamp)='$month' AND Lokacija='$lokacija' AND YEAR(TimeStamp)='$year'";
			
			$result=$link->query($sql);
			$row = $result->fetch_array(MYSQLI_ASSOC);
			$v=$row["HitrostVetra"];
			$sunk=$row["Sunki"];
			
			if($v<$hitrost){
				$sql ="UPDATE Podatki SET HitrostVetra='$hitrost',TimeStamp=now() WHERE MONTH(TimeStamp)='$month' AND Lokacija='$lokacija' AND YEAR(TimeStamp)='$year'";
				$link->query($sql);
				echo $link->error;
			}
			
			if($sunk<$hitrost_sunki){
				$sql ="UPDATE Podatki SET TimeStamp=now(), Sunki='$hitrost_sunki' WHERE MONTH(TimeStamp)='$month' AND Lokacija='$lokacija' AND YEAR(TimeStamp)='$year'";
				$link->query($sql);
				echo $link->error;
			}
		}
	}
	
	echo "vpisano iz arduino!";
	
}
else{
	/*Token 1x na 23 ur*/
	$urlToken="https://www.promet.si/uc/user/token";
	
	$data = array('password' => 'BurjaIOT1', 'username' => 'ak3900@student.uni-lj.si','grant_type' => 'password');
	
	// use key 'http' even if you send the request to https://...
	$options = array(
		'http' => array(
			'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
			'method'  => 'POST',
			'content' => http_build_query($data)
		)
	);
	$context  = stream_context_create($options);
	$result = file_get_contents($urlToken, false, $context);
	
	if ($result === FALSE) {
		/* Handle error */
	}
	
	$token=json_decode($result);
	
	$urlJson="https://www.promet.si/dc/b2b.burja.geojson";
	
	// Create a stream
	$opts = array(
	  'http'=>array(
		'method'=>"GET",
		'header'=>"Authorization: bearer ".$token->access_token
	  )
	);
	
	$context = stream_context_create($opts);
	
	// Open the file using the HTTP headers set above
	$file = file_get_contents($urlJson, false, $context);
	
	echo "<br/>";
	//var_dump(json_decode($file), true);
	
	
	$podatki=json_decode($file);
	
	$lokacije=$podatki->features;
	
	for ($i = 0; $i <= 7; $i++) {
	
		/*geometry properties*/
		$lokacija=$lokacije[$i]->properties->title;
		$hitrost=$lokacije[$i]->properties->burja_veter;
		$hitrost_sunki=$lokacije[$i]->properties->burja_sunki;
		
		$hitrost=$hitrost/3.6;
		$hitrost_sunki=$hitrost_sunki/3.6;
		
		$naprava="LIVE Promet.si";
		$koordinate=$lokacije[$i]->geometry->coordinates;
		$longt = $koordinate[0];
		$lat = $koordinate[1];
	
	
		$month=date('n');
		$year=date("Y");

		$sql ="SELECT * FROM Podatki WHERE MONTH(TimeStamp)='$month' AND Lokacija='$lokacija' AND YEAR(TimeStamp)='$year'";
		
		if($result=$link->query($sql)){
			$r=$result->num_rows;
			$result->close();
			if($r==0){
				$sql = "INSERT INTO Podatki (Lokacija, Latitude, Longitude, Naprava, Sunki, HitrostVetra) VALUES ('$lokacija','$lat','$longt','$naprava','$hitrost_sunki','$hitrost')";
				
				$link->query($sql);
			}
			else{
				$sql = "SELECT HitrostVetra FROM Podatki WHERE MONTH(TimeStamp)='$month' AND Lokacija='$lokacija' AND YEAR(TimeStamp)='$year'";
			
				$result=$link->query($sql);
				$row = $result->fetch_array(MYSQLI_ASSOC);
				$v=$row["HitrostVetra"];
				$s=$row["Sunki"];
			
				if($v<$hitrost){
					$sql ="UPDATE Podatki SET HitrostVetra='$hitrost',TimeStamp=now() WHERE MONTH(TimeStamp)='$month' AND Lokacija='$lokacija' AND YEAR(TimeStamp)='$year'";
					$link->query($sql);
					echo $link->error;
				}
				
				if($s<$hitrost_sunki){
					$sql ="UPDATE Podatki SET TimeStamp=now(), Sunki='$hitrost_sunki' WHERE MONTH(TimeStamp)='$month' AND Lokacija='$lokacija' AND YEAR(TimeStamp)='$year'";
					$link->query($sql);
					echo $link->error;
				}
			}
		}
	}
	echo "vpisano iz PROMET!";
}

//RESET POVPRECJA
$sql ="DELETE FROM Povprecja";
$link->query($sql);
$sql="INSERT INTO Povprecja SELECT AVG(HitrostVetra) AS HitrostVetra, AVG(Sunki) AS PovprecniSunki, Lokacija,Latitude,Longitude, YEAR(TimeStamp) AS Leto FROM Podatki GROUP BY YEAR(TimeStamp), Lokacija";
$link->query($sql);

$link->close();

?>