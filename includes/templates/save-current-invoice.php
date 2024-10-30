<?php
    /* save current invoice */

    // get the customer and invoice id
    $hdv_customer_id = intval($_POST['hdv_customer_id']);
    $hdv_invoice_id = intval($_POST['hdv_invoice_id']);

    // get global settings
    $hdv_setting = hdv_get_settings_values();

    // get the original total to compare for stats
    $hdv_original_subtotal = floatVal(get_post_meta($hdv_invoice_id, 'hdv_invoice_subtotal', true));

    // get invoice data and sanitize
    $hdv_invoice_publish_date = sanitize_text_field($_POST['hdv_invoice_publish_date']);
    $hdv_tax_rate = sanitize_text_field($_POST['hdv_tax_rate']);
    $hdv_invoice_subtotal = sanitize_text_field($_POST['hdv_invoice_subtotal']);
    $hdv_invoice_paid = sanitize_text_field($_POST['hdv_invoice_paid']);
    if ($hdv_invoice_paid == "" || $hdv_invoice_paid == null) {
        $hdv_invoice_paid = 0;
    }
    $hdv_invoice_total = sanitize_text_field($_POST['hdv_invoice_total']);
    $hdv_invoice_description = wp_kses_post($_POST['hdv_invoice_description']);
    $hdv_invoice_note = wp_kses_post($_POST['hdv_invoice_note']);
    $hdv_line_items = sanitize_text_field($_POST['hdv_line_items']);

    // figure out invoice paid state
    $hdv_total_tax = $hdv_tax_rate / 100 * $hdv_invoice_subtotal;
    $hdv_total = $hdv_invoice_subtotal + $hdv_total_tax;
    $hdv_total = number_format($hdv_total, 2, '.', '');


    $hdv_invoice_void = sanitize_text_field($_POST['hdv_invoice_void']);
    if ($hdv_invoice_void == "void") {
        $hdv_invoice_state = "void";
    } else {
        if ($hdv_invoice_paid == 0) {
            $hdv_invoice_state = "unpaid";
        } else {
            if ($hdv_invoice_paid == $hdv_total) {
                $hdv_invoice_state = "paid";
            } else {
                $hdv_invoice_state = "partial";
            }
        }
    }

    // figure out if we are using the global tax settings,
    // custom customer tax, or no tax
    $hdv_taxes = array();
    if ($hdv_tax_rate == 0 || $hdv_tax_rate == null) {
        // the tax is disabled, set taxes as default and zero
        $hdv_taxes = array(array("TAX", $hdv_tax_rate));
    } else {
        // get the customer tax rate and the global tax rate
        $hdv_tax = hdv_get_tax_percent($hdv_customer_id);
        if ($hdv_tax[0] == $hdv_tax_rate && $hdv_tax[0] != $hdv_tax[1]) {
            // we are using custom customer tax rate
            $hdv_taxes = array(array("TAX", $hdv_tax_rate));
        } else {
            // we are using default global tax settings
            if ($hdv_setting->tax_percent1 != "" && $hdv_setting->tax_percent1 != null && $hdv_setting->tax_percent1 != 0) {
                array_push($hdv_taxes, array($hdv_setting->tax_name1, $hdv_setting->tax_percent1));
            }
            if ($hdv_setting->tax_percent2 != "" && $hdv_setting->tax_percent2 != null && $hdv_setting->tax_percent2 != 0) {
                array_push($hdv_taxes, array($hdv_setting->tax_name2, $hdv_setting->tax_percent2));
            }
            if ($hdv_setting->tax_percent3 != "" && $hdv_setting->tax_percent3 != null && $hdv_setting->tax_percent3 != 0) {
                array_push($hdv_taxes, array($hdv_setting->tax_name3, $hdv_setting->tax_percent3));
            }
        }
    }
    $hdv_taxes = json_encode($hdv_taxes);

    if ($hdv_invoice_subtotal != "" && $hdv_invoice_subtotal != null) {
        // now we save custom meta to the published invoice
        update_post_meta($hdv_invoice_id, 'hdv_tax_rate', $hdv_tax_rate);
        update_post_meta($hdv_invoice_id, 'hdv_taxes', $hdv_taxes);
        update_post_meta($hdv_invoice_id, 'hdv_invoice_subtotal', $hdv_invoice_subtotal);
        update_post_meta($hdv_invoice_id, 'hdv_invoice_paid', $hdv_invoice_paid);
        update_post_meta($hdv_invoice_id, 'hdv_invoice_total', $hdv_total);
        update_post_meta($hdv_invoice_id, 'hdv_invoice_owed', $hdv_invoice_total);
        update_post_meta($hdv_invoice_id, 'hdv_invoice_description', $hdv_invoice_description);
        update_post_meta($hdv_invoice_id, 'hdv_invoice_note', $hdv_invoice_note);
        update_post_meta($hdv_invoice_id, 'hdv_line_items', $hdv_line_items);
        update_post_meta($hdv_invoice_id, 'hdv_invoice_state', $hdv_invoice_state);

        // update published date
        $post = array();
        $post['ID'] = $hdv_invoice_id;
        $post['post_date' ] = $hdv_invoice_publish_date;
        wp_update_post($post);

        // update dashboard stats

        $hdv_invoice_publish_date = explode("-", $hdv_invoice_publish_date);
        $month = $hdv_invoice_publish_date[1];
        $year = $hdv_invoice_publish_date[0];
        $date = $year."-".$month;

        if ($hdv_invoice_subtotal > $hdv_original_subtotal) {
            // the new total adds value
            $hdv_diff = $hdv_invoice_subtotal - $hdv_original_subtotal;
            $hdv_invoice_total_stats = $hdv_invoice_total_stats + $hdv_diff;
            hdv_update_invoice_month_stat($hdv_invoice_subtotal, $hdv_diff, $date, true);
        } elseif ($hdv_invoice_subtotal < $hdv_original_subtotal) {
            // the new total subtracts value
            $hdv_diff = $hdv_original_subtotal - $hdv_invoice_subtotal;
            $hdv_invoice_total_stats = $hdv_invoice_total_stats - $hdv_diff;
            hdv_update_invoice_month_stat($hdv_invoice_subtotal, $hdv_diff, $date, false);
        }
    }
    hdv_view_customer($hdv_customer_id);
