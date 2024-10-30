<?php
	/* Settings Global */
	global $hdv_setting;
?>

<div id = "hdv_settings_global" class = "hdv_tab hdv_tab_active">
	<h3>Global Options</h3>
	<p>The following settings will be default on all created invoices.</p>
	
	<div class = "one_half">	
		<div class="hdv_row">
			<label for="hdv_setting_currency_symbol">Currency Symbol <span class="hdv_tooltip">?<span class="hdv_tooltip_content"><span>If you do not use dollars ($), then input your currency symbol here</span></span></span></label>
			<input type="text" id="hdv_setting_currency_symbol" class="hdv_input" placeholder="$" value = "<?php echo $hdv_setting->currency_symbol; ?>" required="">
		</div>
	</div>
	<div class = "one_half last">	
		<div class="hdv_row">
			<label for="hdv_setting_currency_position">Right Side Currency Symbol <span class="hdv_tooltip">?<span class="hdv_tooltip_content"><span>If you use a currency where the currency symbol should appear to the right of the value, then toggle this option.</span></span></span></label>			
			<div class="switchLR">
				<input name="hdv_setting_currency_position" id="hdv_setting_currency_position" class="hdv_toggle" type="checkbox" value="yes"  <?php if($hdv_setting->currency_position == "right") {echo 'checked';}?>><label for="hdv_setting_currency_position"></label>
			</div>			
		</div>
	</div>
	<div class = "clear"></div>
	
	<hr/>
	
	<p>Tax can be overridden on a per-customer basis or on an individual invoice.</p>
	
	<div class = "one_half">	
		<div class="hdv_row">
			<label for="hdv_setting_tax_name1">TAX Name</label>
			<input type="text" id="hdv_setting_tax_name1" class="hdv_input" value = "<?php echo $hdv_setting->tax_name1; ?>" placeholder="please enter the name of your tax" required="">
		</div>
	</div>
	<div class = "one_half last">	
		<div class="hdv_row">
			<label for="hdv_setting_tax_percent1">Tax Percentage</label>
			<input type="text" id="hdv_setting_tax_percent1" class="hdv_input" value = "<?php echo $hdv_setting->tax_percent1; ?>" placeholder="please enter the tax %" required="">
		</div>
	</div>	
	<div class = "clear"></div>
	<div class = "one_half">	
		<div class="hdv_row">
			<label for="hdv_setting_tax_name2">TAX Name 2</label>
			<input type="text" id="hdv_setting_tax_name2" class="hdv_input" value = "<?php echo $hdv_setting->tax_name2; ?>" placeholder="please enter the name of your tax" required="">
		</div>
	</div>
	<div class = "one_half last">	
		<div class="hdv_row">
			<label for="hdv_setting_tax_percent2">Tax Percentage 2</label>
			<input type="text" id="hdv_setting_tax_percent2" class="hdv_input" value = "<?php echo $hdv_setting->tax_percent2; ?>" placeholder="please enter the tax %" required="">
		</div>
	</div>	
	<div class = "clear"></div>
	<div class = "one_half">	
		<div class="hdv_row">
			<label for="hdv_setting_tax_name3">TAX Name 3</label>
			<input type="text" id="hdv_setting_tax_name3" class="hdv_input" value = "<?php echo $hdv_setting->tax_name3; ?>" placeholder="please enter the name of your tax" required="">
		</div>
	</div>
	<div class = "one_half last">	
		<div class="hdv_row">
			<label for="hdv_setting_tax_percent3">Tax Percentage 3</label>
			<input type="text" id="hdv_setting_tax_percent3" class="hdv_input" value = "<?php echo $hdv_setting->tax_percent3; ?>" placeholder="please enter the tax %" required="">
		</div>
	</div>	
	<div class = "clear"></div>
	
	<hr/>
	
	<div class="hdv_row">
		<label for="hdv_setting_invoice_start">Starting Invoice Number <span class="hdv_tooltip">?<span class="hdv_tooltip_content"><span>If you want to start numbering your invoices at 100, then enter 99 here. The first invoice you create will be invoice #100.</span></span></span></label>
		<input type="text" id="hdv_setting_invoice_start" class="hdv_input" value = "<?php echo $hdv_setting->invoice_start; ?>" placeholder="defaults to zero (0)" required="">
	</div>
	
</div>