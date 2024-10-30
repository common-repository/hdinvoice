<?php
	/* add a new invoice */

	// get the customer id
	$hdv_customer_id = intval($_POST['hdv_customer_id']);

	// figure out the total tax rate
	$hdv_tax_percent = hdv_get_tax_percent($hdv_customer_id);
	// check if we can auto send the invoice via email
	$hdv_send_email = hdv_can_send_email($hdv_customer_id);
	echo '
	<script> 
		var hdv_tax_percent = '.$hdv_tax_percent[0].';
		var hdv_tax_percent_default = '.$hdv_tax_percent[1].';
	</script>';

?>

<div id="save_invoice" class="hd_button2" style="display: block;">SAVE</div>
<div id = "save_new_invoice_wrapper">
	<div id = "hdv_model">
		<div id = "hdv_model_content"></div>
		<div id = "hdv_model_footer"></div>
	</div>

	<div class = "one_half">
		<div class = "hdv_row">
			<div id = "date_padding">
				&nbsp;
			</div>
			Invoice Published Date: <a class="hdv_tooltip">?<span class="hdv_tooltip_content"><span>NOTE: This is only for setting a past date and will not schedule future invoices for posting.</span></span></a>	<span id="hd_enable_date"><?php echo date("l, F j, Y");?> </span>
			<input type="date" id = "hdv_invoice_publish_date" class = "hdv_input" value="<?php echo date("Y-m-d"); ?>">		
				<?php
                    // show the date picker
                    hdv_date_picker();
                ?>			
		</div>	
	</div>

	<div class = "one_half last">
		<div class = "one_third">
			<div class="hdv_row">
				<div class = "hdv_toggle_wrap">		
					<input name="hdv_invoice_recurring" id="hdv_invoice_recurring" class="hdv_toggle hdv_toggle_disabled" type="checkbox" value="yes" disabled><label for="hdv_invoice_recurring"></label>
					<label for="hdv_invoice_recurring" class = "hdv_toggle_bot">Recurring <a class="hdv_tooltip">?<span class="hdv_tooltip_content"><span>This feature is only available to HDInvoice Pro users. You can also use this to offer split payments.</span></span></a>	</label>
				</div>
			</div>
		</div>
		<div class = "one_third">
			<div class="hdv_row">
				<div class = "hdv_toggle_wrap">		
					<input name="hdv_invoice_disable_tax" id="hdv_invoice_disable_tax" class="hdv_toggle" type="checkbox" value="yes" <?php if ($hdv_tax_percent[0] == "" || $hdv_tax_percent == null || $hdv_tax_percent[0] == 0) {echo 'checked';}?>/><label for="hdv_invoice_disable_tax"></label>
					<label for="hdv_invoice_disable_tax" class = "hdv_toggle_bot">Disable Tax</label>	
				</div>
			</div>
		</div>
		<div class = "one_third last">
			<div class="hdv_row">
				<div class = "hdv_toggle_wrap">		
					<input name="hdv_invoice_email" id="hdv_invoice_email" class="hdv_toggle <?php if($hdv_send_email == "no") { echo 'hdv_toggle_disabled'; } ?>" type="checkbox" value="yes" <?php if($hdv_send_email == "no") { echo 'disabled'; } ?>><label for="hdv_invoice_email"></label>
					<label for="hdv_invoice_email" class = "hdv_toggle_bot">Send Email <?php if($hdv_send_email == "no") { ?> <a class="hdv_tooltip hdv_tooltip_right">?<span class="hdv_tooltip_content"><span>To automatically send the invoice via email, you must have an email entered in your settings and there must be an email added to the customer.</span></span></a> <?php } ?></label>	
				</div>
			</div>
		</div>
		<div class = "clear"></div>	
	</div>
	<div class = "clear"></div>

	<hr/>

	<div class="hdv_row">
		<label for = "hdv_invoice_subtotal">Invoice Subtotal (tax will be auto calculated)</label>
		<input id="hdv_invoice_subtotal" class="hdv_input hdv_required" type="text" value = "" placeholder = "0.00"/>
	</div>

	<div class = "one_half">
		<div class="hdv_row">
			<label for = "hdv_invoice_paid">Total Amount Paid</label>
			<input id="hdv_invoice_paid" class="hdv_input" type="text" value = "" placeholder = "0.00"/>
		</div>	
	</div>
	<div class = "one_half last">
		<div class="hdv_row">
			<label for = "hdv_invoice_total">Total Amount Owed (subtotal + <span id = "tax_p"></span>% tax)</label>
			<input id="hdv_invoice_total" class="hdv_input hdv_input_disabled" type="text" value = "" placeholder = "0.00" disabled/>
		</div>	
	</div>
	<div class = "clear"></div>

	<hr/>

	<div id="hdv_add_line_item" class="hd_button hd_button_alt">Add Line Item</div>
	<div id = "hdv_line_items">

	</div>
		<div id = "hdv_line_item_subtotal">Line Item Subtotal: <span>0.00</span></div>
		<div class = "clear"></div>

	<hr/>

	<div class = "one_half">
		<div class = "hdv_row">
			<label for = "hdv_invoice_description">Invoice Description — <small>Your customer will see this</small> <a class="hdv_tooltip">?<span class="hdv_tooltip_line" style="transform: matrix(0, 0, 0, 1, 0, 0);"></span><span class="hdv_tooltip_content"><span>Use this to add any additional custom content to the invoice.</span></span></a></label>
			<textarea id = "hdv_invoice_description" class = "hdv_visual_editor"></textarea>
		</div>
	</div>
	<div class = "one_half last">
		<div class = "hdv_row">
			<label for = "hdv_invoice_note">Invoice Notes — <small>Only you can see this</small> <a class="hdv_tooltip">?<span class="hdv_tooltip_line" style="transform: matrix(0, 0, 0, 1, 0, 0);"></span><span class="hdv_tooltip_content"><span>Use this to add any personal notes to the invoice. These notes are for your own internal use only and are not visible to the customer.</span></span></a></label>
			<textarea id = "hdv_invoice_note" class = "hdv_visual_editor"></textarea>
		</div>
	</div>
	<div class = "clear"></div>
</div>