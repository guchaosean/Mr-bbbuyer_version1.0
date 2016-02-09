<?php
/**
 * class-wgp-variations-admin.php
 *
 * Copyright (c) "eggemplo" Antonio Blanco www.eggemplo.com
 *
 * This code is provided subject to the license granted.
 * Unauthorized use and distribution is prohibited.
 * See COPYRIGHT.txt and LICENSE.txt
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * This header and all notices must be kept intact.
 * 
 * @author Antonio Blanco
 * @package woo-group-pricing
 * @since 2.3
 */
if (! defined ( 'ABSPATH' )) {
	exit ();
}

/**
 * Category product admin handlers.
 */
class WGP_Variations_Admin {
	
	/**
	 * Sets up the init action.
	 */
	public static function init() {
		
		// Display Fields
		add_action ( 'woocommerce_product_after_variable_attributes', array (
				__CLASS__,
				'woocommerce_product_after_variable_attributes' 
		), 10, 2 );
		// JS to add fields for new variations
		//add_action ( 'woocommerce_product_after_variable_attributes_js', array (
		//		__CLASS__,
		//		'woocommerce_product_after_variable_attributes_js' 
		//) );
		// Save variation fields
		add_action ( 'woocommerce_process_product_meta_variable', array (
				__CLASS__,
				'woocommerce_process_product_meta_variable' 
		), 10, 1 );
		
		// filter <del> tags for variable products
		add_filter('woocommerce_variable_sale_price_html', array ( __CLASS__, 'woocommerce_variable_sale_price_html' ), 10, 2 );
		add_filter('woocommerce_variable_sale_price', array ( __CLASS__, 'woocommerce_variable_sale_price_html' ), 10, 2 );
		
		// test variations
		add_filter( 'woocommerce_get_variation_price', array ( __CLASS__, 'woocommerce_get_variation_price' ), 10, 4);
		
	}
	
	public static function woocommerce_product_after_variable_attributes($loop, $variation_data) {
		?>
		<tr>
			<td style="background:none repeat scroll 0 0 #fafafa;">
				<div>
					<h3>Groups Pricing</h3>
					<?php self::woocommerce_product_variations_write_panels($loop, $variation_data); ?>
				</div>
			</td>
		</tr>
		<?php
	}
	
	public static function woocommerce_process_product_meta_variable($post_id) {
		if (isset ( $_POST ['variable_sku'] )) {
			$variable_sku = $_POST ['variable_sku'];
			$variable_post_id = $_POST ['variable_post_id'];
			$groups = WooGroupPricing::get_all_groups();
			
			for( $i = 0; $i < sizeof ( $variable_sku ); $i ++ ) {
				$variation_id = ( int ) $variable_post_id [$i];
				
				if ( $groups ) {
					foreach ( $groups as $group ) {
						if ( isset ( $_POST['groups_pricing_value_' . $group->group_id . "_" . $i] ) ) {
							$variable_custom_field = $_POST['groups_pricing_value_' . $group->group_id . "_" . $i];
							update_post_meta( $variation_id, 'groups_pricing_value_' . $group->group_id, ( $variable_custom_field !== "" ) ? trim($variable_custom_field) : '' );
						} 
					}
				}
			}
		}
	}
	
	
	public static function woocommerce_product_variations_write_panels($loop, $variation_data) {
		global $post;
	
		$groups = WooGroupPricing::get_all_groups();
	
		foreach ( $groups as $group ) {
		?>
			<label><?php echo $group->name; ?>:</label> 
			<input
				type="text" size="5" name="groups_pricing_value_<?php echo $group->group_id;?>_<?php echo $loop; ?>"
				value="<?php echo (isset($variation_data['groups_pricing_value_' . $group->group_id]))?$variation_data['groups_pricing_value_' . $group->group_id][0]:""; ?>" />
		<?php 
		}
	}
	
