<?php
             $customername="guchaosean";
			 $servername = "localhost";
             $username = "mrbbbuye_sean";
             $password = "19900825";
             $dbname = "mrbbbuye_wordpress";
			 $price="50";
		     $conn = new mysqli($servername, $username, $password, $dbname);
     
             if ($conn->connect_error) {
                   die("Connection failed: " . $conn->connect_error);
             } 
			 $sql = "SELECT * FROM wp_users where user_login='".$customername."'";
			 
             $result = $conn->query($sql);
		     if ($result->num_rows > 0) {
     
             while($row = $result->fetch_assoc()) {
				 $customerid=$row["ID"];				 
			 }
			 }
			 else{
				 echo "error";
			 }
		 
             $sql1 = "SELECT * FROM wp_usermeta where user_id='".$customerid."' and meta_key='description'";	 
             $result1 = $conn->query($sql1);
		     if ($result1->num_rows > 0) {
    // output data of each row
                  while($row = $result1->fetch_assoc()) {
                        $customerlevel=$row["meta_value"];
                  }
             }
			 $pos=stripos($customerlevel,"user-level:");
			 $customerlevel=substr($customerlevel,11);
			 echo $customerlevel;
			 if ($customerlevel=="1"){$price=$price*98/100;}
			 echo $price;
			 
			 $conn->close();
?>