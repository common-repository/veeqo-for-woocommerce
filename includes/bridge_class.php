<?php
	class VeeqoConnection extends VeeqoPluginCore {

		public function loaded_bridge_url() {
			return strip_tags($_POST['bridge_url']);
		}

		public function valid_bridge_url_loaded($bridge_url) {
			// TODO: Make it into one regex - also URL might have www. so cater for this
			return preg_match('/https:\/\/(api2cart\.veeqo\.com|api\.api2cart\.veeqo\.com|api\.api2cart\.com)\/v1\.0\/bridge\.download\.file?/', $bridge_url) ||
				preg_match('/https:\/\/app\.veeqo\.com\/channels\/get_bridge\/(.*)?store_key=(.*)/', $bridge_url);
		}

	  	public function extracted_store_key() {
	  		$store_key_pattern = "/store_key=([0-9A-Za-z])*/";
	  		preg_match($store_key_pattern, $this->loaded_bridge_url(), $store_key);

	  		return preg_replace('/store_key=/', '', $store_key[0]);
	  	}

	  	public function bridge_installation_directory() {
			return $this->root_path() . 'bridge2cart/';
		}

		public function does_bridge_exist() {
			return file_exists($this->root_path() . 'bridge2cart/bridge.php') ? true : false;
		}

	  	public function remove_bridge() {
	  		foreach($this->bridge_inner_files() as $bridge_inner_file) {
	  			$file_location = $this->bridge_installation_directory() . $bridge_inner_file;
	  			if(file_exists($file_location)) {
	  				return unlink($file_location) ? true : false;
	  			} else {
	  				return false;
	  			}
	  		}

	  		rmdir($this->bridge_installation_directory()) ? true : false;
	  	}

		public function process_bridge_upload() {
			$this->output_bridge_log('<div class="bridge-installation">');
			$this->output_bridge_log('Installing from source: <strong>' . htmlentities($this->loaded_bridge_url(), ENT_QUOTES, 'UTF-8') . '</strong>', true);

			// Open temporary bridge.zip file for writing
			$open_file = $this->open_temporary_bridge_zip_file();

			$this->output_bridge_log('Opening file for writing..', true);

			// Start downloading bridge file
			$this->download_bridge_file($open_file);

			fclose($open_file);

			// Unzip bridge file
			$this->unzip_downloaded_bridge_file();

			$this->set_bridge_directory_permissions();
			$this->delete_temporary_bridge_zip_file();

			$this->output_bridge_log('Activating webstore (' . htmlentities($this->extracted_store_key(), ENT_QUOTES, 'UTF-8') . ') at Veeqo..', true);
			$this->activate_store_at_veeqo($this->extracted_store_key());

			echo '<span class="success">Bridge loaded!</span>';

			echo '</div>';

			echo '<a class="center-action-button veeqo-button-blue" href="https://veeqo.com">Head to Veeqo</a>';

		}

		// Currently WIP
		private function activate_store_at_veeqo($store_key) {
			if(!$store_key) {
				return false;
			}

			$post_data = http_build_query(array('store_key' => $store_key));

			$request_options = array(
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS => $post_data,
				CURLOPT_TIMEOUT =>  60,
				CURLOPT_URL     => 'https://app.veeqo.com/channels/api2cart_webhook',
				CURLOPT_HEADER => 0,
				CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1'
			);

			$initCURL = curl_init();
			curl_setopt_array($initCURL, $request_options);
			$execCurl = curl_exec($initCURL);
			$curlHttpResponse = curl_getinfo($initCURL, CURLINFO_HTTP_CODE);
			$response = json_decode($execCurl, true);

			curl_close($initCURL);

			if($curlHttpResponse !== 200) {
				if($response['error_messages'] == 'Store does not exist on Veeqo') {
					$errorMsg = 'The store does not exist on Veeqo. Are you sure you used the correct bridge URL?';
				} elseif($response['error_messages'] == 'Parameter store_key cannot be blank') {
					$errorMsg = 'You have no store key. Are you sure you have entered it correctly?';
				} elseif(preg_match('/already exists/', $response['error_messages'])) {
					$errorMsg = 'This store already exists on Veeqo. Please turn on the store in Settings -> Stores';
				} elseif($response['error_messages']) {
					$errorMsg = 'Error activating the channel at Veeqo due to the following: ' . $response['error_messages'];
				} else {
					// TODO: Acommodate for various http error codes
					$errorMsg = 'Error activating the channel at Veeqo at this current time (HTTP code: ' . $curlHttpResponse . '). Please try again later.';
				}
			}

			if(!$errorMsg || $errorMsg == "") {
				$this->output_bridge_log('Store activated at Veeqo!', true);
			} else {
				$this->output_bridge_error($errorMsg);
			}

		}

		private function output_bridge_log($content, $line_break = false) {
			$outputlbr = $line_break ? '<br />' : '';
			echo $content . $outputlbr;
		}

		private function output_bridge_error($error_message) {
			if(!$error_message) {
				return false;
			} else {
				echo '<span class="failure">' . $error_message . '</span>';
				die;
			}
		}

		private function bridge_inner_files() {
	  		return array('bridge.php', 'config.php');
	  	}

		private function temporary_bridge_zip_file() {
			return $this->root_path() . 'bridge.zip';
		}

		private function delete_temporary_bridge_zip_file() {
			if($this->temporary_bridge_zip_file()) {
				unlink($this->temporary_bridge_zip_file());
			}
		}

		private function open_temporary_bridge_zip_file() {
			file_exists($this->temporary_bridge_zip_file()) ? unlink($this->temporary_bridge_zip_file()) : false;
			return fopen($this->temporary_bridge_zip_file(), 'w');
		}

		private function download_bridge_file($write_file) {
			$request_options = array(
			  CURLOPT_FILE    => $write_file,

			  CURLOPT_TIMEOUT =>  60,
			  CURLOPT_URL     => $this->loaded_bridge_url(),
			  CURLOPT_HEADER => 0,
			  CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1'
			);

			$initCURL = curl_init();
			curl_setopt_array($initCURL, $request_options);
			curl_exec($initCURL);
			$curlHttpResponse = curl_getinfo($initCURL, CURLINFO_HTTP_CODE);
			curl_close($initCURL);

			if($curlHttpResponse !== 200) {
				// Display error to the user if download isn't successful
				$errorMsg = 'Error downloading bridge file (HTTP code: ' . $curlHttpResponse . '). Please try again later.';
				$this->output_bridge_error($errorMsg);
			} else {
				$this->output_bridge_log('Downloading bridge file..', true);
			}
		}

		private function unzip_downloaded_bridge_file() {
			WP_Filesystem();

			// Unzip bridge file
			$file = $this->temporary_bridge_zip_file();
			if(file_exists($file)) {
				if(unzip_file($file, $this->root_path())) {
					$this->output_bridge_log('Unzipped the bridge file.', true);
				} else {
					$errorMsg = 'No zip unarchive utility available. We would recommend unzipping the bridge.zip file via your FTP client.';
					$this->output_bridge_error($errorMsg);
				}
			} else {
				$errorMsg = 'Failed to locate the temporary bridge.zip file. Are you sure it exists?';
				$this->output_bridge_error($errorMsg);
			}
		}

		private function set_bridge_directory_permissions() {
			chmod($this->bridge_installation_directory(), 0755);
		}

	}

?>
