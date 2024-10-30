<?php
    /* save a new invoice */

    // get the customer id
    $hdv_customer_id = intval($_POST['hdv_customer_id']);

    // get global settings
    $hdv_setting = hdv_get_settings_values();

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
    if ($hdv_invoice_paid == 0) {
        $hdv_invoice_state = "unpaid";
    } else {
        if ($hdv_invoice_paid == $hdv_total) {
            $hdv_invoice_state = "paid";
        } else {
            $hdv_invoice_state = "partial";
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
        $postTitle = hdv_generate_invoice_id(12);
        $postTitle = $postTitle.'_'.$hdv_customer_id;

        // save the invoice - name and editor
        $post_information = array(
            'post_title' => $postTitle,
            'post_content' => '', // post_content is required, so we leave blank
            'post_type' => 'hdv_invoice',
            'post_status' => 'publish'
        );
        $post_id = wp_insert_post($post_information);

        // now we save custom meta to the published invoice
        // add_post_meta($post_id, 'hdv_invoice_publish_date', $hdv_invoice_publish_date, true);
        add_post_meta($post_id, 'hdv_tax_rate', $hdv_tax_rate, true);
        add_post_meta($post_id, 'hdv_taxes', $hdv_taxes, true);
        add_post_meta($post_id, 'hdv_invoice_subtotal', $hdv_invoice_subtotal, true);
        add_post_meta($post_id, 'hdv_invoice_total', $hdv_total, true);
        add_post_meta($post_id, 'hdv_invoice_paid', $hdv_invoice_paid, true);
        add_post_meta($post_id, 'hdv_invoice_owed', $hdv_invoice_total, true);
        add_post_meta($post_id, 'hdv_invoice_description', $hdv_invoice_description, true);
        add_post_meta($post_id, 'hdv_invoice_note', $hdv_invoice_note, true);
        add_post_meta($post_id, 'hdv_line_items', $hdv_line_items, true);
        add_post_meta($post_id, 'hdv_invoice_state', $hdv_invoice_state, true);

        // need to figure out what the semantic invoice number should be
        if ($hdv_setting->last_invoice_number != "") {
            $hdv_invoice_number = intval($hdv_setting->last_invoice_number) + 1;
        } else {
            // this is the first ever created invoice
            $hdv_invoice_number = intval($hdv_setting->invoice_start) + 1;
        }
        add_post_meta($post_id, 'hdv_invoice_number', $hdv_invoice_number, true);
        update_option("hdv_last_invoice_number", $hdv_invoice_number); // update the plugin setting as well

        // now we need to add the invoice to the customer
        wp_set_post_terms($post_id, $hdv_customer_id, "hdv_customer");

        // check to see if the published date was modified
        $today = date("Y-m-d");
        if ($today != $hdv_invoice_publish_date) {
            echo '<h2>Different Date</h2>';
            $post = array();
            $post['ID'] = $post_id;
            // update published date if it was changed
            $post['post_date' ] = $hdv_invoice_publish_date;
            wp_update_post($post);
        }

        // create dashboard stats

        $hdv_invoice_publish_date = explode("-", $hdv_invoice_publish_date);
        $month = $hdv_invoice_publish_date[1];
        $year = $hdv_invoice_publish_date[0];
        $date = $year."-".$month;
        hdv_update_invoice_stats($hdv_invoice_subtotal, $date);
    }


    hdv_view_customer($hdv_customer_id);
