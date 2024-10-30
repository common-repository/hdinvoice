<?php
/* view customer */

global $hdv_customer_id;

if ($hdv_customer_id == "" || $hdv_customer_id == null) {
    $hdv_customer_id = intval($_POST['hdv_customer_id']);
}

// // make sure the customer ID exists
if ($hdv_customer_id != "" && $hdv_customer_id != null) {
    // get customer info
    $hdv_customer = hdv_get_customer($hdv_customer_id);
    // if customer info exists, continue
    if ($hdv_customer->name != "" && $hdv_customer->name != null) {
        hdv_show_customer($hdv_customer_id, $hdv_customer);
    } else {
        echo 'Customer does not exist or is invalid';
    }
} else {
    echo 'no cutomer ID found';
}

function hdv_show_customer($hdv_customer_id, $hdv_customer)
{
    ?>
<div id="edit_customer" data-customer-id = "<?php echo $hdv_customer_id; ?>" class="hd_button3" style="display: block;">EDIT CUSTOMER</div>
<div id="add_invoice" data-customer-id = "<?php echo $hdv_customer_id; ?>" class="hd_button2" style="display: block;">ADD INVOICE</div>
<div id = "hdv_view_customer">
	<div class = "one_half">
		<h2><?php echo $hdv_customer->name; ?></h2>
		<?php
            // check for website data and print
            if ($hdv_customer->website != "" && $hdv_customer->website != null) {
                echo '<span class = "hdv_view_customer_item"><span class="dashicons dashicons-admin-links"></span> <a href = "'.$hdv_customer->website.'">'.$hdv_customer->website.'</a></span>';
            }
    // check for email data and print
    if ($hdv_customer->email != "" && $hdv_customer->email != null) {
        echo '<span class = "hdv_view_customer_item"><span class="dashicons dashicons-email-alt"></span> <a href = "mailto:'.$hdv_customer->email.'">'.$hdv_customer->email.'</a></span>';
    }
    // check for phone number data and print
    if ($hdv_customer->phone != "" && $hdv_customer->phone != null) {
        echo '<span class = "hdv_view_customer_item"><span class="dashicons dashicons-phone"></span> '.$hdv_customer->phone.'</span>';
    }
    // check for address data and print
    $address = "";
    if ($hdv_customer->country != "" && $hdv_customer->country != null) {
        $address = $hdv_customer->country;
    }
    if ($hdv_customer->state != "" && $hdv_customer->state != null) {
        $address = $hdv_customer->state. ', '.$address;
    }
    if ($hdv_customer->city != "" && $hdv_customer->city != null) {
        $address = $hdv_customer->city. ', '.$address;
    }
    if ($hdv_customer->address != "" && $hdv_customer->address != null) {
        if ($hdv_customer->address2 != "" && $hdv_customer->address2 != null) {
            $address = $hdv_customer->address.' '.$hdv_customer->address2. ', '.$address;
        } else {
            $address = $hdv_customer->address. ', '.$address;
        }
    }
    if ($address != "" && $address != null) {
        echo '<span class = "hdv_view_customer_item"><span class="dashicons dashicons-admin-post"></span> '.$address.'</span>';
    } ?>
	</div>
	<div class = "one_half last">
		<div id = "hdv_customer_logo_wrap">
			<img src = "<?php echo $hdv_customer->logo; ?>" alt = "Company Logo"/>
		</div>
	</div>
	<div class = "clear"></div>
	<?php
        if ($hdv_customer->info != "" && $hdv_customer->info != null) {
            echo apply_filters('the_content', $hdv_customer->info);
        } ?>

	<hr/>

	<div id = "hdv_customer_invoices">
		<h3>
			Customer Invoices
		</h3>
		<table class="hdv_table">
			<thead>
				<tr>
					<th>Invoice #</th>
					<th>Date</th>
					<th>Subtotal</th>
					<th width="30">edit</th>
					<th width="30">view</th>
				</tr>
			</thead>
			<tbody>

			<?php
                // WP_Query arguments
                $args = array(
                    'post_type'              => array( 'hdv_invoice' ),
                    'nopaging'               => true,
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'hdv_customer',
                            'field' => 'term_id',
                            'terms' => $hdv_customer_id
                        )
                    )
                );

    // The Query
    $query = new WP_Query($args);

    // The Loop
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $hdv_invoice_number = get_post_meta(get_the_ID(), 'hdv_invoice_number', true);
            $invoice_date = get_the_date();
            $invoice_subtotal = get_post_meta(get_the_ID(), 'hdv_invoice_subtotal', true);
            $hdv_invoice_state = get_post_meta(get_the_ID(), 'hdv_invoice_state', true); ?>

				<tr class="hdv_<?php echo $hdv_invoice_state; ?>">
					<td><?php echo $hdv_invoice_number; ?></td>
					<td><?php echo $invoice_date; ?></td>
					<td align="right"><?php echo hdv_amount($invoice_subtotal); ?></td>
					<td class="textCenter"><span class="dashicons dashicons-edit hdv_edit_invoice" data-id = "<?php echo get_the_ID(); ?>"></span></td>
					<td class="textCenter">
						<?php
                            if ($hdv_invoice_state != "void") {
                                ?>
						<a href = "<?php the_permalink(); ?>" target = "_blank"><span class="dashicons dashicons-welcome-view-site"></span></a>
						<?php
                            } else {
                                echo '&nbsp;';
                            } ?>
					</td>
				</tr>
					<?php
        }
    } else {
        // no posts found
                    ?>
				<tr>
					<td colspan = "5">No invoices have been added to this customer</td>
				</tr>
					<?php
    }
    // Restore original Post Data
    wp_reset_postdata(); ?>

			</tbody>
		</table>
	</div>
</div>
<?php
}

?>
