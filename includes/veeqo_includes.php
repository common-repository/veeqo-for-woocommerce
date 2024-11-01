<?php
	// Main core class
	
	include_once('veeqo_core_class.php');
	include_once('bridge_class.php');
	
	class WooVeeqo {
		
		public function __construct() {
			$this->core = new VeeqoPluginCore();
			$this->bridge = new VeeqoConnection();
		}
	
	}
	
	$wooVeeqo = new WooVeeqo();
	
?>