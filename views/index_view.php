<section>
<?php
	global $wooVeeqo;
	
	if(isset($_POST['removebridge'])) {
		if($wooVeeqo->bridge->remove_bridge()) {
			echo '<div class="wrap"><h2>Bridge Removed</h2>';
			echo '<p>The Veeqo bridge has been disconnected from your store.</p>';
			echo '</div>';
		} else {
			echo '<div class="wrap"><h2>Failed to Remove Bridge</h2>';
			echo '<p>We were unable to remove the bridge from your store. Are you sure it still exists?</p>';
			echo '</div>';
		}
		exit();
	}
?>

	<h1>Welcome to Veeqo for WooCommerce!</h1>
	<?php
		if($wooVeeqo->bridge->does_bridge_exist()) {
			echo 'Your bridge is <strong><span style="color: #00ff00">installed</span></strong>!';
			echo '<br /><br /><form method="POST" action="">';
			echo '<input type="submit" name="removebridge" class="center-action-button veeqo-button-blue" value="Remove Bridge" />';
			echo '</form>';
		} else {
			echo '<p class="starter-message">Veeqo is the place to keep your inventory right and your shipping on time. Setup your account today and install our free WordPress plugin to connect your online inventory directly to your WooCommerce store. Ready to get connected? Just follow the steps below to get started!</p>';
			include_once($wooVeeqo->core->views_path() . 'install_bridge_form_view.php');
			echo '<div class="home-view">';
			if(!isset($_POST['bridge_url'])) {
				include_once($wooVeeqo->core->views_path() . 'layout/index/index_bottom.php');
			}
			echo '</div>';
		}
		
	?>
</div>
</section>