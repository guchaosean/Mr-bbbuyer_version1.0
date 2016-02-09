<?php
/**
 * Cart item data (when outputting non-flat)
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version 	2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<dl class="variation" style="    top: 30px;position: relative;">
	<?php
	    $count=0;
		foreach ( $item_data as $data ) :
	        $count=$count+1;	
     		$key = sanitize_text_field( $data['key'] );
	     if ($count==1){
	?>
		<dt class="variation-<?php echo sanitize_html_class( $key ); ?>"><?php echo wp_kses_post( $data['key'] ); ?>:</dt>
		<dd class="variation-<?php echo sanitize_html_class( $key ); ?>"><?php echo wp_kses_post( wpautop( $data['value'] ) ); ?></dd>
	
	
	<?php 
		 }
	    else {
			?>
		<dt style="    top: -20px; position: relative;"class="variation-<?php echo sanitize_html_class( $key ); ?>"><?php echo wp_kses_post( $data['key'] ); ?>:</dt>
		<dd style="    top: -20px; position: relative;"class="variation-<?php echo sanitize_html_class( $key ); ?>"><?php echo wp_kses_post( wpautop( $data['value'] ) ); ?></dd>
			
	<?	}
	
	
	endforeach; ?>
</dl>
