<?php
	global $wooVeeqo;
	
	if(!$wooVeeqo->bridge->does_bridge_exist()) {
?>
<form class="bridge-connection" name="bridge_connect" method="POST" action="">
	<input class="bridge-url-input" type="text" name="bridge_url" size="60" placeholder="Bridge URL..." />
	<input class="connect-bridge veeqo-button-blue" type="submit" name="connect_bridge" value="Connect" />
</form>

<div class="bridge-connection-view">
	<div class="loading-msg">
		<div class="veeqo-spinner">
			<div class="ui active dimmer">
				<div class="ui large inline loader" style="margin: 0 auto"></div>
			</div>
		</div>
		<h1>Hold on! We're connecting your Veeqo account...</h1>
		<div class="view-additional-info"><a id="display-bridge-steps" href="#">View More Info</a></div>
	</div>
	<div class="bridge-response"></div>
</div>

<?php
	}
?>
