<?php

require("phpsqlajax_dbinfo.php");

$connection=mysqli_connect ('localhost', $username, $password,$database);
if (!$connection) {  die('Not connected : ' . mysqli_error($connection));}

// Select all the rows in the markers table

$query = "SELECT a.mac,a.lat,a.lon,a.timestamp FROM sensorreadings a
INNER JOIN (
SELECT mac, Max(timestamp) as latesttime 
FROM sensorreadings 
GROUP BY mac 
) b ON a.mac = b.mac AND a.timestamp = b.latesttime";
$result = mysqli_query($connection,$query);

$xml = new SimpleXMLElement('<markers/>');

//foreach ($result as $row)    //while works for the crin server
while ($row = mysqli_fetch_array($result))
{
	if(($row['lat']!=0)&&($row['lon']!=0)){
	
		$marker = $xml->addChild('marker');
		foreach ($row as $key => $value) 
		{
			$marker[$key] = $value;
		}
	}
}

header("Content-type: text/xml");
$xml->asXML('php://output');

?>