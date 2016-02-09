<?php 
 date_default_timezone_set('UTC');
 echo  "
 <table style='border:1px solid #F00;display:table; width:500px;'>
 <tr>
    <td>Name</td>
	<td>Wechat</td>
    <td>Birthday</td>
	
  </tr>";

$servername = "localhost";
$username = "mrbbbuye_sean";
$password = "19900825";
$dbname = "mrbbbuye_wordpress";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
     die("Connection failed: " . $conn->connect_error);
} 
$sql = "SELECT * FROM wp_usermeta where meta_key='pie_date_5'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
         $jietu= $row["meta_value"];
	     $user_id= $row["user_id"];
		 $pos1=stripos($jietu,"mm",0);
		 $pos1=$pos1+4;
	 
		 $pos2=stripos($jietu,"\"",$pos1);   
		 $pos3=stripos($jietu,"\"",$pos2+1);  
		 $month=substr($jietu,$pos2+1,$pos3-$pos2-1);
	 
		 
		 $pos4=stripos($jietu,"dd",$pos3);
		 $pos4=$pos4+4;
		 
		 $pos5=stripos($jietu,"\"",$pos4);   
		 $pos6=stripos($jietu,"\"",$pos5+1);  
		 $day=substr($jietu,$pos5+1,$pos6-$pos5-1);
		 $year="2015";
		 $date=$year."-".$month."-".$day." "."24:00:00";
		 $zero1=strtotime (date("Y-m-d H:i:s")); 
		 $zero2=strtotime ($date );
		   
		 $guonian=ceil(($zero2-$zero1)/86400); //60s*60min*24h
		 $diff=(int)$guonian;
         if (($diff<=30) and ($diff>0)){  
		        $sql2 = "SELECT * FROM wp_users where ID='".$user_id."'";
		        $result2 = $conn->query($sql2);
		        if ($result2->num_rows > 0) {
					while($row2 = $result2->fetch_assoc()) {
						$user_name=$row2["user_login"];
					}
					
				}
				$sql3= "SELECT * FROM wp_usermeta where user_id='".$user_id."' and meta_key='pie_text_3'";
		        $result3 = $conn->query($sql3);
		        if ($result3->num_rows > 0) {
					while($row3 = $result3->fetch_assoc()) {
						$wechat=$row3["meta_value"];
					}
					
				}
				
				
				
		 echo "<tr><td>".$user_name."</td><td>".$wechat."</td><td>".$date."</td></tr>";
		 
		 }
		 
    }
} 

echo "</table>";
$conn->close();

?>