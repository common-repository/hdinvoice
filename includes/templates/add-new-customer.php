<?php
    /* add a new customer */
?>

<div id="save_customer" class="hd_button2" style="display: block;">SAVE</div>

<div class = "one_half">
	<div class = "hdv_row">
		<label for = "hdv_customer_name">Customer Name</label>
		<input type = "text" id = "hdv_customer_name" class = "hdv_input hdv_required" placeholder = "enter customer name" required/>
	</div>
	<div class = "hdv_row">
		<label for = "hdv_customer_email">Customer Email <span class="hdv_tooltip">?<span class="hdv_tooltip_content"><span>Needed to automatically send the invoice to the customer.</span></span></span></label>
		<input type = "email" id = "hdv_customer_email" class = "hdv_input" placeholder = "enter customer email"/>
	</div>
	<div class = "hdv_row">
		<label for = "hdv_customer_website">Customer Website</label>
		<input type = "text" id = "hdv_customer_website" class = "hdv_input" placeholder = "website" pattern="https?://.+"/>
	</div>
</div>
<div class = "one_half last">
	<div class = "hdv_row">
		<label>Customer Logo</label>
		<div class="hd_inv_settings_option">
			<div id="hdv_customer_logo_wrap">
				<img id="hdv_company_logo_img" src="https://dummyimage.com/560x250/bbbbbb/2d2d2d.gif&amp;text=customer+logo" data-attachment-id = "" class="hdv_upload" alt="upload customer logo">
			</div>
		</div>
	</div>
</div>
<div class = "clear"></div>

<hr/>

<div class = "hdv_row">
	<label for = "hdv_customer_phone">Phone Number</label>
	<input type = "tel" id = "hdv_customer_phone" class = "hdv_input" placeholder = "enter phone number"/>
</div>

<div class = "hdv_row">
	<label for = "hdv_customer_address">Address</label>
	<input type = "text" id = "hdv_customer_address" class = "hdv_input" placeholder = "enter address"/>
</div>

<div class = "hdv_row">
	<label for = "hdv_customer_address2">Address Line 2</label>
	<input type = "text" id = "hdv_customer_address2" class = "hdv_input" placeholder = "enter address 2"/>
</div>

<div class = "hdv_row">
	<label for = "hdv_customer_city">City</label>
	<input type = "text" id = "hdv_customer_city" class = "hdv_input" placeholder = "enter city"/>
</div>

<div class = "hdv_row">
	<label for = "hdv_customer_state">Province/State</label>
	<input type = "text" id = "hdv_customer_state" class = "hdv_input" placeholder = "enter state or province"/>
</div>

<div class = "hdv_row">
	<label for = "hdv_customer_country">Country</label>
	<input type = "text" id = "hdv_customer_country" class = "hdv_input" placeholder = "enter country"/>
</div>

<div class = "hdv_row">
	<label for = "hdv_customer_zip">Postal/ZIP code</label>
	<input type = "text" id = "hdv_customer_zip" class = "hdv_input" placeholder = "enter postal/ZIP code"/>
</div>

<hr/>

<div class = "hdv_row">
	<label for = "hdv_customer_tax">Custom Tax Rate <span class="hdv_tooltip">?<span class="hdv_tooltip_content"><span>Enter a tax percent if you need to charge this customer a custom tax rate. Leave blank to use the global tax settings</span></span></span></label>
	<input type = "email" id = "hdv_customer_tax" class = "hdv_input" placeholder = "enter custom tax rate"/>
</div>
<div class = "hdv_row">
	<label for = "hdv_customer_info">Customer Extra Info <span class="hdv_tooltip">?<span class="hdv_tooltip_content"><span>Enter any personal notes or information on the customer here. These notes are for your use only and are not visible to the customer</span></span></span></label>
	<textarea id = "hdv_customer_info" class = "hdv_visual_editor"></textarea>
</div>
