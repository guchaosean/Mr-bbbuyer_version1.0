<?php
/**
 * Output a single payment method
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<script>

$(function() {
		$( "#select_pay_way" ).on('change', function() {
             var vall=$( "#select_pay_way" ).val();
		 
			 if (vall=='au_pay'){$( "#au_pay_des" ).show(); $( "#zhifubao_pay_des" ).hide();$( "#china_pay_des" ).hide();}
			 if (vall=='zhifubao'){$( "#au_pay_des" ).hide(); $( "#zhifubao_pay_des" ).show();$( "#china_pay_des" ).hide();}
			 if (vall=='china_pay'){$( "#au_pay_des" ).hide(); $( "#zhifubao_pay_des" ).hide();$( "#china_pay_des" ).show();}
        });   
    });
</script>
 
 
 
<li class="payment_method_<?php echo $gateway->id; ?>">
	<input id="payment_method_<?php echo $gateway->id; ?>" type="radio" class="input-radio" name="payment_method" value="<?php echo esc_attr( $gateway->id ); ?>" <?php checked( $gateway->chosen, true ); ?> data-order_button_text="<?php echo esc_attr( $gateway->order_button_text ); ?>" />

	<label for="payment_method_<?php echo $gateway->id; ?>">
		<?php echo $gateway->get_title(); ?> <?php echo $gateway->get_icon(); ?>
	</label>
	<?php if ( $gateway->has_fields() || $gateway->get_description() ) : ?>
		<div class="payment_box payment_method_<?php echo $gateway->id; ?>" <?php if ( ! $gateway->chosen ) : ?>style="display:none;"<?php endif; ?>>
			<?php $gateway->payment_fields(); ?>
		</div>
	<?php endif; ?>
</li>
