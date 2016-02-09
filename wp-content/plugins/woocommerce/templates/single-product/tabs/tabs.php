<?php
/**
 * Single Product tabs
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Filter tabs and allow third parties to add their own
 *
 * Each tab is an array containing title, callback and priority.
 * @see woocommerce_default_product_tabs()
 */
$tabs = apply_filters( 'woocommerce_product_tabs', array() );

if ( ! empty( $tabs ) ) : ?>
    <div id="wrapper_tabs_sider"  >
	<div id="sidermenu" style="float:left; width:15%; position:relative;  font-size:15px; top: 103px;">
	 <ul style=" list-style:none">
	 <li>
        <td><a href="https://www.mr-bbbuyer.com/%E5%95%86%E5%93%81%E5%88%86%E7%B1%BB/%E6%9C%8D%E9%A5%B0/"  >服饰</a></td>
     </li>
	<li>
        <td><a href="https://www.mr-bbbuyer.com/%E5%95%86%E5%93%81%E5%88%86%E7%B1%BB/%E5%8C%85%E5%8C%85/"  > 包包</a></td>
     </li>
	 <li>
        <td><a href="https://www.mr-bbbuyer.com/%E5%95%86%E5%93%81%E5%88%86%E7%B1%BB/%E9%85%8D%E4%BB%B6/"  >配件</a></td>
     </li>
	  
	</ul>
	
	
	</div>
	<div class="woocommerce-tabs" style="float:right; width:80%; position: relative; top: -36px;">
		<ul class="tabs">
			<?php foreach ( $tabs as $key => $tab ) : ?>

				<li class="<?php echo $key ?>_tab">
					<a href="#tab-<?php echo $key ?>"><?php echo apply_filters( 'woocommerce_product_' . $key . '_tab_title', $tab['title'], $key ) ?></a>
				</li>

			<?php endforeach; ?>
			<li class="salerecord_tab">	 
				<a href="#tab-salerecord">销售记录</a>
			</li>
		</ul>
		<?php foreach ( $tabs as $key => $tab ) : ?>

			<div class="panel entry-content" id="tab-<?php echo $key ?>">
				<?php call_user_func( $tab['callback'], $key, $tab ) ?>
			</div>

		<?php endforeach; ?>
		 <div class="panel entry-content" id="tab-salerecord" style="display: none;">
				
	         <h2>销售记录</h2>
           <div id="test">
            <table class="sale_record">
            <tbody>
			 <tr>
               <th>用户名</th>
               <th>购买数量</th> 
               <th>购买时间</th>
             </tr>
			 <?php 
			        $output="";
					$data_x=array();
					$data_number=0;
					$orderid_array=array();
					$order_num=0;
			        $servername = "localhost";
                    $username = "mrbbbuye_sean";
                    $password = "19900825";
                    $dbname = "mrbbbuye_wordpress";
                    $conn = new mysqli($servername, $username, $password, $dbname);
                    if ($conn->connect_error) {
                          die("Connection failed: " . $conn->connect_error);
                     } 
                    $sql = "SELECT * FROM wp_woocommerce_order_items ";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
						      if (!(in_array($row["order_id"], $orderid_array))){
                              $orderid_array[$order_num]=$row["order_id"] ;
							  $order_num=$order_num+1;
							  }
                      }
                    }  
                   
			       global $product;
                   $productid = $product->id;
				   
			       if ($order_num>=149){$order_num=149;}
			  
			       for ($x = $order_num; $x>=0 ; $x--) {
                        $order_id=$orderid_array[$x];
                        $order = new WC_Order( $order_id );
                        $items = $order->get_items(); 
                        foreach ( $items as $item ) {
                               $product_id = $item['product_id'];
							   if ($product_id == $productid){
								   $user_id=$order->get_user_id();
								   $sql2 = "SELECT * FROM wp_users where ID='".$user_id."'";
								   $result = $conn->query($sql2);
                                   if ($result->num_rows > 0) {
                                        while($row = $result->fetch_assoc()) {
						                    $user_name=$row["user_login"]; 
											$len=strlen($user_name );
											$user_replace=substr($user_name,0,2);
											for ($i = 0; $i <=$len-3; $i++){ $user_replace=$user_replace."*" ;    }
							            }
                                    }
									 $date=$order->order_date ;
								   
								   
								   $quality=$item['qty'];
								    $data_number=$data_number+1;
								   $data_x[$data_number]=array("name"=>$user_replace,"quality"=>$quality,"data"=>$date);
								   if ($data_number<=15){
								   $output =$output."<tr><th>".$user_replace."</th><th>".$quality."</th><th>".$date."</th>"."</tr>" ;
								   }
                                }  
								   
								  
								   
							   }
                        }
						
                   
			 
			 
			 
			  $conn->close();
			 
			    echo $output;	
			 ?>
	    
            </tbody></table>
			</div>
			<script>
			  function pagechange(page_index){
				    var data_field=<?php echo json_encode($data_x);?>;
					var start_index=(page_index-1)*15+1;
					var end_index=(page_index)*15;
					if (end_index>=<?php echo $data_number;?>){
						end_index=<?php echo $data_number;?>;
					}
					var output="<table class='sale_record'><tbody><tr><th>用户名</th><th>购买数量</th> <th>购买时间</th></tr>";
					for (i=start_index;i<=end_index;i++){
						output=output+"<tr><th>"+data_field[i]['name']+"</th><th>"+data_field[i]['quality']+"</th><th>"+data_field[i]['data']+"</th>"+"</tr>";
					}
					output=output+" </tbody></table>";
					
					document.getElementById("test").innerHTML=output;
			  }
			</script>
			<div style="TEXT-ALIGN: center;">
	  <?php 
	    $totalpagenumber=ceil($data_number/15);
		for ($x=1; $x<=$totalpagenumber; $x++){
			echo "<a href='javascript:pagechange(".$x.")'>".$x."     "."</a>";
			 
		}
		?>
			
			
			
			
			</div>
			
	</div>
</div>
<?php endif; ?>
