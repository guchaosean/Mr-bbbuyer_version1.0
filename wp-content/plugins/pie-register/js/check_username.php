<?php 
$username1=$_POST["username"];
$servername = "localhost";
$username = "mrbbbuye_sean";
$password = "19900825";
$dbname = "mrbbbuye_wordpress";
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
$sql1 = "SELECT * FROM wp_users where user_login='".$username1."'";
$result1 = $conn->query($sql1);

if ($result1->num_rows > 0) {
	echo "false";
}else{
	echo "true";
}

?>