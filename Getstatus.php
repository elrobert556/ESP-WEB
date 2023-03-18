<?php

$con = new mysqli("localhost","id19945353_joel","ABCabc123***","id19945353_espwifi");
$sql = "SELECT status from status ORDER BY id DESC LIMIT 1";
$res = $con->query($sql);
	
		while($row = $res->fetch_assoc()) 
		{
			echo $row["status"];					// Echo data , equivalent with send data to esp
		}
?>