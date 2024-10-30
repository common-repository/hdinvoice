<?php
    /* Save the settings page */

    // get and santize all settings data before saving
    $hdv_setting_currency_symbol = sanitize_text_field($_POST['hdv_setting_currency_symbol']);
    $hdv_setting_currency_position = sanitize_text_field($_POST['hdv_setting_currency_position']);
    $hdv_setting_tax_name1 = sanitize_text_field($_POST['hdv_setting_tax_name1']);
    $hdv_setting_tax_name2 = sanitize_text_field($_POST['hdv_setting_tax_name2']);
    $hdv_setting_tax_name3 = sanitize_text_field($_POST['hdv_setting_tax_name3']);
    $hdv_setting_invoice_start = intval($_POST['hdv_setting_invoice_start']); // it's ok if this reverts to zero
    $hdv_setting_name = sanitize_text_field($_POST['hdv_setting_name']);
    $hdv_setting_email = sanitize_email($_POST['hdv_setting_email']);
    $hdv_setting_website = sanitize_text_field($_POST['hdv_setting_website']);
    $hdv_setting_phone = sanitize_text_field($_POST['hdv_setting_phone']);
    $hdv_setting_address = sanitize_text_field($_POST['hdv_setting_address']);
    $hdv_setting_address2 = sanitize_text_field($_POST['hdv_setting_address2']);
    $hdv_setting_city = sanitize_text_field($_POST['hdv_setting_city']);
    $hdv_setting_state = sanitize_text_field($_POST['hdv_setting_state']);
    $hdv_setting_country = sanitize_text_field($_POST['hdv_setting_country']);
    $hdv_setting_zip = sanitize_text_field($_POST['hdv_setting_zip']);
    $hdv_setting_info = wp_kses_post($_POST['hdv_setting_info']);
    $hdv_setting_layout = sanitize_text_field($_POST['hdv_setting_layout']);
    if ($hdv_setting_layout == "" || $hdv_setting_layout == null) {
        $hdv_setting_layout == "1_col";
    }

    $hdv_setting_layout_logo = sanitize_text_field($_POST['hdv_setting_layout_logo']);
    $hdv_setting_layout_address = sanitize_text_field($_POST['hdv_setting_layout_address']);
    $hdv_setting_layout_love = sanitize_text_field($_POST['hdv_setting_layout_love']);

    // make sure that empty does not convert to zero when cast to int
    $tax_p_1 = $_POST['hdv_setting_tax_percent1'];
    $tax_p_2 = $_POST['hdv_setting_tax_percent2'];
    $tax_p_3 = $_POST['hdv_setting_tax_percent3'];
    $logo = $_POST['hdv_setting_logo'];
    if ($tax_p_1 != "" && $tax_p_1 != null) {
        $hdv_setting_tax_percent1 = intval($_POST['hdv_setting_tax_percent1']);
    } else {
        $hdv_setting_tax_percent1 = "";
    }

    if ($tax_p_2 != "" && $tax_p_2 != null) {
        $hdv_setting_tax_percent2 = intval($_POST['hdv_setting_tax_percent2']);
    } else {
        $hdv_setting_tax_percent2 = "";
    }

    if ($tax_p_3 != "" && $tax_p_3 != null) {
        $hdv_setting_tax_percent3 = intval($_POST['hdv_setting_tax_percent3']);
    } else {
        $hdv_setting_tax_percent3 = "";
    }

    if ($logo != "" && $logo != null) {
        $hdv_setting_logo = intval($_POST['hdv_setting_logo']);
    } else {
        $hdv_setting_logo = "";
    }

    // now save the settings
    update_option("hdv_setting_currency_symbol", $hdv_setting_currency_symbol);
    update_option("hdv_setting_currency_position", $hdv_setting_currency_position);
    update_option("hdv_setting_tax_name1", $hdv_setting_tax_name1);
    update_option("hdv_setting_tax_name2", $hdv_setting_tax_name2);
    update_option("hdv_setting_tax_name3", $hdv_setting_tax_name3);
    update_option("hdv_setting_tax_percent1", $hdv_setting_tax_percent1);
    update_option("hdv_setting_tax_percent2", $hdv_setting_tax_percent2);
    update_option("hdv_setting_tax_percent3", $hdv_setting_tax_percent3);
    update_option("hdv_setting_invoice_start", $hdv_setting_invoice_start);
    update_option("hdv_setting_name", $hdv_setting_name);
    update_option("hdv_setting_email", $hdv_setting_email);
    update_option("hdv_setting_website", $hdv_setting_website);
    update_option("hdv_setting_logo", $hdv_setting_logo);
    update_option("hdv_setting_phone", $hdv_setting_phone);
    update_option("hdv_setting_address", $hdv_setting_address);
    update_option("hdv_setting_address2", $hdv_setting_address2);
    update_option("hdv_setting_city", $hdv_setting_city);
    update_option("hdv_setting_state", $hdv_setting_state);
    update_option("hdv_setting_country", $hdv_setting_country);
    update_option("hdv_setting_zip", $hdv_setting_zip);
    update_option("hdv_setting_info", $hdv_setting_info);
    update_option("hdv_setting_layout", $hdv_setting_layout);
    update_option("hdv_setting_layout_logo", $hdv_setting_layout_logo);
    update_option("hdv_setting_layout_address", $hdv_setting_layout_address);
    update_option("hdv_setting_layout_love", $hdv_setting_layout_love);


    hdv_view_settings()
?>
