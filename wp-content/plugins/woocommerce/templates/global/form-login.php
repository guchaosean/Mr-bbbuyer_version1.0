<?php
/**
 * Login form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( is_user_logged_in() ) {
	return;
}

?>
<form style="width:300px;"method="post" class="login" <?php if ( $hidden ) echo 'style="display:none;"'; ?>>

	<?php do_action( 'woocommerce_login_form_start' ); ?>

	<?php if ( $message ) echo wpautop( wptexturize( $message ) ); ?>

	<p class="form-row form-row-first" style="width:300px; display:flex;">
		<label style="width:100px;" for="username"><?php _e( '用户名', 'woocommerce' ); ?> <span class="required">*</span></label>
		<input style="width:200px;" type="text" class="input-text" name="username" id="username" />
	</p>
	<p class="form-row form-row-last" style="width:300px; display:flex;">
		<label style="width:100px;" for="password"><?php _e( 'Password', 'woocommerce' ); ?> <span class="required">*</span></label>
		<input style="width:200px;" class="input-text" type="password" name="password" id="password" />
	</p>
	<div class="clear"></div>

	<?php do_action( 'woocommerce_login_form' ); ?>

	<p class="form-row" style="width:300px; display:flex;">
		<?php wp_nonce_field( 'woocommerce-login' ); ?>
		<label style="width:100px; left:10px; position:relative;" for="rememberme" class="inline">
			<input  style="position: relative;left: -10px;" name="rememberme" type="checkbox" id="rememberme" value="forever" /> <?php _e( 'Remember me', 'woocommerce' ); ?>
		</label>
		
		<input   type="hidden" name="redirect" value="<?php echo esc_url( $redirect ) ?>" />
		<!--<p style="width:100px;" class="lost_password">-->
		<a style="width:200px; float:right;" href="<?php echo esc_url( wc_lostpassword_url() ); ?>"><?php _e( 'Lost your password?', 'woocommerce' ); ?></a>
	    <!--</p>-->
	</p>
	<style>
	@media only screen and (min-width: 768px){
		#rememberme{
			 top: 5px;
             position: relative;
	    }	
	}
	
	
	</style>
	<input style="background-color: #777777;
    background-image: -webkit-gradient(linear, left top, left bottom, from(#777777), to(#444444));
	width:300px;
    background-image: -webkit-linear-gradient(top, #777777, #444444);
    background-image: -moz-linear-gradient(top, #777777, #444444);
    background-image: -ms-linear-gradient(top, #777777, #444444);
    background-image: -o-linear-gradient(top, #777777, #444444);
    background-image: linear-gradient(to bottom, #777777, #444444);
    text-shadow: 0 1px 0 #333333;
    border: 1px solid #222222;
    -webkit-box-shadow: 0 0 0 0.327em rgba(0, 0, 0, 0.075), 0 1px 2px rgba(0, 0, 0, 0.2), inset 0 1px #999999, inset 0 -1px #333333;
    box-shadow: 0 0 0 0.327em rgba(0, 0, 0, 0.075), 0 1px 2px rgba(0, 0, 0, 0.2), inset 0 1px #999999, inset 0 -1px #333333;" type="submit" class="button" name="login" value="<?php _e( 'Login', 'woocommerce' ); ?>" />

	<div class="clear"></div>

	<?php do_action( 'woocommerce_login_form_end' ); ?>

</form>
