<?php
    // export invoice data
?>

<h3>Use this tool to export your invoices to a CSV file that can be imported into most other services.</h3>

<p>This can take a while depending on the power of your server and the amount of invoices to export.</p>
<p><strong>The fields exported are</strong>: Invoice ID, Customer Name, Subtotal, Total, Amount Paid, Publish Date, Line Items, Description, and Notes. The file uses a semicolon ";" as a delimiter, and the at symbol "@" for strings.</p>

<?php
    // TODO: change this to ajax so that the user gets info while csv generates
    // also create a date range selection

    // create the first line
    $line = "Invoice ID;Customer Name;Subtotal;Total;Amount Paid;Publish Date;Line Items;Description;Notes\n";

    // WP_Query arguments
    $args = array(
        'post_type'              => array( 'hdv_invoice' ),
        'nopaging'               => true,
    );

    // The Query
    $query = new WP_Query($args);

    // The Loop
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();

            // get invoice data
            $invoice = hdv_get_invoice_values(get_the_ID());
            // get customer info
            $customer = get_the_terms(get_the_ID(), 'hdv_customer');
            $hdv_customer_id = $customer[0]->term_id;
            $hdv_customer = hdv_get_customer($hdv_customer_id);

            $data = [];
            $data[0] = $invoice->invoice_number;
            $data[1] = "@".$hdv_customer->name."@";
            $data[2] = $invoice->invoice_subtotal;
            $data[3] = $invoice->invoice_total;
            $data[4] = $invoice->invoice_paid;
            $data[5] = "@".get_the_date("Y-m-d", get_the_ID())."@";
            $data[6] = "@".$invoice->line_items."@";
            $data[7] = "@".urlencode($invoice->invoice_description)."@";
            $data[8] = "@".urlencode($invoice->invoice_note)."@";

            $line.= $data[0].';'.$data[1].';'.$data[2].';'.$data[3].';'.$data[4].';'.$data[5].';'.$data[6].';'.$data[7].';'.$data[8].';'."\"\n";
        }
    } else {
        echo "Either you have no invoices added yet or the export failed";
    }


        $hdInv_CSV = fopen(plugin_dir_path(__FILE__) ."hdv-export.csv", "w") or die("Unable to open file!");
        fwrite($hdInv_CSV, $line);
        fclose($hdInv_CSV);
        // echo $line;
        echo 'COMPLETE: Right click the following link and choose "save link as" or similar. <a href ="'.plugin_dir_url(__FILE__) .'hdv-export.csv">DOWNLOAD CSV</a>';

        // Restore original Post Data
        wp_reset_postdata();
?>
