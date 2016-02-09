<?php
/**
 * class-wgp-grouped-admin.php
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
 * @since 2.4
 */
if (! defined ( 'ABSPATH' )) {
	exit ();
}

/**
 * Category product admin handlers.
 */
class WGP_Grouped_Admin {
	
	/**
	 * Sets up the init action.
	 */
	public static function init() {
		
		// filter <del> tags for variable products
		add_filter('woocommerce_grouped_price_html', array ( __CLASS__, 'woocommerce_grouped_price_html' ), 10, 2 );
		
	}
	
	public static function woocommerce_grouped_price_html ( $price, $product ) {
		$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );
		$child_prices     = array();

		foreach ( $product->get_children() as $child_id ) {
			//$child_prices[] = get_post_meta( $child_id, '_price', true );
			$child = get_product( $child_id );
			$child_prices[] = $child->get_price(); 
		}

		$child_prices     = array_unique( $child_prices );
		$get_price_method = 'get_price_' . $tax_display_mode . 'uding_tax';

		if ( ! empty( $child_prices ) ) {
			$min_price = min( $child_prices );
			$max_price = max( $child_prices );
		} else {
			$min_price = '';
			$max_price = '';
		}

		if ( $min_price ) {
			if ( $min_price == $max_price ) {
				$display_price = wc_price( $product->$get_price_method( 1, $min_price ) );
			} else {
				$from          = wc_price( $product->$get_price_method( 1, $min_price ) );
				$to            = wc_price( $product->$get_price_method( 1, $max_price ) );
				$display_price = sprintf( _x( '%1$s&ndash;%2$s', 'Price range: from-to', 'woocommerce' ), $from, $to );
			}

			$price = $display_price . $product->get_price_suffix();

		}
		
		return $price;
	}
	
	
}
WGP_Grouped_Admin::init();
