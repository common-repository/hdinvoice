<?php
/*
    HDInvoice primary functions file
    Generic functions are located here

    NOTE: Company = Site owner, Customer = Debtor
*/

/* Include the basic required files
------------------------------------------------------- */
require(dirname(__FILE__).'/functions/admin.php'); // admin functions and layout
require(dirname(__FILE__).'/functions/actions.php'); // various ajax actions
require(dirname(__FILE__).'/functions/stats.php'); // dashboard stats
require(dirname(__FILE__).'/functions/single-invoice.php'); // invoice helper functions

/* GENERAL FUNCTIONS
 * basic helper functions below
------------------------------------------------------- */

/* Converts number into the HDV currency amount
------------------------------------------------------- */
function hdv_amount($amount)
{
    $amount = number_format(floatVal($amount), 2);
    $currencyL = "";
    $currencyR = "";
    $currency = sanitize_text_field(get_option('hdv_setting_currency_symbol'));
    $currency_position = sanitize_text_field(get_option('hdv_setting_currency_position'));
    if ($currency == "" || $currency == null) {
        $currency = "$";
    }
    if ($currency_position == "right") {
        $currency_position = "r";
    } else {
        $currency_position = "l";
    }

    if ($currency_position == "l") {
        $currencyL = $currency;
    } else {
        $currencyR = $currency;
    }
    $currency = $currencyL.$amount.$currencyR;
    return $currency;
}

/* Return the total tax percent
 * based on Global settings and Customer Info
------------------------------------------------------- */
function hdv_get_tax_percent($hdv_customer_id)
{
    $tax1 = "";
    $tax2 = "";

    // first check to see if the customer has custom tax settings
    $hdv_customer = hdv_get_customer($hdv_customer_id);
    if ($hdv_customer->tax != "" && $hdv_customer->tax != null || $hdv_customer->tax === 0) {
        $tax1 = $hdv_customer->tax;
    }

    // get the global settings
    $hdv_settings = hdv_get_settings_values();
    $tax_total = 0;
    if ($hdv_settings->tax_percent1 != "" && $hdv_settings->tax_percent1 != null) {
        $tax_total = $tax_total + $hdv_settings->tax_percent1;
    }
    if ($hdv_settings->tax_percent2 != "" && $hdv_settings->tax_percent2 != null) {
        $tax_total = $tax_total + $hdv_settings->tax_percent2;
    }
    if ($hdv_settings->tax_percent3 != "" && $hdv_settings->tax_percent3 != null) {
        $tax_total = $tax_total + $hdv_settings->tax_percent3;
    }
    $tax2 = intval($tax_total);
    if (empty($tax1) && $tax1 !== 0) {
        $tax1 = $tax2;
    }
    return array($tax1, $tax2); // set tax, default tax
}

/* Returns if sending an automatic email
 * to customer is possible
 * TODO: This function checks if we can / should send email
 * Still need to write the actual sending function and verifications
------------------------------------------------------- */
function hdv_can_send_email($hdv_customer_id)
{
    // first, check if their is an email address for the customer
    $hdv_customer = hdv_get_customer($hdv_customer_id);
    if ($hdv_customer->email != "" && $hdv_customer->email != null) {
        // check if there is a send from email address
        $hdv_settings = hdv_get_settings_values();
        if ($hdv_settings->email != "" && $hdv_settings->email != null) {
            return "yes";
        } else {
            return "no";
        }
    } else {
        return "no";
    }
}

