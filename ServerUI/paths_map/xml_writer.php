<?php

require("phpsqlajax_dbinfo.php");

$connection=mysqli_connect ('localhost', $username, $password,$database);
if (!$connection) {  die('Not connected : ' . mysqli_error($connection));}

// Select all the rows in the markers table

$query = "SELECT a.mac, a.lat, a.lon, a.timestamp FROM sensorreadings AS a WHERE (SELECT COUNT(*) FROM sensorreadings 
          AS b WHERE b.mac = a.mac AND b.timestamp >= a.timestamp) <= 4
		  ORDER BY a.mac ASC, a.timestamp DESC";
$result = mysqli_query($connection,$query);

$xml = new SimpleXMLElement('<markers/>');

$first = 1;
$marker = NULL;
//foreach ($result as $row)    //while works for the crin server
while ($row = mysqli_fetch_array($result))
{
	if($first == 1){
		$current_mac = $row['mac'];
	}
	
	if(($row['lat']!=0)&&($row['lon']!=0)){
	
	   if($first == 1){
		   $marker = $xml->addChild('marker');
		   $marker['mac'] = $row['mac'];
		   $info = $marker->addChild('info');
		   foreach ($row as $key => $value){
			   $info[$key] = $value;
		   }
		   $first = 0;
	   }
	   else{
		   if($row['mac'] != $current_mac){
			   $marker = $xml->addChild('marker');
		       $marker['mac'] = $row['mac'];
		       $info = $marker->addChild('info');
		       foreach ($row as $key => $value){
			      $info[$key] = $value;
		       }
			   $current_mac = $row['mac'];
		   }
		   else{
			   $info = $marker->addChild('info');
			   foreach ($row as $key => $value){			  
			      $info[$key] = $value;
		       }
		   }
	   }
	
	//	$marker = $xml->addChild('marker');
	//	foreach ($row as $key => $value) 
	//	{
	//		$marker[$key] = $value;
	//	}
	}
}

header("Content-type: text/xml");
$xml->asXML('php://output');

?>