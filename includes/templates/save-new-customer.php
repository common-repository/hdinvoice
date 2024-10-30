<?php
/* save new customer */

// get and santize form data
$hdv_customer_name = sanitize_text_field($_POST['hdv_customer_name']);
$hdv_customer_email = sanitize_email($_POST['hdv_customer_email']);
$hdv_customer_website = sanitize_text_field($_POST['hdv_customer_website']);
$hdv_customer_phone = sanitize_text_field($_POST['hdv_customer_phone']);
$hdv_customer_address = sanitize_text_field($_POST['hdv_customer_address']);
$hdv_customer_address2 = sanitize_text_field($_POST['hdv_customer_address2']);
$hdv_customer_city = sanitize_text_field($_POST['hdv_customer_city']);
$hdv_customer_state = sanitize_text_field($_POST['hdv_customer_state']);
$hdv_customer_country = sanitize_text_field($_POST['hdv_customer_country']);
$hdv_customer_zip = sanitize_text_field($_POST['hdv_customer_zip']);
$tax = $_POST['hdv_customer_tax'];

// make sure that intval does not convert empty field to a zero
if ($tax != "" && $tax != null) {
    $hdv_customer_tax = intval($_POST['hdv_customer_tax']);
} else {
    $hdv_customer_tax = "";
}
$hdv_customer_info =  wp_kses_post($_POST['hdv_customer_info']);
$logo = $_POST['hdv_customer_logo'];
if ($logo != "" && $logo != null) {
    $hdv_customer_logo = intval($_POST['hdv_customer_logo']);
} else {
    $hdv_customer_logo = "";
}

// make sure the customer name was entered
if ($hdv_customer_name != "" && $hdv_customer_name != null) {

    // create the new term
    $newCustomer = wp_insert_term($hdv_customer_name, 'hdv_customer');

    // now add any additional information
    add_term_meta($newCustomer['term_id'], 'hdv_customer_name', $hdv_customer_name, false);
    add_term_meta($newCustomer['term_id'], 'hdv_customer_email', $hdv_customer_email, false);
    add_term_meta($newCustomer['term_id'], 'hdv_customer_website', $hdv_customer_website, false);
    add_term_meta($newCustomer['term_id'], 'hdv_customer_phone', $hdv_customer_phone, false);
    add_term_meta($newCustomer['term_id'], 'hdv_customer_address', $hdv_customer_address, false);
    add_term_meta($newCustomer['term_id'], 'hdv_customer_address2', $hdv_customer_address2, false);
    add_term_meta($newCustomer['term_id'], 'hdv_customer_city', $hdv_customer_city, false);
    add_term_meta($newCustomer['term_id'], 'hdv_customer_state', $hdv_customer_state, false);
    add_term_meta($newCustomer['term_id'], 'hdv_customer_country', $hdv_customer_country, false);
    add_term_meta($newCustomer['term_id'], 'hdv_customer_zip', $hdv_customer_zip, false);
    add_term_meta($newCustomer['term_id'], 'hdv_customer_tax', $hdv_customer_tax, false);
    add_term_meta($newCustomer['term_id'], 'hdv_customer_info', $hdv_customer_info, false);
    add_term_meta($newCustomer['term_id'], 'hdv_customer_logo', $hdv_customer_logo, false);

    echo '<div style = "display:none;"><span class = "hd_saved_customer_name">'.$hdv_customer_name.'</span><span class = "hd_saved_customer_id">'.$newCustomer['term_id'].'</span></div>';
    global $hdv_customer_id;
    $hdv_customer_id = $newCustomer['term_id'];
    hdv_view_customer();
} else {
    echo 'A customer name was never entered';
}