/* Generate Invoice title / permalink
------------------------------------------------------- */
function hdv_generate_invoice_id($length)
{
    // NOTE: Technically, there is no gurantee that this is unique
    // TODO: Perhaps hash with current time?
    // Either way, odds of repeats are pretty much zero cause we add customer id to the end as well.
    $add_dashes = false;
    $available_sets = 'luds';
    $sets = array();
    if (strpos($available_sets, 'l') !== false) {
        $sets[] = 'abcdefghjkmnpqrstuvwxyz';
    }
    if (strpos($available_sets, 'u') !== false) {
        $sets[] = '1234567';
    }
    if (strpos($available_sets, 'd') !== false) {
        $sets[] = '23456789';
    }
    if (strpos($available_sets, 's') !== false) {
        $sets[] = '567890';
    }
    $all = '';
    $password = '';
    foreach ($sets as $set) {
        $password .= $set[array_rand(str_split($set))];
        $all .= $set;
    }
    $all = str_split($all);
    for ($i = 0; $i < $length - count($sets); $i++) {
        $password .= $all[array_rand($all)];
    }
    $password = str_shuffle($password);
    if (!$add_dashes) {
        return $password;
    }
    $dash_len = floor(sqrt($length));
    $dash_str = '';
    while (strlen($password) > $dash_len) {
        $dash_str .= substr($password, 0, $dash_len) . '-';
        $password = substr($password, $dash_len);
    }
    $dash_str .= $password;
    return $dash_str;
}

/* Generate Invoice Number
------------------------------------------------------- */
function hdv_generate_invoice_number()
{
    $last_invoice_number = intval(get_option("hdv_last_invoice_number"));
    if ($last_invoice_number == "" || $last_invoice_number == null || $last_invoice_number == 0) {
        // get starting invoice #
        $invoice_start = intval(get_option("hdv_setting_invoice_start"));
        return $invoice_start + 1;
    } else {
        return $last_invoice_number + 1;
    }
}

/* Get Customer Meta
------------------------------------------------------- */
function hdv_get_customer($hdv_customer_id)
{
    $hdv_customer = new \stdClass();
    $hdv_customer->name = sanitize_text_field(get_term_meta($hdv_customer_id, 'hdv_customer_name', true));
    $hdv_customer->email = sanitize_email(get_term_meta($hdv_customer_id, 'hdv_customer_email', true));
    $hdv_customer->website = sanitize_text_field(get_term_meta($hdv_customer_id, 'hdv_customer_website', true));
    $hdv_customer->phone = sanitize_text_field(get_term_meta($hdv_customer_id, 'hdv_customer_phone', true));
    $hdv_customer->address = sanitize_text_field(get_term_meta($hdv_customer_id, 'hdv_customer_address', true));
    $hdv_customer->address2 = sanitize_text_field(get_term_meta($hdv_customer_id, 'hdv_customer_address2', true));
    $hdv_customer->city = sanitize_text_field(get_term_meta($hdv_customer_id, 'hdv_customer_city', true));
    $hdv_customer->state = sanitize_text_field(get_term_meta($hdv_customer_id, 'hdv_customer_state', true));
    $hdv_customer->country = sanitize_text_field(get_term_meta($hdv_customer_id, 'hdv_customer_country', true));
    $hdv_customer->zip = sanitize_text_field(get_term_meta($hdv_customer_id, 'hdv_customer_zip', true));
    $hdv_customer->info = wp_kses_post(get_term_meta($hdv_customer_id, 'hdv_customer_info', true));

    $tax = get_term_meta($hdv_customer_id, 'hdv_customer_tax', true);
    if ($tax == "" || $tax == null) {
        $tax = "";
    } else {
        $tax = intval($tax);
    }
    $hdv_customer->tax = $tax;

    $logo = get_term_meta($hdv_customer_id, 'hdv_customer_logo', true);
    if ($logo != "" && $logo != null) {
        $logo = intval($logo);
        $attachUrl = wp_get_attachment_image_src($logo, 'full', false);
        if ($attachUrl[0] != "" && $attachUrl[0] != null) {
            $logo = $attachUrl[0];
        }
    } else {
        $logo = "https://dummyimage.com/560x250/bbbbbb/2d2d2d.gif&text=customer+logo";
    }
    $hdv_customer->logo = $logo;
    return $hdv_customer;
}

