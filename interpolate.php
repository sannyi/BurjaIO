<?php


$year = date("Y");


$link = new mysqli("127.0.0.1","root","ife","burjaIO");
	
if(!$link){
    
    echo "Error: Ni se mogoce povezati z MariaDB bazo.".PHP_EOL;
    echo "Debug errno: ".mysqli_connect_errno().PHP_EOL;
    echo "Debug error: ".mysqli_connect_error().PHP_EOL;
    exit;
}
$sql = " SELECT DISTINCT(Lokacija) as Lokacija, Latitude,Longitude FROM Podatki WHERE Lokacija  NOT IN (SELECT Lokacija FROM Podatki WHERE Naprava IN ('LIVE Promet.si','Arhiv','Arduino'))";

$result=$link->query($sql);
$lokacije =array();
$lat=array();
$long  =array();
while($row = $result->fetch_assoc())
{
    array_push($lokacije,$row["Lokacija"]);
    array_push($lat,$row["Latitude"]);
    array_push($long,$row["Longitude"]);
}

//var_dump($long);

for($first_year=$year; $first_year<=$year;$first_year++)
{
    for($month=6; $month<=date('n');$month++)
    {
        if($first_year==2017)
        {
            $month=12;
        }
        if ($month==4) {continue;}
        
        $sql ="SELECT AVG(HitrostVetra) as H, AVG(Sunki) as S FROM Podatki WHERE  MONTH(TimeStamp)=$month and YEAR(TimeStamp)=$first_year";
        $result=$link->query($sql);
      //  echo $sql;
        while($row = $result->fetch_assoc())
        {
            $h1 = $row["H"]; 
            $s1=$row["S"]; 
            
        }
    
       
        for($j=0;$j<count($lokacije);$j++)
        { 

            $h=$h1;
            $s=$s1;
           // var_dump($h);
              //  exit;
            //random za hitrost
              $random = mt_rand(5, 21) / mt_rand(1,4);
              $predznak = mt_rand(0,1);

              if($predznak==0)
                              $h += $random*(-1);
              else
                              $h += $random;
      //sunki
              $random = mt_rand(5, 21) / mt_rand(1,4);
              $predznak = mt_rand(0,1);

              if($predznak==0)
                    $s += $random*(-1);
              else
                    $s += $random;

                if($s<0)
                        $s=$s*(-1);
                if($h<0)
                        $h=$h*(-1);
             
                $timeStamp = $first_year."-".$month."-28 23:59:59";
                $l=$lokacije[$j];
                $sql="INSERT INTO Podatki VALUES('$l','$lat[$j]','$long[$j]','$timeStamp','RSkripta','$s','$h')";
             echo $sql."<br>";
               $link->query($sql);
        }


        

    }
} mysqli_close($link);
?>