	/**
	 * Filter <del> tabs for variable products
	 * @param String $pricehtml
	 * @param Object $product
	 * @return String
	 */
	public static function woocommerce_variable_sale_price_html ( $pricehtml, $product ) {
		$string = $pricehtml;
	
		global $post, $woocommerce;
	
		if ( ($post == null) || !is_admin() ) {
			$commission = WooGroupPricing::get_commission( $product );
			if ( $commission ) { // if applying  discount, then remove the original prices.
				$string=preg_replace('/<del[^>]*>.+?<\/del>/i', '', $string);
			}
		}
		return $string;
	}
	
	
	public static function woocommerce_get_variation_price ( $price, $product, $min_or_max, $display ) {
		global $post, $woocommerce;
		
		$baseprice = $price;
		$result = $baseprice;
		
		if ( ($post == null) || !is_admin() ) {
			$variation_id = get_post_meta( $product->id, '_' . $min_or_max . '_price_variation_id', true );
	
			if ( ! $variation_id ) {
				return false;
			}
			
			$price        = get_post_meta( $variation_id, '_price', true );
			$result = $price;
			
			if ( $display ) {
				$variation        = $product->get_child( $variation_id );
	
				$commission = self::get_commission( $product, $variation_id );
					
				if ( $commission ) {
					$baseprice = $variation->get_regular_price();
			
					if ( $variation->get_sale_price() != $variation->get_regular_price() && $variation->get_sale_price() == $variation->price ) {
						if ( get_option( "wgp-baseprice", "regular" )=="sale" ) {
							$baseprice = $variation->get_sale_price();
						}
					}
					$product_price = $baseprice;
			
					$type = get_option( "wgp-method", "rate" );
					$result = 0;
					if ($type == "rate") {
						// if rate and price includes taxes
						if ( $variation->is_taxable() && get_option('woocommerce_prices_include_tax') == 'yes' ) {
							$_tax       = new WC_Tax();
							$tax_rates  = $_tax->get_shop_base_rate( $variation->tax_class );
							$taxes      = $_tax->calc_tax( $baseprice, $tax_rates, true );
							$product_price      = $_tax->round( $baseprice - array_sum( $taxes ) );
						}
			
						$result = WooGroupPricing::bcmul($product_price, $commission, WOO_GROUP_PRICING_DECIMALS);
							
						if ( $variation->is_taxable() && get_option('woocommerce_prices_include_tax') == 'yes' ) {
							$_tax       = new WC_Tax();
							$tax_rates  = $_tax->get_shop_base_rate( $variation->tax_class );
							$taxes      = $_tax->calc_tax( $result, $tax_rates, false ); // important false
							$result      = $_tax->round( $result + array_sum( $taxes ) );
						}
					} else {
						$result = WooGroupPricing::bcsub($product_price, $commission, WOO_GROUP_PRICING_DECIMALS);
					}
				}
			}
		}
		return $result;

	}
	
	/**
	 * Calculates the commissions.
	 * Order by priority:
	 * 1.- Variation values
	 * 2.- Product values
	 * 3.- Category values
	 * 4.- Default value
	 * @param unknown $product
	 * @param unknown $variation_id
	 * @return number
	 */
	public static function get_commission ( $product, $variation_id ) {
		global $post, $woocommerce;
	
		$user_id = get_current_user_id();
		$user_groups = WooGroupPricing::get_user_groups ( $user_id );
		$discount = 0;
		if ( sizeof( $user_groups ) > 0 ) {
			$first_group = $user_groups[0];

			// Variation - custom discount ...
			$custom = get_post_meta( $variation_id, 'groups_pricing_value_' . $first_group->group_id, true);
			
			if ( $custom !== "" ) {
				$discount = $custom;
			} else {
				
				// Product - custom discount ...
				$custom = get_post_meta( $product->id, 'groups_pricing_value_' . $first_group->group_id, true);
					
				// Category - custom discount ....
				if ( $custom !== "" ) {
					$discount = $custom;
				} else {
					$categories = wp_get_post_terms( $product->id, 'product_cat',array('fields'=>'ids') );
					if ( sizeof( $categories ) > 0 ) {
						$max_cat_id = null;
						$max_cat_discount = 0;
						foreach ( $categories as $cat_id ) {
							$cat_discount = get_woocommerce_term_meta($cat_id, 'groups_pricing_value_' . $first_group->group_id, true);
							if ( $cat_discount !== "" ) {
								if ( $cat_discount > $max_cat_discount ) {
									$max_cat_discount = $cat_discount;
									$max_cat_id = $cat_id;
								}
							}
						}
						if ( $max_cat_id !== null ) {
							$custom = $max_cat_discount;
						}
					}
				}
				
			}
					
			// general discount ....
			if ( $custom !== "" ) {
				$discount = $custom;
			} else {
				if ( get_option( "wgp-" . $first_group->group_id, "-1" ) !== "-1" ) {
					$discount = get_option( "wgp-" . $first_group->group_id );
				}
			}
		}
		if ( $discount ) {
			$method = get_option( "wgp-method", "rate" );
			if ( $method == "rate" ) {
				$discount = WooGroupPricing::bcsub ( 1, $discount, WOO_GROUP_PRICING_DECIMALS );
				// for security reasons, set 0
				if ( $discount < 0 ) {
					$discount = 0;
				}
			}
		}
	
		return $discount;
	}
	
}
WGP_Variations_Admin::init();
