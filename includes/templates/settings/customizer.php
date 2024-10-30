<?php
	/* Settings Invoice Customizer */
?>


<div id = "hdv_settings_customizer" class = "hdv_tab">
	<h3>Invoice Customizer</h3>
	<p>The following settings are to customize the appearance of your invoices.</p>

	<h3>Layout</h3>
	<br/>
	<input type = "hidden" id="hdv_setting_layout" value = "<?php echo $hdv_setting->layout; ?>"/>
	<div class = "one_half">
		<div class="hdv_row">
			<label>One Column Layout</label>
			<img src = "<?php echo plugins_url('../../../images/1-col.jpg', __FILE__); ?>" class = "hdv_setting_layout <?php if($hdv_setting->layout == "1_col") { echo "hdv_selected_layout"; } ?>" data-layout = "1_col" alt = "1 Column Layout" />
		</div>
	</div>
	<div class = "one_half last">
		<div class="hdv_row">
			<label>Multi-column Layout  <span class="hdv_tooltip">?<span class="hdv_tooltip_content"><span>Multi Column layout will be available soon</span></span></span></label>
			<img src = "<?php echo plugins_url('../../../images/2-col.jpg', __FILE__); ?>" class = "hdv_setting_layout_disable <?php if($hdv_setting->layout == "2_col") { echo "hdv_selected_layout"; } ?>" data-layout = "2_col" alt = "1 Column Layout" />
		</div>
	</div>
	<div class = "clear"></div>

	<h3>Company Information</h3>
	<br/>
	<div class = "one_third">
		<div class="hdv_row">
			<label for="hdv_setting_layout_logo">Disable Company Logo</label>
			<div class="switchLR">
				<input name="hdv_setting_layout_logo" id="hdv_setting_layout_logo" class="hdv_toggle" type="checkbox" value="yes"  <?php if($hdv_setting->layout_logo == "disable") {echo 'checked';}?>><label for="hdv_setting_layout_logo"></label>
			</div>
		</div>
	</div>
	<div class = "one_third">
		<div class="hdv_row">
			<label for="hdv_setting_layout_address">Disable Company Address</label>
			<div class="switchLR">
				<input name="hdv_setting_layout_logo" id="hdv_setting_layout_address" class="hdv_toggle" type="checkbox" value="yes"  <?php if($hdv_setting->layout_address == "disable") {echo 'checked';}?>><label for="hdv_setting_layout_address"></label>
			</div>
		</div>
	</div>
	<div class = "one_third last">
		<div class="hdv_row">
			<label for="hdv_setting_layout_love">❤️ HDInvoice <span class="hdv_tooltip">?<span class="hdv_tooltip_content"><span>Allow a discrete link to HDInvoice on the bottom of all invoices.</span></span></span></label>
			<div class="switchLR">
				<input name="hdv_setting_layout_love" id="hdv_setting_layout_love" class="hdv_toggle" type="checkbox" value="yes"  <?php if($hdv_setting->layout_love == "enable") {echo 'checked';}?>><label for="hdv_setting_layout_love"></label>
			</div>
		</div>
	</div>
	<div class = "clear"></div>
	<p>
		More customization options will be provided in future updates to HDInvoice
	</p>
</div>
