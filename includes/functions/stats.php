<?php
/*
    HDInvoice stats functions file
    Generic functions for retrieving or saving invoice stats
*/


/* Updates order stats option
 * this is used when *creating* a new invoice
------------------------------------------------------- */
function hdv_update_invoice_stats($amount, $date)
{
    $amount = floatVal($amount);
    $hdv_invoice_stats = sanitize_text_field(get_option("hdv_invoice_stats"));

    if ($hdv_invoice_stats != "" && $hdv_invoice_stats != null) {
        // read JSON
        $hdv_invoice_stats = stripslashes($hdv_invoice_stats);
        $hdv_invoice_stats = json_decode(html_entity_decode($hdv_invoice_stats), true);
        // add this order to exising month, or push new array for month
        $existing_month = false;

        $counter = 0;
        foreach ($hdv_invoice_stats as $value) {
            if ((string)$date == (string)$value[0]) {
                // there is an existing month
                $amountO = floatVal($hdv_invoice_stats[$counter][1]);
                $amount = floatVal($amountO + $amount);
                $hdv_invoice_stats[$counter][1] = $amount;
                $orders = $hdv_invoice_stats[$counter][2];
                $hdv_invoice_stats[$counter][2] = $orders + 1;
                $existing_month = true;
            }
            $counter = $counter + 1;
        }

        if ($existing_month == false) {
            // this is a new month - push the new month array
            array_push($hdv_invoice_stats, array($date, floatVal($amount),1));
        }

        $hdv_invoice_stats = json_encode($hdv_invoice_stats);
        update_option("hdv_invoice_stats", $hdv_invoice_stats);
    } else {
        // store the date (m-Y) and the amount of the order
        $hdv_invoice_stats = array(array($date, floatVal($amount),1));
        $hdv_invoice_stats = json_encode($hdv_invoice_stats);
        update_option("hdv_invoice_stats", $hdv_invoice_stats);
    }
}

/* Updates order stats option
 * this is used when *editing* an existing invoice
------------------------------------------------------- */
function hdv_update_invoice_month_stat($amount, $diff, $date, $increase)
{
    $amount = floatVal($amount);
    $diff = floatVal($diff);
    $hdv_invoice_stats = sanitize_text_field(get_option("hdv_invoice_stats"));

    if ($hdv_invoice_stats != "" && $hdv_invoice_stats != null) {
        // read JSON
        $hdv_invoice_stats = stripslashes($hdv_invoice_stats);
        $hdv_invoice_stats = json_decode(html_entity_decode($hdv_invoice_stats), true);
        // add this order to exising month, or push new array for month
        $existing_month = false;

        $counter = 0;
        foreach ($hdv_invoice_stats as $value) {
            if ((string)$date == (string)$value[0]) {
                // there is an existing month
                if ($increase) {
                    // we are adding value
                    $amountO = floatVal($hdv_invoice_stats[$counter][1]);
                    $amount = floatVal($amountO + $diff);
                    $hdv_invoice_stats[$counter][1] = $amount;
                } else {
                    // we are subtracting value
                    $amountO = floatVal($hdv_invoice_stats[$counter][1]);
                    $amount = floatVal($amountO - $diff);
                    $hdv_invoice_stats[$counter][1] = $amount;
                }
                $existing_month = true;
            }
            $counter = $counter + 1;
        }

        if ($existing_month == false) {
            // this is a new month - push the new month array
            array_push($hdv_invoice_stats, array($date, floatVal($amount),1)); // using $amount instead of $diff
        }
        $hdv_invoice_stats = json_encode($hdv_invoice_stats);
        update_option("hdv_invoice_stats", $hdv_invoice_stats);
    }
}

