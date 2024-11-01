<?php
	
	class VeeqoPluginCore {
		
		public function root_path() {
			return get_home_path();
		}
		
		public function plugin_version() {
			$currentVersion = '1.0.2';
			return $currentVersion;
		}
		
		public function plugin_path() {
			return plugin_dir_path(dirname(__FILE__));
		}
		
		public function plugin_url() {
			return plugin_dir_url(dirname(__FILE__));
		}
		
		public function views_path() {
			return $this->plugin_path() . 'views/';
		}
		
	}

?>