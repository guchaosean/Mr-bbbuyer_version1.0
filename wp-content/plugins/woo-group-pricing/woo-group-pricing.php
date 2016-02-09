<?php
/**
 * woo-group-pricing.php
 *
 * Copyright (c) 2011,2012 Antonio Blanco http://www.blancoleon.com
 *
 * This code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This header and all notices must be kept intact.
 *
 * @author Antonio Blanco	
 * @package woogrouppricing
 * @since woogrouppricing 1.0.0
 *
 * Plugin Name: Woocommerce Group Pricing
 * Plugin URI: http://www.eggemplo.com/plugins/woocommerce-group-pricing
 * Description: Shows different prices according to the user's group
 * Version: 2.4.1
 * Author: eggemplo
 * Author URI: http://www.eggemplo.com
 * License: GPLv3
 */

define( 'WOO_GROUP_PRICING_DOMAIN', 'woogrouppricing' );
define( 'WOO_GROUP_PRICING_PLUGIN_NAME', 'woo-group-pricing' );

define( 'WOO_GROUP_PRICING_FILE', __FILE__ );

if ( !defined( 'WOO_GROUP_PRICING_CORE_DIR' ) ) {
	define( 'WOO_GROUP_PRICING_CORE_DIR', WP_PLUGIN_DIR . '/woo-group-pricing/core' );
}

define ( 'WOO_GROUP_PRICING_DECIMALS', apply_filters( 'woo_group_pricing_num_decimals', 2 ) );

class WooGroupPricing_Plugin {
	
	private static $notices = array();
	
	public static function init() {
			
		load_plugin_textdomain( WOO_GROUP_PRICING_DOMAIN, null, WOO_GROUP_PRICING_PLUGIN_NAME . '/languages' );
		
		register_activation_hook( WOO_GROUP_PRICING_FILE, array( __CLASS__, 'activate' ) );
		register_deactivation_hook( WOO_GROUP_PRICING_FILE, array( __CLASS__, 'deactivate' ) );
		
		register_uninstall_hook( WOO_GROUP_PRICING_FILE, array( __CLASS__, 'uninstall' ) );
		
		add_action( 'init', array( __CLASS__, 'wp_init' ) );
		add_action( 'admin_notices', array( __CLASS__, 'admin_notices' ) );
		
		
	}
	
	public static function wp_init() {
		
		if ( is_multisite() ) {
			$active_sitewide_plugins = array_keys( get_site_option( 'active_sitewide_plugins', array() ) );
			$active_plugins = array_merge( get_option( 'active_plugins', array() ), $active_sitewide_plugins );
		} else {
			$active_plugins = get_option( 'active_plugins', array() );
		}
		$groups_is_active = in_array( 'groups/groups.php', $active_plugins );
		$woo_is_active = in_array( 'woocommerce/woocommerce.php', $active_plugins );
		
		if ( ( !$groups_is_active ) || ( !$woo_is_active ) ) {
			if ( !$groups_is_active ) {
				self::$notices[] = "<div class='error'>" . __( 'The <strong>Woocommerce Group Pricing</strong> plugin requires the <a href="http://wordpress.org/extend/plugins/groups" target="_blank">Groups</a> plugin to be activated.', WOO_GROUP_PRICING_DOMAIN ) . "</div>";
			} 
			if ( !$woo_is_active ) {
				self::$notices[] = "<div class='error'>" . __( 'The <strong>Woocommerce Group Pricing</strong> plugin requires the <a href="http://wordpress.org/extend/plugins/woocommerce" target="_blank">Woocommerce</a> plugin to be activated.', WOO_GROUP_PRICING_DOMAIN ) . "</div>";
			} 
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			deactivate_plugins( array( __FILE__ ) );
		} else {
				
			add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ), 40 );
				
			//call register settings function
			add_action( 'admin_init', array( __CLASS__, 'register_woogrouppricing_settings' ) );
			
