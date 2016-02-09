<?php
/**
 * class-woogrouppricing.php
 *
 * Copyright (c) Antonio Blanco http://www.blancoleon.com
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
 */

/**
 * WooGroupPricing class
 */
class WooGroupPricing {

	public static function init() {
	
		add_filter('woocommerce_get_price', array( __CLASS__, 'woocommerce_get_price' ), 10, 2);
		
		add_action('woocommerce_product_write_panel_tabs', array(__CLASS__,'woocommerce_product_write_panel_tabs') );
		add_action('woocommerce_product_write_panels', array(__CLASS__,'woocommerce_product_write_panels') );
		add_action('woocommerce_process_product_meta', array(__CLASS__,'woocommerce_process_product_meta') );
		
	}
	
	public static function woocommerce_get_price ( $price, $product ) {
		global $post, $woocommerce;

		$baseprice = $price;
		$result = $baseprice;
		
		if ( ($post == null) || !is_admin() ) {
		
			if ( $product->is_type( 'variation' ) ) {
				$commission = WGP_Variations_Admin::get_commission( $product, $product->variation_id );
			} else {
				$commission = self::get_commission( $product );
			}
			
			if ( $commission ) {
				
				$baseprice = $product->get_regular_price();
				
				if ( $product->get_sale_price() != $product->get_regular_price() && $product->get_sale_price() == $product->price ) {
					if ( get_option( "wgp-baseprice", "regular" )=="sale" ) {
						$baseprice = $product->get_sale_price();
					}
				}
				$product_price = $baseprice;
				
				$type = get_option( "wgp-method", "rate" );
				$result = 0;
				if ($type == "rate") {
					// if rate and price includes taxes
					if ( $product->is_taxable() && get_option('woocommerce_prices_include_tax') == 'yes' ) {
						$_tax       = new WC_Tax();
						$tax_rates  = $_tax->get_shop_base_rate( $product->tax_class );
						$taxes      = $_tax->calc_tax( $baseprice, $tax_rates, true );
						$product_price      = $_tax->round( $baseprice - array_sum( $taxes ) );
					}

					$result = self::bcmul($product_price, $commission, WOO_GROUP_PRICING_DECIMALS);
					
					if ( $product->is_taxable() && get_option('woocommerce_prices_include_tax') == 'yes' ) {
						$_tax       = new WC_Tax();
						$tax_rates  = $_tax->get_shop_base_rate( $product->tax_class );
						$taxes      = $_tax->calc_tax( $result, $tax_rates, false ); // important false
						$result      = $_tax->round( $result + array_sum( $taxes ) );
					}
				} else {
					$result = self::bcsub($product_price, $commission, WOO_GROUP_PRICING_DECIMALS);
				}
			}
		}
		return $result;
	}
	
	public static function woocommerce_product_write_panel_tabs() {
	
		echo '<li class="groups_pricing_tab general_options"><a href="#groups_pricing_data">' . __( 'Groups Pricing', WOO_GROUP_PRICING_DOMAIN ) . '</a></li>';
	
	}
	
	public static function woocommerce_product_write_panels() {
		global $post;
	
		$groups = self::get_all_groups();
		if ( $groups ) {
			$pricing_options = array();
			foreach ( $groups as $group ) {
				$pricing_options['value_' . $group->group_id] = get_post_meta($post->ID, 'groups_pricing_value_' . $group->group_id, true);
			}
		}
		?>
		<div id="groups_pricing_data" class="panel woocommerce_options_panel">
			<div class="options_group">
				<p class="description">
					<?php echo __( 'Leave empty if no custom group discount should be applied to this product (default setting).', WOO_GROUP_PRICING_DOMAIN ); ?>
				</p>
			</div>
			
			<div class="options_group custom_tab_options">
				<?php 
				foreach ( $groups as $group ) {
				?>
				<p class="form-field">
					<label><?php echo $group->name; ?>:</label>
					<input type="text" class="short" name="groups_pricing_value_<?php echo $group->group_id;?>" value="<?php echo @$pricing_options['value_' . $group->group_id]; ?>" />
				</p>
				<?php 
				}
				?>
	        </div>	
		</div>
	<?php
	}

	public static function woocommerce_process_product_meta( $post_id ) {
		$groups = self::get_all_groups();
		if ( $groups ) {
			foreach ( $groups as $group ) {
				update_post_meta( $post_id, 'groups_pricing_value_' . $group->group_id, ( isset($_POST['groups_pricing_value_' . $group->group_id]) && ( $_POST['groups_pricing_value_' . $group->group_id] !== "" ) ) ? trim($_POST['groups_pricing_value_' . $group->group_id]) : '' );
			}
		}
	}
	
	// extra functions
	
	public static function get_commission ( $product ) {
		global $post, $woocommerce;
	
		$user_id = get_current_user_id();
		$user_groups = self::get_user_groups ( $user_id );
		$discount = 0;
		if ( sizeof( $user_groups ) > 0 ) {
			$first_group = $user_groups[0];
			
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
				$discount = self::bcsub ( 1, $discount, WOO_GROUP_PRICING_DECIMALS );
				// for security reasons, set 0
				if ( $discount < 0 ) {
					$discount = 0;
				}
			}
		}
	
		return $discount;
	}
	
	public static function get_all_groups (  ) {
		global $wpdb;
	
		$groups_table = _groups_get_tablename( 'group' );
	
		return $wpdb->get_results( "SELECT * FROM $groups_table ORDER BY name" );
	
	}
	
	public static function get_user_groups ( $user_id ) {
		global $wpdb;
	
		$groups_table = _groups_get_tablename( 'group' );
		$result = array();
		if ( $groups = $wpdb->get_results( "SELECT * FROM $groups_table ORDER BY group_id DESC" ) ) {
			foreach( $groups as $group ) {
				$is_member = Groups_User_Group::read( $user_id, $group->group_id ) ? true : false;
				if ( $is_member ) {
					$result[] = $group;
				}
			}
		}
		return  $result;
	}
	
	public static function bcmul( $data1, $data2, $prec = 0 ) {
		$result = 0;
		if ( function_exists('bcmul') ) {
			$result = bcmul( $data1, $data2, $prec );
		} else {
			$value = $data1 * $data2;
			if ($prec) {
				$result = round($value, $prec);
			}
		}
		return $result;
	}
	
	public static function bcsub( $data1, $data2, $prec = 0 ) {
		$result = 0;
		if ( function_exists('bcsub') ) {
			$result = bcsub( $data1, $data2, $prec );
		} else {
			$value = $data1 - $data2;
			if ($prec) {
				$result = round($value, $prec);
			}
		}
		return $result;
	}
	
	
	
}
WooGroupPricing::init();
