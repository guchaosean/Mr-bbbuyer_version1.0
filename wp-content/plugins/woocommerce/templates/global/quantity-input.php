<?php
/**
 * Product quantity inputs
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<div class="quantity"><input id= "quantity" type="number" step="<?php echo esc_attr( $step ); ?>" <?php if ( is_numeric( $min_value ) ) : ?>min="<?php echo esc_attr( $min_value ); ?>"<?php endif; ?> <?php if ( is_numeric( $max_value ) ) : ?>max="<?php echo esc_attr( $max_value ); ?>"<?php endif; ?> name="<?php echo esc_attr( $input_name ); ?>" value="<?php echo esc_attr( $input_value ); ?>" title="<?php _ex( 'Qty', 'Product quantity input tooltip', 'woocommerce' ) ?>" class="input-text qty text" size="4" required /></div>
<script>
$(document).ready(function(){
	var sss=$( "#quantity" ).val();
	 
  $( "#quantity" ).keyup(function() {      
	   if (document.getElementById("quantity").validity.rangeOverflow) {
	         document.getElementById("quantity").setCustomValidity("！您所填写的数量超过库存");
			 document.getElementById("quantity").title="";
			 $( "#help_message_1" ).show();
			  document.getElementById("quantity").value=sss;
			 return;
		 }
		 else if (document.getElementById("quantity").validity.valueMissing){
		     document.getElementById("quantity").setCustomValidity("！请填写数量");
		 document.getElementById("quantity").value=sss;
		 $( "#help_message_1" ).hide();
		     return;
		 }
		 else if (document.getElementById("quantity").validity.rangeUnderflow	)
		 {
		     document.getElementById("quantity").setCustomValidity("！数量必须大于零");  
          document.getElementById("quantity").value=sss;	
$( "#help_message_1" ).hide();		  
			 return;
		 }
		 else{
		     document.getElementById("quantity").setCustomValidity("");
			 $( "#help_message_1" ).hide();
		 }
    });
});
</script>