/* Get Company Meta
------------------------------------------------------- */
function hdv_get_settings_values()
{
    $hdv_setting = new \stdClass();
    $hdv_setting->currency_symbol = sanitize_text_field(get_option("hdv_setting_currency_symbol"));
    $hdv_setting->currency_position = sanitize_text_field(get_option("hdv_setting_currency_position"));
    $hdv_setting->tax_name1 = sanitize_text_field(get_option("hdv_setting_tax_name1"));
    $hdv_setting->tax_name2 = sanitize_text_field(get_option("hdv_setting_tax_name2"));
    $hdv_setting->tax_name3 = sanitize_text_field(get_option("hdv_setting_tax_name3"));
    $hdv_setting->invoice_start = intval(get_option("hdv_setting_invoice_start"));
    $hdv_setting->name = sanitize_text_field(get_option("hdv_setting_name"));
    $hdv_setting->email = sanitize_email(get_option("hdv_setting_email"));
    $hdv_setting->website = sanitize_text_field(get_option("hdv_setting_website"));
    $hdv_setting->phone = sanitize_text_field(get_option("hdv_setting_phone"));
    $hdv_setting->address = sanitize_text_field(get_option("hdv_setting_address"));
    $hdv_setting->address2 = sanitize_text_field(get_option("hdv_setting_address2"));
    $hdv_setting->city = sanitize_text_field(get_option("hdv_setting_city"));
    $hdv_setting->state = sanitize_text_field(get_option("hdv_setting_state"));
    $hdv_setting->country = sanitize_text_field(get_option("hdv_setting_country"));
    $hdv_setting->zip = sanitize_text_field(get_option("hdv_setting_zip"));
    $hdv_setting->info = wp_kses_post(get_option("hdv_setting_info"));
    $hdv_setting->layout = sanitize_text_field(get_option("hdv_setting_layout"));
    $hdv_setting->layout_logo = sanitize_text_field(get_option("hdv_setting_layout_logo"));
    $hdv_setting->layout_address = sanitize_text_field(get_option("hdv_setting_layout_address"));
    $hdv_setting->layout_love = sanitize_text_field(get_option("hdv_setting_layout_love"));
    // make sure that empty does not convert to zero when cast to int
    $tax_p_1 = get_option("hdv_setting_tax_percent1");
    $tax_p_2 = get_option("hdv_setting_tax_percent2");
    $tax_p_3 = get_option("hdv_setting_tax_percent3");
    $logo = get_option("hdv_setting_logo");
    $last_invoice_number = get_option("hdv_last_invoice_number");

    if ($last_invoice_number != "" && $last_invoice_number != null) {
        $hdv_setting->last_invoice_number = intval($last_invoice_number);
    } else {
        $hdv_setting->last_invoice_number = "";
    }

    if ($tax_p_1 != "" && $tax_p_1 != null) {
        $hdv_setting->tax_percent1 = intval($tax_p_1);
        // since there is a tax rate set, we need to make sure the name is not blank
        if ($hdv_setting->tax_name1 == "" || $hdv_setting->tax_name1 == null) {
            $hdv_setting->tax_name1 = "TAX";
        }
    } else {
        $hdv_setting->tax_percent1 = "";
    }

    if ($tax_p_2 != "" && $tax_p_2 != null) {
        $hdv_setting->tax_percent2 = intval($tax_p_2);
        // since there is a tax rate set, we need to make sure the name is not blank
        if ($hdv_setting->tax_name2 == "" || $hdv_setting->tax_name2 == null) {
            $hdv_setting->tax_name2 = "TAX";
        }
    } else {
        $hdv_setting->tax_percent2 = "";
    }

    if ($tax_p_3 != "" && $tax_p_3 != null) {
        $hdv_setting->tax_percent3 = intval($tax_p_3);
        // since there is a tax rate set, we need to make sure the name is not blank
        if ($hdv_setting->tax_name3 == "" || $hdv_setting->tax_name3 == null) {
            $hdv_setting->tax_name3 = "TAX";
        }
    } else {
        $hdv_setting->tax_percent3 = "";
    }

    if ($logo != "" && $logo != null) {
        $logo = intval($logo);
        $hdv_setting->logo_id = $logo;
        if ($logo == "https://dummyimage.com/560x250/bbbbbb/2d2d2d.gif&text=customer+logo") {
            $hdv_setting->logo_id = "";
        } else {
            $attachUrl = wp_get_attachment_image_src($logo, 'full', false);
            if ($attachUrl[0] != "" && $attachUrl[0] != null) {
                $logo = $attachUrl[0];
            } else {
                $logo = "https://dummyimage.com/560x250/bbbbbb/2d2d2d.gif&text=customer+logo";
            }
        }
    } else {
        $hdv_setting->logo_id = "";
        $logo = "https://dummyimage.com/560x250/bbbbbb/2d2d2d.gif&text=customer+logo";
    }
    $hdv_setting->logo = $logo;

    return $hdv_setting;
}

