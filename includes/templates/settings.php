<?php
    /* Settings Page */
    global $hdv_setting;
    $hdv_setting = hdv_get_settings_values();
?>
<div id="save_settings" class="hd_button2" style="display: block;">SAVE</div>

<div id = "hdv_settings">
	<div id="hdv_tabs">
		<ul>
			<li class="hdv_active_tab" data-hdv-content="hdv_settings_global">Global</li>
			<li class="" data-hdv-content="hdv_settings_company">Company</li>
			<li class="" data-hdv-content="hdv_settings_ecommerce">eCommerce</li>
			<li class="" data-hdv-content="hdv_settings_customizer">Customizer</li>
		</ul>
		<div class="clear"></div>
	</div>
	<?php
        include(dirname(__FILE__).'/settings/global.php');
        include(dirname(__FILE__).'/settings/company.php');
        include(dirname(__FILE__).'/settings/eCommerce.php');
        include(dirname(__FILE__).'/settings/customizer.php');
    ?>
</div>
