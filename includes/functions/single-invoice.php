<?php
/*
    HDInvoice single-invoice functions
    Functions to help build font-end invoice pages
*/

/* Get's the company logo, or returns the company name
------------------------------------------------------- */
function hdv_get_invoice_header($logo, $name)
{
    // check if there is a valid logo uploaded
    if ($logo != "https://dummyimage.com/560x250/bbbbbb/2d2d2d.gif&text=customer+logo") {
        echo '<img src = "'.$logo.'" alt = "'.$name.'"/>';
    } else {
        echo '<h1 id = "hdv_no_logo">'.$name.'</h1>';
    }
}

/* Calculates the invoice tax lines and totals
------------------------------------------------------- */
function hdv_get_invoice_tax($subtotal, $invoice_tax, $taxes, $customer_tax)
{
    // only return if tax is enabled on the invoice
    if ($invoice_tax != 0) {
        // check if the invoice tax rate is the same as the customer tax rate
        // if it is, then we simply declare a tax rate line item
        if ($invoice_tax == $customer_tax) {
            $tax_amount = ($invoice_tax / 100) * $subtotal;
            echo '<div class="hdv_line_item">';
            echo '<div class="two_third">'.$invoice_tax.'% TAX</div>';
            echo '<div class="one_third last">'.hdv_amount($tax_amount).'</div>';
            echo '<div class = "clear"></div>';
            echo '</div>';
        } else {
            // use JSON array to print the taxes
            $taxes = json_decode(html_entity_decode($taxes), true);
            foreach ($taxes as $value) {
                $tax_amount = ($value[1] / 100) * $subtotal;
                echo '<div class="hdv_line_item">';
                echo '<div class="two_third">'.$value[0].' '.$value[1].'<small>%</small></div>';
                echo '<div class="one_third last">'.hdv_amount($tax_amount).'</div>';
                echo '<div class = "clear"></div>';
                echo '</div>';
            }
        }
    }
}

/* Get the company address for use on the bottom of invoices
------------------------------------------------------- */
function hdv_get_company_address()
{
    $hdv_settings = hdv_get_settings_values();
    $address = "";
    if ($hdv_settings->address2 != "" && $hdv_settings->address2 != null) {
        $address = $address.$hdv_settings->address2.' - ';
    }
    if ($hdv_settings->address != "" && $hdv_settings->address != null) {
        $address = $address.$hdv_settings->address.', <br/>';
    }
    if ($hdv_settings->city != "" && $hdv_settings->city != null) {
        $address = $address.$hdv_settings->city.', ';
    }
    if ($hdv_settings->state != "" && $hdv_settings->state != null) {
        $address = $address.$hdv_settings->state.',<br/>';
    }
    if ($hdv_settings->zip != "" && $hdv_settings->zip != null) {
        $address = $address.$hdv_settings->zip;
    }
    if ($hdv_settings->country != "" && $hdv_settings->country != null) {
        $address = $address.', '.$hdv_settings->country;
    }

    if ($address != "" && $address != null) {
        $address = $address.'<br/>';
    }
    return $address;
}
