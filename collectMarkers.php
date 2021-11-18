<?php
$userID = $_GET['id'];

$vote = intval($_GET['vote']);
$gpsLat = floatval($_GET['GPSlatitude']);
$gpsLong = floatval($_GET['GPSlongitude']);


$conn = new mysqli('localhost','fusy','Mussio4ever!','RankerGPS');
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
} 

$sql="SELECT * FROM pins";

$result = $conn->query($sql);

echo json_encode(mysqli_fetch_all($result));

$conn->close();
?>