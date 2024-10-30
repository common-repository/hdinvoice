<?php
    /* save bulk invoices invoice */

    $counter = 0; // row we are at
    $at_a_time = 10; // total invoices to add at a time
    $x = 1; // counter for the loop

    // get updated counter via ajax POST if exists
    if (isset($_POST["counter"])) {
        $counter = intval($_POST["counter"]);
    }
    if (isset($_POST["hdv_csv_path"])) {
        $hdv_csv_path = sanitize_text_field($_POST["hdv_csv_path"]);
    } else {
        $hdv_csv_path = null;
    }

    // get global settings
    $hdv_setting = hdv_get_settings_values();

    // load the CSV file
    $csvAsArray = array_map(function ($v) {
        return str_getcsv($v, ";", "@");
    }, file($hdv_csv_path));
    // get toal rows
    $total_to_import = sizeof($csvAsArray);
    // make sure we stop if we've reached the end of the file
    if ($total_to_import >= $counter) {
        while ($x <= $at_a_time) {
            // grab the sanitized row data
            $data = sanitize_import_data($csvAsArray[$counter]);
            // only continue if the invoice has a customer name
            if ($data[1] != "" && $data[1] != null) {
                // if invalid invoice number
                if (!is_numeric($data[0]) || $data[0] == "" || $data[0] == null || $data[0] == 0) {
                    $data[0] = hdv_generate_invoice_number();
                }
                // check to see if the customer already exists
                $customer = check_customer_exist($data[1]);
                if ($customer == 0 || $customer == null) {
                    $customer = wp_insert_term($data[1], 'hdv_customer');
                    add_term_meta($customer['term_id'], 'hdv_customer_name', $data[1], false);
                    $customer = $customer['term_id'];
                }
                // now we create the invoice
                $postTitle = hdv_generate_invoice_id(12);
                $postTitle = $postTitle.'_'.$customer;
                // save the invoice - name and editor
                $post_information = array(
                    'post_title' => $postTitle,
                    'post_content' => '', // post_content is required, so we leave blank
                    'post_type' => 'hdv_invoice',
                    'post_status' => 'publish'
                );
                $post_id = wp_insert_post($post_information);

                // figure out invoice status
                $hdv_invoice_state = "unpaid";
                if ($data[3] <= $data[4]) {
                    $hdv_invoice_state = "paid";
                } elseif ($data[4] > 0) {
                    $hdv_invoice_state = "partial";
                }
                // figure out the tax rate
                if ($data[3] == $data[2]) {
                    $hdv_tax_rate = 0;
                } else {
                    $tax_diff = $data[3] - $data[2];
                    $hdv_tax_rate = ($tax_diff / $data[2]) * 100;
                }

                $owed = 0;
                if ($data[3] > $data[4]) {
                    $owed = $data[3] - $data[4];
                }

                // figure out if we are using the global tax settings,
                // custom customer tax, or no tax
                $hdv_taxes = array();
                if ($hdv_tax_rate == 0 || $hdv_tax_rate == null) {
                    // the tax is disabled, set taxes as default and zero
                    $hdv_taxes = array(array("TAX", $hdv_tax_rate));
                } else {
                    // get the customer tax rate and the global tax rate
                    $hdv_tax = hdv_get_tax_percent($customer);
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

                // now we save custom meta to the published invoice
                add_post_meta($post_id, 'hdv_tax_rate', $hdv_tax_rate, true);
                add_post_meta($post_id, 'hdv_taxes', $hdv_taxes, true);
                add_post_meta($post_id, 'hdv_invoice_subtotal', $data[2], true);
                add_post_meta($post_id, 'hdv_invoice_paid', $data[4], true);
                add_post_meta($post_id, 'hdv_invoice_total', $data[3], true);
                add_post_meta($post_id, 'hdv_invoice_owed', $owed, true);
                add_post_meta($post_id, 'hdv_invoice_description', urldecode($data[7]), true);
                add_post_meta($post_id, 'hdv_invoice_note', $data[8], true);
                add_post_meta($post_id, 'hdv_line_items', $data[6], true);
                add_post_meta($post_id, 'hdv_invoice_state', $hdv_invoice_state, true);
                add_post_meta($post_id, 'hdv_invoice_number', $data[0], true);

                // update plugin invoice number if this one is larger
                $last_invoice_number = intval(get_option("hdv_last_invoice_number"));
                if ($data[0] > $last_invoice_number) {
                    update_option("hdv_last_invoice_number", $data[0]);
                }

                // now we need to add the invoice to the customer
                wp_set_post_terms($post_id, $customer, "hdv_customer");

                // publish date
                $today = strval(date("Y-m-d"));
                if ($data[5] == "" || $data[5] == null) {
                    $data[5] = $today;
                }

                if (strval($today) != strval($data[5])) {
                    // set the post date
                    $post = array();
                    $post['ID'] = $post_id;
                    // set published date
                    $post['post_date' ] = $data[5];
                    wp_update_post($post);
                }
                // create dashboard stats
                $hdv_invoice_publish_date = explode("-", $data[5]);
                $month = $hdv_invoice_publish_date[1];
                $year = $hdv_invoice_publish_date[0];
                $date = $year."-".$month;
                hdv_update_invoice_stats($data[2], $date);
            }
            $x++;
            $counter++;
        }

        if ($counter < $total_to_import) {
            echo '<p style = "text-align:center;">'.$counter.' / '.$total_to_import.' invoices added so far... please wait while HDInvoice uploads the rest.</p>';
        } else {
            echo '<h2>All Invoices have been uploaded</h2><p>This page will refresh in 5 seconds...</p>';
        }
        if ($counter == 10) {
            echo '<h3 style = "text-align:center;">Please wait while invoices are uploaded. You will be notified upon completion</h3>';
        }
        echo '<script>
			function start_hdv_continue_import(){hdv_continue_import('.$counter.');}setTimeout(start_hdv_continue_import, 1000);
			var hdv_csv_path = "'.$hdv_csv_path.'";
			</script>';
    } else {
        echo '<h2>All Invoices have been uploaded</h2><p>This page will refresh in 5 seconds...</p>';
    }

    function check_customer_exist($customer)
    {
        $term = term_exists($customer, 'hdv_customer');
        if ($term !== 0 && $term !== null) {
            // customer exists
            $term = $term['term_id'];
        }
        return $term;
    }


    function sanitize_import_data($data)
    {
        // get and sanitize data
        if (isset($data[0])) {
            $data0 = intVal($data[0]);
        } else {
            $data0 = "";
        }
        if (isset($data[1])) {
            $data1 = sanitize_text_field($data[1]);
        } else {
            $data1 = "";
        }
        if (isset($data[2])) {
            $data2 = floatVal($data[2]);
        } else {
            $data2 = "";
        }
        if (isset($data[3])) {
            $data3 = floatVal($data[3]);
        } else {
            $data3 = "";
        }
        if (isset($data[4])) {
            $data4 = floatVal($data[4]);
        } else {
            $data4 = "";
        }
        if (isset($data[5])) {
            $data5 = sanitize_text_field($data[5]);
        } else {
            $data5 = "";
        }
        if (isset($data[6])) {
            $data6 = sanitize_text_field($data[6]);
        } else {
            $data6 = "";
        }
        if (isset($data[7])) {
            $data7 = wp_kses_data($data[7]);
        } else {
            $data7 = "";
        }
        if (isset($data[8])) {
            $data8 = wp_kses_data($data[8]);
        } else {
            $data8 = "";
        }
        return array($data0, $data1, $data2, $data3, $data4, $data5, $data6, $data7, $data8 );
    }