/* Get Invoice Meta
------------------------------------------------------- */
function hdv_get_invoice_values($invoice_id)
{
    // TODO: might want to also get_the_date() here as well for conistancy
    $hdv_tax_rate = get_post_meta($invoice_id, 'hdv_tax_rate', true);
    $hdv_taxes = get_post_meta($invoice_id, 'hdv_taxes', true);
    $hdv_invoice_subtotal = get_post_meta($invoice_id, 'hdv_invoice_subtotal', true);
    $hdv_invoice_paid = get_post_meta($invoice_id, 'hdv_invoice_paid', true);
    $hdv_invoice_total = get_post_meta($invoice_id, 'hdv_invoice_total', true);
    $hdv_invoice_owed = get_post_meta($invoice_id, 'hdv_invoice_owed', true);
    $hdv_invoice_description = get_post_meta($invoice_id, 'hdv_invoice_description', true);
    $hdv_invoice_note = get_post_meta($invoice_id, 'hdv_invoice_note', true);
    $hdv_line_items = get_post_meta($invoice_id, 'hdv_line_items', true);
    $hdv_invoice_number = get_post_meta($invoice_id, 'hdv_invoice_number', true);
    $hdv_invoice_state = get_post_meta($invoice_id, 'hdv_invoice_state', true);

    $hdv = new \stdClass();
    $hdv->tax_rate = sanitize_text_field($hdv_tax_rate);
    $hdv->taxes = sanitize_text_field($hdv_taxes);
    $hdv->invoice_subtotal = sanitize_text_field($hdv_invoice_subtotal);
    $hdv->invoice_paid = sanitize_text_field($hdv_invoice_paid);
    $hdv->invoice_total = sanitize_text_field($hdv_invoice_total);
    $hdv->invoice_owed = sanitize_text_field($hdv_invoice_owed);
    $hdv->invoice_description = wp_kses_post($hdv_invoice_description);
    $hdv->invoice_note = wp_kses_post($hdv_invoice_note);
    $hdv->line_items = sanitize_text_field($hdv_line_items);
    $hdv->invoice_number = sanitize_text_field($hdv_invoice_number);
    $hdv->invoice_state = sanitize_text_field($hdv_invoice_state);
    return $hdv;
}

/* Get a file's MIME type (used for importer)
------------------------------------------------------- */
function hdv_get_mime($file)
{
    if (function_exists("finfo_file")) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
        $mime = finfo_file($finfo, $file);
        finfo_close($finfo);
        return $mime;
    } elseif (function_exists("mime_content_type")) {
        return mime_content_type($file);
    } elseif (!stristr(ini_get("disable_functions"), "shell_exec")) {
        // http://stackoverflow.com/a/134930/1593459
        $file = escapeshellarg($file);
        $mime = shell_exec("file -bi " . $file);
        return $mime;
    } else {
        return false;
    }
}

