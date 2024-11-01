<?php
	add_action('admin_init', 'veeqo_integration_settings');
	add_action('admin_enqueue_scripts', 'wooveeqo_styles_scripts');
	add_action('wp_ajax_start_veeqo_connection_process', 'start_veeqo_connection_process');
	
	if(is_admin()) {
		add_action('admin_menu', 'veeqo_integration_settings_menuitem');
		add_action('admin_notices', 'admin_bridge_notice');
	}

	function wooveeqo_styles_scripts() {
		global $wooVeeqo;
		$screen = get_current_screen();
		
		if(in_array($screen->id, array('veeqo-integration-settings', 'toplevel_page_veeqo-integration-settings'))) {
			wp_enqueue_style('veeqo_integration', $wooVeeqo->core->plugin_url() . 'assets/css/admin.css', array(), $wooVeeqo->core->plugin_version(), 'screen');
			wp_enqueue_style('veeqo_integration-main', $wooVeeqo->core->plugin_url() . 'assets/css/veeqo-woocommerce.css', array(), $wooVeeqo->core->plugin_version(), 'screen');
			
			wp_enqueue_script('veeqo-woocommerce', $wooVeeqo->core->plugin_url() . 'assets/js/veeqo-woocommerce.js', array(), $wooVeeqo->core->plugin_version(), true);
		}
	}
	
	function start_veeqo_connection_process() {
		global $wooVeeqo;
	
		$bridgeUrl = $_POST['bridge_url'];
		if($wooVeeqo->bridge->valid_bridge_url_loaded($bridgeUrl)) {
			$wooVeeqo->bridge->process_bridge_upload();
		} else {
			echo '<div class="bridge-error">Sorry but it appears that the bridge URL isn\'t valid. Please check and re-enter the URL.</div>';
		}
		
		wp_die();
	}
	
	function admin_bridge_notice() {
		global $wooVeeqo;
		global $pagenow;
		
		if($pagenow == 'index.php' && !$wooVeeqo->bridge->does_bridge_exist()) {
			echo '<div class="error">';
			echo '<p>You have no Veeqo bridge loaded! Click <a href="' . admin_url("admin.php?page=veeqo-integration-settings") . '">here</a> to load one.</p>';
			echo '</div>';
		}
	}
	
	function veeqo_integration_settings() {
		register_setting('veeqo-integration-settings-group', 'bridge_url');
	}
	
	function veeqo_integration_settings_menuitem() {
		add_menu_page('Veeqo', 'Veeqo', 'administrator', 'veeqo-integration-settings', 'veeqo_integration_settings_page', 'dashicons-cloud');
	}
?>