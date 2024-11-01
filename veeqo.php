<?php
/**
 * Plugin Name: Veeqo for WooCommerce
 * Plugin URI: https://www.veeqo.com/woocommerce-integration
 * Description: This plugin allows for quick and easy integration with Veeqo, the leading inventory management software.
 * Version: 2.2.8
 * Author: Veeqo
 * Author URI: https://veeqo.com
 *
 * WC requires at least: 1.6
 * WC tested up to: 4.0
 *
 * License: BSD-3-Clause
 */

// Check to see if it's being loaded via WordPress and not directly
if(!defined('WPINC')) {
    die('Cannot be loaded directly.');
}

// Check to see if WooCommerce is currently active
if(!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    die('It looks like your WordPress installation doesn\'t have WooCommerce activated. Please activate WooCommerce first.');
}

include_once('includes/veeqo_includes.php');
include_once('includes/wordpress_hooks.php');

function veeqo_integration_settings_page() {
    global $wooVeeqo;

    include_once($wooVeeqo->core->views_path() . 'layout/header.php');
    include_once($wooVeeqo->core->views_path() . 'index_view.php');
}
?>
