<?php
/**
 * My Orders
 *
 * Shows recent orders on the account page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$customer_orders = get_posts( apply_filters( 'woocommerce_my_account_my_orders_query', array(
	
	//'numberposts' => $order_count,
	'numberposts' => 200,
	'meta_key'    => '_customer_user',
	'meta_value'  => get_current_user_id(),
	'post_type'   => wc_get_order_types( 'view-orders' ),
	'post_status' => array_keys( wc_get_order_statuses() )
) ) );

if ( $customer_orders ) : ?>

	<h2><?php echo apply_filters( 'woocommerce_my_account_my_orders_title', __( 'Recent Orders', 'woocommerce' ) ); ?></h2>
 <form method="get" action="https://www.mr-bbbuyer.com/my-account/">
	<p>选择查看订单日期：</p>
	<?php if (( isset($_GET["start_date"]) and isset($_GET["end_date"]))and($_GET["start_date"]!="")and($_GET["end_date"]!="")){
		
		echo "<input name='start_date' type='date' value='".$_GET["start_date"]."'>"."&nbsp;&nbsp;&nbsp;&nbsp; ". "到"." &nbsp;&nbsp;&nbsp;&nbsp; " ."<input name='end_date' type='date' value='".$_GET["end_date"]."'>  ";
	}
	else {
		echo "<input name='start_date' type='date' value=''>" ." &nbsp;&nbsp;&nbsp;&nbsp; ". "到"." &nbsp;&nbsp;&nbsp;&nbsp; " ."<input name='end_date' type='date' value=''>  ";
		
	}
	?>
	 
    <input type="submit" value="搜索" style="background-color: #777777;
    background-image: -webkit-gradient(linear, left top, left bottom, from(#777777), to(#444444));
	left:50px;position:relative;
    background-image: -webkit-linear-gradient(top, #777777, #444444);
    background-image: -moz-linear-gradient(top, #777777, #444444);
    background-image: -ms-linear-gradient(top, #777777, #444444);
    background-image: -o-linear-gradient(top, #777777, #444444);
    background-image: linear-gradient(to bottom, #777777, #444444);
    text-shadow: 0 1px 0 #333333;
    border: 1px solid #222222;
    -webkit-box-shadow: 0 0 0 0.327em rgba(0, 0, 0, 0.075), 0 1px 2px rgba(0, 0, 0, 0.2), inset 0 1px #999999, inset 0 -1px #333333;
    box-shadow: 0 0 0 0.327em rgba(0, 0, 0, 0.075), 0 1px 2px rgba(0, 0, 0, 0.2), inset 0 1px #999999, inset 0 -1px #333333;">
	<input type="hidden" value="8" name="page_id">
	</form>
	
	<?php 
	$ordernumber=0;
	   foreach ( $customer_orders as $customer_order ) {
		$ordernumber=$ordernumber+1;		
	   }
	$pagenumber= ceil($ordernumber/10);
	
	$current_page_number=$_GET["pagex"];
			if (!isset($current_page_number)){$current_page_number=1;}	  
	 
	?>
	<table class="shop_table shop_table_responsive my_account_orders">

		<thead>
			<tr>
				<th class="order-number"><span class="nobr"><?php _e( 'Order', 'woocommerce' ); ?></span></th>
				<th class="order-date"><span class="nobr"><?php _e( 'Date', 'woocommerce' ); ?></span></th>
				<th class="order-status"><span class="nobr"><?php _e( 'Status', 'woocommerce' ); ?></span></th>
				<th class="order-total"><span class="nobr"><?php _e( 'Total', 'woocommerce' ); ?></span></th>
				<th class="order-actions">&nbsp;</th>
			</tr>
		</thead>

		<tbody><?php
		 $array_num=-1;
		   $new_customer_orders= array();
		       if (( isset($_GET["start_date"]) and isset($_GET["end_date"]))and($_GET["start_date"]!="")and($_GET["end_date"]!="")){
			   
			   foreach ( $customer_orders as $customer_order ) {
				  $order = wc_get_order( $customer_order ); 
     		 	  $order->populate( $customer_order );
				  $item_count = $order->get_item_count();
				  $date_temp=date( 'Y-m-d', strtotime( $order->order_date ));
				  if (($date_temp<=$_GET["end_date"]) and ($date_temp>=$_GET["start_date"])){
					   $array_num=$array_num+1;
					   $new_customer_orders[$array_num]=$customer_order;
				  }
			   }
			   $customer_orders=$new_customer_orders;
			   $ordernumber=$array_num+1;
			   $pagenumber= ceil($ordernumber/10);
		   }
		
		
		    $the_start=($current_page_number-1)*10;
		    if ($current_page_number*10-1>($ordernumber-1)) {$the_end= $ordernumber-1;}
			else {
                 if (current_page_number!=1){
     				 $the_end=$current_page_number*10-1;
				 }
				 else{
					 $the_end=$current_page_number;
				 }
				}
			
			for ($d=$the_start; $d<=$the_end; $d++){
			//foreach ( $customer_orders as $customer_order ) {
				$order      = wc_get_order();
				//$order->populate( $customer_order );
				$order->populate( $customer_orders[$d] );
				
				$item_count = $order->get_item_count();

				?><tr class="order">
					<td class="order-number" data-title="<?php _e( 'Order Number', 'woocommerce' ); ?>">
						<a href="<?php echo $order->get_view_order_url(); ?>">
							#<?php echo $order->get_order_number(); ?>
						</a>
					</td>
					<td class="order-date" data-title="<?php _e( 'Date', 'woocommerce' ); ?>">
						<time datetime="<?php echo date( 'Y-m-d', strtotime( $order->order_date ) ); ?>" title="<?php echo esc_attr( strtotime( $order->order_date ) ); ?>"><?php echo date_i18n( get_option( 'date_format' ), strtotime( $order->order_date ) ); ?></time>
					</td>
					<td class="order-status" data-title="<?php _e( 'Status', 'woocommerce' ); ?>" style="text-align:left; white-space:nowrap;">
						<?php echo wc_get_order_status_name( $order->get_status() ); ?>
					</td>
					<td class="order-total" data-title="<?php _e( 'Total', 'woocommerce' ); ?>">
						<?php //echo sprintf( _n( '%s for %s item', '%s for %s items', $item_count, 'woocommerce' ), $order->get_formatted_order_total(), $item_count ); ?>
					    <?php echo sprintf( _n( '%s - %s 物品', '%s - %s 物品', $item_count, 'woocommerce' ), $order->get_formatted_order_total(), $item_count ); ?>
					</td>
					<td class="order-actions">
						<?php
							$actions = array();

							if ( in_array( $order->get_status(), apply_filters( 'woocommerce_valid_order_statuses_for_payment', array( 'pending', 'failed' ), $order ) ) ) {
								$actions['pay'] = array(
									'url'  => $order->get_checkout_payment_url(),
									'name' => __( 'Pay', 'woocommerce' )
								);
							}

							if ( in_array( $order->get_status(), apply_filters( 'woocommerce_valid_order_statuses_for_cancel', array( 'pending', 'failed' ), $order ) ) ) {
								$actions['cancel'] = array(
									'url'  => $order->get_cancel_order_url( wc_get_page_permalink( 'myaccount' ) ),
									'name' => __( 'Cancel', 'woocommerce' )
								);
							}

							$actions['view'] = array(
								'url'  => $order->get_view_order_url(),
								'name' => __( 'View', 'woocommerce' )
							);

							$actions = apply_filters( 'woocommerce_my_account_my_orders_actions', $actions, $order );

							if ($actions) {
								foreach ( $actions as $key => $action ) {
									echo '<a href="' . esc_url( $action['url'] ) . '" class="button ' . sanitize_html_class( $key ) . '">' . esc_html( $action['name'] ) . '</a>';
								}
							}
						?>
					</td>
				</tr><?php
			}
		?></tbody>

	</table>
<div style="TEXT-ALIGN: center;">
	   <?php 
	 
	   
	 
 
	   for($x=1; $x<=$pagenumber; $x++){
	        if ($x==$current_page_number){
			  echo "<a href='https://www.mr-bbbuyer.com/my-account/?&pagex=".$x."&start_date=".$_GET["start_date"]."&end_date=".$_GET["end_date"]."' style='color:red;'>".$x.'    '."</a>";	
			}else{
		    echo "<a href='https://www.mr-bbbuyer.com/my-account/?&pagex=".$x."&start_date=".$_GET["start_date"]."&end_date=".$_GET["end_date"]."'>".$x.'    '."</a>";
			}
	   }?>
	
	
	</div>
<?php endif; ?>