			if ( !class_exists( "WooGroupPricing" ) ) {
				include_once 'core/class-woogrouppricing.php';
				include_once 'core/class-wgp-categories-admin.php';
				include_once 'core/class-wgp-variations-admin.php';
				include_once 'core/class-wgp-grouped-admin.php';
				include_once 'core/class-wgp-shortcodes.php';
			}

		}
		
	}
	
	
	/**
	 * Register settings as groups-mailchimp-settings
	 */
	public static function register_woogrouppricing_settings() {
		register_setting( 'woogrouppricing', 'wgp-method' );
		add_option( 'wgp-method','rate' ); // by default rate
		
		register_setting( 'woogrouppricing', 'wgp-baseprice' );
		add_option( 'wgp-baseprice','regular' ); // by default regular
		
	}
	
	public static function admin_notices() { 
		if ( !empty( self::$notices ) ) {
			foreach ( self::$notices as $notice ) {
				echo $notice;
			}
		}
	}
	
	/**
	 * Adds the admin section.
	 */
	public static function admin_menu() {
		$admin_page = add_submenu_page(
				'woocommerce',
				__( 'Group Pricing' ),
				__( 'Group Pricing' ),
				'manage_options',
				'woogrouppricing',
				array( __CLASS__, 'woogrouppricing_settings' )
		);
		
	}
	
	/**
	 * Show Groups MailChimp setting page.
	 */
	public static function woogrouppricing_settings () {
	?>
	<div class="wrap">
	<h2><?php echo __( 'Woocommerce Group Pricing', WOO_GROUP_PRICING_DOMAIN ); ?></h2>
	<?php 
	$alert = "";
	$groups = WooGroupPricing::get_all_groups();
	
	if ( isset( $_POST['submit'] ) ) {
		$alert = __("Saved", WOO_GROUP_PRICING_DOMAIN);
		
		add_option( "wgp-method",$_POST[ "wgp-method" ] );
		update_option( "wgp-method", $_POST[ "wgp-method" ] );
		
		add_option( "wgp-baseprice",$_POST[ "wgp-baseprice" ] );
		update_option( "wgp-baseprice", $_POST[ "wgp-baseprice" ] );
		
		foreach ( $groups as $group ) {
			if ( isset( $_POST[ "wgp-" . $group->group_id ] ) && ( $_POST[ "wgp-" . $group->group_id ] !== "" ) ) {
				add_option( "wgp-" . $group->group_id,$_POST[ "wgp-" . $group->group_id ] );
				update_option( "wgp-" . $group->group_id, $_POST[ "wgp-" . $group->group_id ] );
			} else {
				delete_option( "wgp-" . $group->group_id );
			}
		}
	}
	
	if ($alert != "")
		echo '<div style="background-color: #ffffe0;border: 1px solid #993;padding: 1em;margin-right: 1em;">' . $alert . '</div>';
	
	?>
	<div class="wrap" style="border: 1px solid #ccc; padding:10px;">
	<form method="post" action="">
	    <table class="form-table">
	        <tr valign="top">
	        <th scope="row"><strong><?php echo __( 'Products discount method:', WOO_GROUP_PRICING_DOMAIN ); ?></strong></th>
	        <td>
	        	<select name="wgp-method">
	        	<?php 
	        	if ( get_option("wgp-method") == "amount" ) {
	        	?>
	        		<option value="rate">Rate</option>
	        		<option value="amount" selected="selected">Amount</option>
	        	<?php 
	        	} else {
	        	?>
	        		<option value="rate" selected="selected">Rate</option>
	        		<option value="amount">Amount</option>
	        	<?php 
	        	}
	        	?>
	        	</select>
	        </tr>
	        
	        <tr valign="top">
	        <th scope="row"><strong><?php echo __( 'Apply to:', WOO_GROUP_PRICING_DOMAIN ); ?></strong></th>
	        <td>
	        	<select name="wgp-baseprice">
	        	<?php 
	        	if ( get_option("wgp-baseprice") == "sale" ) {
	        	?>
	        		<option value="regular">Regular price</option>
	        		<option value="sale" selected="selected">Sale price</option>
	        	<?php 
	        	} else {
	        	?>
	        		<option value="regular" selected="selected">Regular price</option>
	        		<option value="sale">Sale price</option>
	        	<?php 
	        	}
	        	?>
	        	</select>
	        </tr>
	    </table>
	    <h3><?php echo __( 'Groups:', WOO_GROUP_PRICING_DOMAIN ); ?></h3>
	    <div class="description">Leave empty if no group discount should be applied (default setting).<br>
	    Example with rate method: Indicate 0.1 for 10% discounts on every product.
	    </div>
		
		<table class="form-table">
	      <?php 
			if ( $groups ) {
	        	foreach ( $groups as $group ) {
			        ?>
			        <tr valign="top">
			        <th scope="row"><?php echo $group->name . ':'; ?></th>
			        <td>
			        	<input type="text" name="wgp-<?php echo $group->group_id;?>" value="<?php echo get_option( "wgp-" . $group->group_id ); ?>" />
			        </td>
			        </tr>
			        <?php 
				}
			}
	        ?>
	    </table>
	    
	    <?php submit_button( __( "Save", WOO_GROUP_PRICING_DOMAIN ) ); ?>
	    
	    <?php settings_fields( 'woogrouppricing' ); ?>
	    
	</form>
	
	</div>
	</div>
	<?php 
	}
	
	
	/**
	 * Plugin activation work.
	 * 
	 */
	public static function activate() {
	
	}
	
	/**
	 * Plugin deactivation.
	 *
	 */
	public static function deactivate() {
	
	}

	/**
	 * Plugin uninstall. Delete database table.
	 *
	 */
	public static function uninstall() {
	
	}
	
}
WooGroupPricing_Plugin::init();

