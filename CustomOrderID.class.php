<?php
/*
Plugin Name: TheCartPress Custom Order ID
Plugin URI: http://thecartpress.com/
Description: TheCartPress
Version: 1.2
Author: TheCartPress team
Author URI: http://thecartpress.com
License: GPL
Parent: thecartpress
*/

/**
 * This file is part of Custom Order ID.
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'TCPCustomOrderID' ) ) :

class TCPCustomOrderID {

	function __construct() {
		add_action( 'tcp_init'						, array( $this, 'init' ) );
		if ( is_admin() ) {
			register_activation_hook( __FILE__		, array( $this, 'activate_plugin' ) );
			add_action( 'tcp_main_settings_page'	, array( $this, 'tcp_main_settings_page' ) );
			add_filter( 'tcp_main_settings_action'	, array( $this, 'tcp_main_settings_action' ) );
		}
	}

	function tcp_main_settings_page() {
		global $thecartpress;
		$custom_order_id = $thecartpress->get_setting( 'custom_order_id', 0 ); ?>
		<tr>
			<th><?php _e( 'Custom Order ID', 'tcp' ); ?></th>
			<td>
			<input type="number" id="custom_order_id" name="custom_order_id" value="<?php echo $custom_order_id; ?>" size="6" maxlength="20"/>
			<p class="description"><?php _e( 'To apply this change the database user must have privilages to Alter tables. If the ID typed is less than the current ID,  new ID will not be applied.', 'tcp-coi'); ?></p>
			</td>
		</tr><?php
	}

	function tcp_main_settings_action( $settings ) {
		$custom_order_id = isset( $_POST['custom_order_id'] ) ? (int)$_POST['custom_order_id'] : -1;
		if ( $custom_order_id > 0 ) {
			global $wpdb;
			$sql = 'ALTER TABLE ' . $wpdb->prefix . 'tcp_orders AUTO_INCREMENT = %d';
			$sql = $wpdb->prepare( $sql, $custom_order_id );
			$wpdb->query( $sql );
		}
		$settings['custom_order_id'] = $custom_order_id;
		return $settings;
	}

	function init() {
		if ( ! function_exists( 'is_plugin_active' ) ) require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		if ( ! is_plugin_active( 'thecartpress/TheCartPress.class.php' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		}
		if ( function_exists( 'load_plugin_textdomain' ) ) {
			load_plugin_textdomain( 'tcp-coi', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}
	}

	function admin_notices() { ?>
		<div class="error"><p><?php _e( '<strong>Custom Order ID for TheCartPress</strong> requires TheCartPress plugin activated.', 'tcp-coi' ); ?></p></div><?php
	}

	function activate_plugin() {
		if ( ! function_exists( 'is_plugin_active' ) ) require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		if ( ! is_plugin_active( 'thecartpress/TheCartPress.class.php' ) )
			exit( __( '<strong>Custom Order ID for TheCartPress</strong> requires TheCartPress plugin', 'tcp-coi' ) );
	}
}

new TCPCustomOrderID();
endif;