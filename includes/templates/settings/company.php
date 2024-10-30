<?php
	/* Settings Company */
	global $hdv_setting;
?>
    <div id="hdv_settings_company" class="hdv_tab">
        <h3>Your Options</h3>
        <p>The following settings are the details of your company that will show on each invoice.</p>
        <!--
			@hdv_setting_name
			@hdv_setting_email
			@hdv_setting_website
			@hdv_setting_logo
		-->
        <div class="one_half">
            <div class="hdv_row">
                <label for="hdv_setting_name">Company Name</label>
                <input type="text" id="hdv_setting_name" class="hdv_input" value = "<?php echo $hdv_setting->name; ?>" placeholder="enter customer name" required/>
            </div>
            <div class="hdv_row">
                <label for="hdv_setting_email">Company Email <span class="hdv_tooltip">?<span class="hdv_tooltip_content"><span>This address will be used as the 'Reply-To' field in all sent invoices.</span></span></span></label>
                <input type="email" id="hdv_setting_email" class="hdv_input" value = "<?php echo $hdv_setting->email; ?>" placeholder="enter invoice email" />
            </div>
            <div class="hdv_row">
                <label for="hdv_setting_website">Company Website</label>
                <input type="text" id="hdv_setting_website" class="hdv_input" value = "<?php echo $hdv_setting->website; ?>" placeholder="website" pattern="https?://.+" />
            </div>
        </div>
        <div class="one_half last">
            <div class="hdv_row">
                <label>Company Logo</label>
                <div class="hd_inv_settings_option">
                    <div id="hdv_setting_logo_wrap">
                        <img id="hdv_company_logo_img" src="<?php echo $hdv_setting->logo; ?>" data-attachment-id="<?php echo $hdv_setting->logo_id; ?>" class="hdv_upload" alt="">
                    </div>
                </div>
            </div>
        </div>
        <div class="clear"></div>
        <hr/>
        <!--
			@hdv_setting_address
			@hdv_setting_address2
			@hdv_setting_city
			@hdv_setting_state
			@hdv_setting_country
			@hdv_setting_zip
		-->
        <div class="hdv_row">
            <label for="hdv_setting_phone">Phone Number</label>
            <input type="tel" id="hdv_setting_phone" class="hdv_input hdv_required" value = "<?php echo $hdv_setting->phone; ?>" placeholder="enter phone number" />
        </div>
        <div class="hdv_row">
            <label for="hdv_setting_address">Address</label>
            <input type="text" id="hdv_setting_address" class="hdv_input" value = "<?php echo $hdv_setting->address; ?>" placeholder="enter address" />
        </div>
        <div class="hdv_row">
            <label for="hdv_setting_address2">Address Line 2</label>
            <input type="text" id="hdv_setting_address2" class="hdv_input" value = "<?php echo $hdv_setting->address2; ?>" placeholder="enter address 2" />
        </div>
        <div class="hdv_row">
            <label for="hdv_setting_city">City</label>
            <input type="text" id="hdv_setting_city" class="hdv_input" value = "<?php echo $hdv_setting->city; ?>" placeholder="enter city" />
        </div>
        <div class="hdv_row">
            <label for="hdv_setting_state">Province/State</label>
            <input type="text" id="hdv_setting_state" class="hdv_input" value = "<?php echo $hdv_setting->state; ?>" placeholder="enter state or province" />
        </div>
        <div class="hdv_row">
            <label for="hdv_setting_country">Country</label>
            <input type="text" id="hdv_setting_country" class="hdv_input" value = "<?php echo $hdv_setting->country; ?>" placeholder="enter country" />
        </div>
        <div class="hdv_row">
            <label for="hdv_setting_zip">Postal/ZIP code</label>
            <input type="text" id="hdv_setting_zip" class="hdv_input" value = "<?php echo $hdv_setting->zip; ?>" placeholder="enter postal/ZIP code" />
        </div>
        <hr/>

        <div class="hdv_row">
            <label for="hdv_setting_info">Your Extra Info</label>
            <p>This information will be displayed at the bottom of every invoice. Good ideas are to put in your TAX/VAT/HST/Business number as well as any other information you want your customers to have such as a thank you message.</p>
            <textarea id="hdv_setting_info" class="hdv_visual_editor"><?php echo $hdv_setting->info; ?></textarea>
			<pre><?php 
echo apply_filters('the_excerpt', $hdv_setting->info ); 
?></pre>
        </div>
    </div>