/* Show the custom date picker
------------------------------------------------------- */
function hdv_date_picker()
{
    ?>
				<div class="hd_birthdate_picker_model">
					<div class="hd_birthdate_picker_model_month">
						<div class="hd_birthdate_item one_third" data-id="01">
							January
						</div>
						<div class="hd_birthdate_item one_third" data-id="02">
							February
						</div>
						<div class="hd_birthdate_item one_third last" data-id="03">
							March
						</div>
						<div class="clear"></div>
						<div class="hd_birthdate_item one_third" data-id="04">
							April
						</div>
						<div class="hd_birthdate_item one_third" data-id="05">
							May
						</div>
						<div class="hd_birthdate_item one_third last" data-id="06">
							June
						</div>
						<div class="clear"></div>
						<div class="hd_birthdate_item one_third" data-id="07">
							July
						</div>
						<div class="hd_birthdate_item one_third" data-id="08">
							August
						</div>
						<div class="hd_birthdate_item one_third last" data-id="09">
							September
						</div>
						<div class="clear"></div>
						<div class="hd_birthdate_item one_third" data-id="10">
							October
						</div>
						<div class="hd_birthdate_item one_third" data-id="11">
							November
						</div>
						<div class="hd_birthdate_item one_third last" data-id="12">
							December
						</div>
						<div class="clear"></div>
					</div>
					<div class="hd_birthdate_picker_model_day">
						<div class="hd_birthdate_item one_seventh" data-id="01">01</div>
						<div class="hd_birthdate_item one_seventh" data-id="02">02</div>
						<div class="hd_birthdate_item one_seventh" data-id="03">03</div>
						<div class="hd_birthdate_item one_seventh" data-id="04">04</div>
						<div class="hd_birthdate_item one_seventh" data-id="05">05</div>
						<div class="hd_birthdate_item one_seventh" data-id="06">06</div>
						<div class="hd_birthdate_item one_seventh last" data-id="07">07</div>
						<div class="clear"></div>
						<div class="hd_birthdate_item one_seventh" data-id="08">08</div>
						<div class="hd_birthdate_item one_seventh" data-id="09">09</div>
						<div class="hd_birthdate_item one_seventh" data-id="10">10</div>
						<div class="hd_birthdate_item one_seventh" data-id="11">11</div>
						<div class="hd_birthdate_item one_seventh" data-id="12">12</div>
						<div class="hd_birthdate_item one_seventh" data-id="13">13</div>
						<div class="hd_birthdate_item one_seventh last" data-id="14">14</div>
						<div class="clear"></div>
						<div class="hd_birthdate_item one_seventh" data-id="15">15</div>
						<div class="hd_birthdate_item one_seventh" data-id="16">16</div>
						<div class="hd_birthdate_item one_seventh" data-id="17">17</div>
						<div class="hd_birthdate_item one_seventh" data-id="18">18</div>
						<div class="hd_birthdate_item one_seventh" data-id="19">19</div>
						<div class="hd_birthdate_item one_seventh" data-id="20">20</div>
						<div class="hd_birthdate_item one_seventh last" data-id="21">21</div>
						<div class="clear"></div>
						<div class="hd_birthdate_item one_seventh" data-id="22">22</div>
						<div class="hd_birthdate_item one_seventh" data-id="23">23</div>
						<div class="hd_birthdate_item one_seventh" data-id="24">24</div>
						<div class="hd_birthdate_item one_seventh" data-id="25">25</div>
						<div class="hd_birthdate_item one_seventh" data-id="26">26</div>
						<div class="hd_birthdate_item one_seventh" data-id="27">27</div>
						<div class="hd_birthdate_item one_seventh last" data-id="28">28</div>
						<div class="clear"></div>
						<div class="hd_birthdate_item one_seventh" data-id="29">29</div>
						<div class="hd_birthdate_item one_seventh" data-id="30">30</div>
						<div class="hd_birthdate_item one_seventh" data-id="31">31</div>
						<div class="one_seventh"></div>
						<div class="one_seventh"></div>
						<div class="one_seventh"></div>
						<div class="one_seventh last"></div>
						<div class="clear"></div>
					</div>
					<div class="hd_birthdate_picker_model_year"></div>
				</div>
	<?php
}
?>
