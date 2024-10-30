<?php

    // single-invoice template

    // STOP PAGE CACHING
    // WP Fastest Cache
    echo '<!-- [wpfcNOT] -->';
    // W3 Total Cache, WP Super Cache, WP Rocket, Comet Cache, Cachify
    define('DONOTCACHEPAGE', true);
    define('DONOTCACHEDB', true);

    // get global settings
    global $hdv_settings;
    $hdv_settings = hdv_get_settings_values();

    // get invoice data
    global $hdv_invoice;
    $hdv_invoice = hdv_get_invoice_values(get_the_ID());

    if ($hdv_invoice->invoice_state == "void") {
        wp_die("this invoice is not available", "HDInvoice") ;
    } else {
        if ($hdv_settings->layout == "2_col") {
            include(dirname(__FILE__).'/multi-col.php');
        } else {
            include(dirname(__FILE__).'/one-col.php');
        }
    }