/* Gets the dashboard stats
------------------------------------------------------- */
function hdv_get_stats()
{
    $hdv_invoice_stats = sanitize_text_field(get_option("hdv_invoice_stats"));
    $chart_data = array();
    $chart_data2 = array();
    $this_year = date("Y");
    $this_month = date("m");

    $hdv_stats = new \stdClass();
    $hdv_stats->jan = $hdv_stats->feb = $hdv_stats->mar = $hdv_stats->apr = $hdv_stats->may = $hdv_stats->jun = $hdv_stats->jul = $hdv_stats->aug = $hdv_stats->sep = $hdv_stats->oct = $hdv_stats->nov = $hdv_stats->dec = $hdv_stats->this_month = 0;
    $hdv_stats->jan2 = $hdv_stats->feb2 = $hdv_stats->mar2 = $hdv_stats->apr2 = $hdv_stats->may2 = $hdv_stats->jun2 = $hdv_stats->jul2 = $hdv_stats->aug2 = $hdv_stats->sep2 = $hdv_stats->oct2 = $hdv_stats->nov2 = $hdv_stats->dec2  = 0;

    $hdv_invoice_total = 0;
    $hdv_stats->invoice_total = 0;
    if ($hdv_invoice_stats != "" && $hdv_invoice_stats != null) {
        $hdv_invoice_stats = stripslashes($hdv_invoice_stats);
        $hdv_invoice_stats = json_decode(html_entity_decode($hdv_invoice_stats), true);
        foreach ($hdv_invoice_stats as $value) {
            $date = explode("-", $value[0]);
            $month = $date[1];
            $year = $date[0];
            // only grab stats from the current year
            if ($year == $this_year) {
                if ($month == $this_month) {
                    $hdv_stats->this_month = $value[2];
                }
                $amount = floatVal($value[1]);
                array_push($chart_data, array($month, $amount));
            } elseif ($year == $this_year - 1) {
                $amount = floatVal($value[1]);
                array_push($chart_data2, array($month, $amount));
            }
            // add all of the totals together to get the invoice_total
            $hdv_invoice_total = $hdv_invoice_total + $value[1];
        }
        $hdv_stats->invoice_total = $hdv_invoice_total;
    }

    // now we loop through the array and create data for each month
    foreach ($chart_data as $value) {
        if ($value[0] == "01") {
            $hdv_stats->jan = $value[1];
        }
        if ($value[0] == "02") {
            $hdv_stats->feb = $value[1];
        }
        if ($value[0] == "03") {
            $hdv_stats->mar = $value[1];
        }
        if ($value[0] == "04") {
            $hdv_stats->apr = $value[1];
        }
        if ($value[0] == "05") {
            $hdv_stats->may = $value[1];
        }
        if ($value[0] == "06") {
            $hdv_stats->jun = $value[1];
        }
        if ($value[0] == "07") {
            $hdv_stats->jul = $value[1];
        }
        if ($value[0] == "08") {
            $hdv_stats->aug = $value[1];
        }
        if ($value[0] == "09") {
            $hdv_stats->sep = $value[1];
        }
        if ($value[0] == "10") {
            $hdv_stats->oct = $value[1];
        }
        if ($value[0] == "11") {
            $hdv_stats->nov = $value[1];
        }
        if ($value[0] == "12") {
            $hdv_stats->dec = $value[1];
        }
    }

    foreach ($chart_data2 as $value) {
        if ($value[0] == "01") {
            $hdv_stats->jan2 = $value[1];
        }
        if ($value[0] == "02") {
            $hdv_stats->feb2 = $value[1];
        }
        if ($value[0] == "03") {
            $hdv_stats->mar2 = $value[1];
        }
        if ($value[0] == "04") {
            $hdv_stats->apr2 = $value[1];
        }
        if ($value[0] == "05") {
            $hdv_stats->may2 = $value[1];
        }
        if ($value[0] == "06") {
            $hdv_stats->jun2 = $value[1];
        }
        if ($value[0] == "07") {
            $hdv_stats->jul2 = $value[1];
        }
        if ($value[0] == "08") {
            $hdv_stats->aug2 = $value[1];
        }
        if ($value[0] == "09") {
            $hdv_stats->sep2 = $value[1];
        }
        if ($value[0] == "10") {
            $hdv_stats->oct2 = $value[1];
        }
        if ($value[0] == "11") {
            $hdv_stats->nov2 = $value[1];
        }
        if ($value[0] == "12") {
            $hdv_stats->dec2 = $value[1];
        }
    }
    return $hdv_stats;
}
