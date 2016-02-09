<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
</head>
<body>
<?php  

$orderid=$_GET['orderid'];
 
$servername = "localhost";
$username = "mrbbbuye_sean";
$password = "19900825";
$dbname = "mrbbbuye_wordpress";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
     die("Connection failed: " . $conn->connect_error);
} 
$sql = "SELECT * FROM wp_woocommerce_order_jietu where order_id='".$orderid."'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
         $jietu=    $row["jietu_daima"] ;
		 $zhifuway= $row["zhifu_way"] ;
		 $user_commetn=$row["comments"];
    }
} 



 
     $sql2 = "SELECT * FROM wp_woocommerce_order_refeenumber where order_id='".$orderid."'";
     $result = $conn->query($sql2);
     if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
              $refee_number=    $row["refee_number"] ;
        }
     } 
	 if ($zhifuway=="zhifubao"){$zhifuway="支付宝支付";}
	 if ($zhifuway=="au_pay"){$zhifuway="澳洲银行支付";}
	 if ($zhifuway=="china_pay"){$zhifuway="中国国内银行支付";}
	 echo "支付方式为：".$zhifuway."<br><br>";
	 echo "他的支付refee number为：".$refee_number."<br><br>";
	 echo  "截图为: "."<img src='../../../../../../".$jietu."'></img><br><br>";
	 
	 echo "用户订单备注：".$user_commetn;
 
$conn->close();
?>
</body>
</html>