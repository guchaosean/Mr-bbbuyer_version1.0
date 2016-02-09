<?php
/**
 * Single Product Price, including microdata for SEO
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

?>
<div itemprop="offers" itemscope itemtype="http://schema.org/Offer">
 <table id="table_1" style="width:100%;  ">
  <tr>
    <td><?php 
	 
	$is_vip_member=false;
	 
    require_once( ABSPATH . 'wp-includes/pluggable.php' );
	
    if ( $group = Groups_Group::read_by_name( 'Registered' ) ) {
          $is_vip_member = Groups_User_Group::read( get_current_user_id() , $group->group_id );
		  $is_vip_member = !$is_vip_member ;
    }	
	if (get_current_user_id()==null){$is_vip_member=false;}
	 
	
	if  ($is_vip_member==false){ echo '<p class="price">原价 ：' .$product->get_price_html().'</p>'; }
	else {echo  "<span style='TEXT-DECORATION: line-through; font-size:21px; color:black;'>原价：$".get_post_meta( $product->id, 'original_price', true)."</span>" ;}
 
	?></td>
    <td> <span style="font-size:21px; "><?php echo "                "." 总销售量："." ". get_post_meta( $product->id, 'total_sales', true );?></span></td> 
     
  </tr>
  <tr>
    <td> <?php   $productxx=new WC_Product_Variable($product->id);
	           $total_st=0;
			   foreach ( $productxx->get_children() as $child_id ) {
                    $stockf = get_post_meta( $child_id, '_stock', true );

                     if ( $stockf != '' ) {
                          $total_st=$total_st+$stockf;
                     }
               }
	
	
	
	?> <?php
	
	if ($is_vip_member==false){      }
	else {echo  '<p class="price">VIP价：'.$product->get_price_html().'</p>' ;}
	
	
	
	
	?></td>
    <td> <?php   echo "<p style='font-size:21px; color:black;'>总库存：". $total_st."</p>"; ?></td> 
      
  </tr>
</table>
	

	<meta itemprop="price" content="<?php echo $product->get_price(); ?>" />
	<meta itemprop="priceCurrency" content="<?php echo get_woocommerce_currency(); ?>" />
	<link itemprop="availability" href="http://schema.org/<?php echo $product->is_in_stock() ? 'InStock' : 'OutOfStock'; ?>" />
  
</div>
