<?php
/**
 * class-wgp-categories-admin.php
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
 * @since 2.1
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Category product admin handlers. 
*/
class WGP_Categories_Admin {

	/**
	 * Sets up the init action.
	 */
	public static function init() {

		// when adding a new category
		add_action( "product_cat_add_form_fields", array( __CLASS__, 'taxonomy_add_form_fields' ) );
		
		// when editing a category
		add_action( "product_cat_edit_form", array( __CLASS__, 'taxonomy_edit_form' ), 10, 2 );
		
		// Save for a new category
		add_action( "created_product_cat", array( __CLASS__, 'created_taxonomy' ), 10, 2 );
		
		// Save for a category
		add_action( "edited_product_cat", array( __CLASS__, 'edited_taxonomy' ), 10, 2 );
		
	}

	/**
	 * WGP fields before the "Add New Category" button.
	 * 
	 * @param string $taxonomy
	 */
	public static function taxonomy_add_form_fields( $taxonomy ) {
		self::panel( $taxonomy );
		
	}

	/**
	 * Hook in wp-admin/edit-tag-form.php - add fields
	 * 
	 * @param string $tag
	 * @param string $taxonomy
	 */
	public static function taxonomy_edit_form( $tag, $taxonomy ) {
		self::panel( $tag, $taxonomy );
		
	}

	/**
	 * Renders our  panel.
	 */
	public static function panel( $tag = null ) {

		global $post, $wpdb;

		$output        = '';
		$term_id       = isset( $tag->term_id ) ? $tag->term_id : null;

		$output .= '<div class="form-field">';

		$groups = WooGroupPricing::get_all_groups();
		if ( $groups ) {
			$pricing_options = array();
			foreach ( $groups as $group ) {
				$pricing_options['value_' . $group->group_id] = get_woocommerce_term_meta($term_id, 'groups_pricing_value_' . $group->group_id, true);
			}
		}
		
		$output .= '<div class="options_group"  style="border: 1px solid #ccc; padding:10px;">';
		$output .=  '<h4>' . __( 'Woocommerce Group Pricing', WOO_GROUP_PRICING_DOMAIN ) . '</h4>';
		$output .= '<p class="description">';
		$output .= __( 'Leave empty if no custom group discount should be applied to this category.', WOO_GROUP_PRICING_DOMAIN );
		$output .= '</p>';
		
		foreach ( $groups as $group ) {
			$output .= '<p>';
			$output .= '<label style="width:120px;float:left;">' . $group->name . '</label>';
			$output .= '<input type="text" style="width:auto;" size="10" name="groups_pricing_value_' . $group->group_id . '" value="' . @$pricing_options['value_' . $group->group_id] . '" />';
			$output .= '</p>';
		}

		$output .= '</div>';
		
		$output .= '</div>'; // .form-field
		echo $output;

	}

	/**
	 * Save WGP values for a new category
	 * @param int $term_id
	 * @param int $tt_id
	 */
	public static function created_taxonomy( $term_id, $tt_id ) {
		self::edited_taxonomy( $term_id, $tt_id );
	}

	/**
	 * Save category WGP values
	 * @param int $term_id term ID
	 * @param int $tt_id taxonomy ID
	 */
	public static function edited_taxonomy( $term_id, $tt_id ) {

		$groups = WooGroupPricing::get_all_groups();
		if ( $groups ) {
			foreach ( $groups as $group ) {
				update_woocommerce_term_meta( $term_id, 'groups_pricing_value_' . $group->group_id, ( isset($_POST['groups_pricing_value_' . $group->group_id]) && ( $_POST['groups_pricing_value_' . $group->group_id] !== "" ) ) ? trim($_POST['groups_pricing_value_' . $group->group_id]) : '' );
			}
		}
	}


}
WGP_Categories_Admin::init();
