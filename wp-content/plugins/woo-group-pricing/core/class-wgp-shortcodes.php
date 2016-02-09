<?php
/**
 * class-wgp-shortcodes.php
 *
 * Copyright (c) Antonio Blanco http://www.eggemplo.com
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
 * @author Antonio Blanco (eggemplo)
 * @package woogrouppricing
 * @since woogrouppricing 2.4.1
 */

/**
 * WGP_Shortcodes class
 */
class WGP_Shortcodes {

	public static function init() {
		add_shortcode( 'wgp_show_discount', array( __CLASS__, 'wgp_show_discount' ) );
	}
	
	function wgp_show_discount () {
		$user_id = get_current_user_id();
		$user_groups = WooGroupPricing::get_user_groups ( $user_id );
		$discount = 0;
		if ( sizeof( $user_groups ) > 0 ) {
			$first_group = $user_groups[0];
			if ( get_option( "wgp-" . $first_group->group_id, "-1" ) !== "-1" ) {
				$discount = get_option( "wgp-" . $first_group->group_id );
			}
		}
		if ( $discount !== 0 ) {
			echo $discount;
		}
	}	
	
}
WGP_Shortcodes::init();
