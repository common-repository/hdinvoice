<?php
    $css = plugins_url('style.css', __FILE__);
    $script = plugins_url('script.js', __FILE__);

    // get customer info
    $customer = get_the_terms(get_the_ID(), 'hdv_customer');
    $hdv_customer_id = $customer[0]->term_id;
    $hdv_customer = hdv_get_customer($hdv_customer_id);
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title><?php echo $hdv_settings->name; ?> invoice #<?php echo $hdv_invoice->invoice_number; ?></title>
		<link rel="stylesheet" type="text/css" href="<?php echo $css; ?>" media="all" />
		<meta name="theme-color" content="#222">
	</head>
	<body class="home">
		<div id="wrapper">
			<div id = "hdv_invoice">
				<div id = "hdv_header">
					<?php
                        if ($hdv_settings->layout_logo != "disable") {
                            echo hdv_get_invoice_header($hdv_settings->logo, $hdv_settings->name);
                        }
                    ?>
				</div>
				<div id = "hdv_content">
					<?php
                        echo '<h2 id = "hdv_invoice_title">'.$hdv_customer->name.' &#8212; Invoice #'.$hdv_invoice->invoice_number.'</h2>';
                        echo '<h3 id = "hdv_invoice_date">'.get_the_date().'</h3>';
                        echo apply_filters('the_content', $hdv_invoice->invoice_description);
                    ?>
				</div>

					<?php
                        if ($hdv_invoice->line_items != "" && $hdv_invoice->line_items != null && $hdv_invoice->line_items != "[]") {
                            echo '<div id = "hdv_line_items">';
                            $data = stripslashes(html_entity_decode($hdv_invoice->line_items));
                            $data = json_decode($data);

                            foreach ($data as $value) {
                                $line_item_name = sanitize_text_field($value[0]);
                                $line_item_value = hdv_amount(sanitize_text_field($value[1]));

                                echo '<div class="hdv_line_item">';
                                echo '<div class="two_third">'.$line_item_name.'</div>';
                                echo '<div class="one_third last">'.$line_item_value.'</div>';
                                echo '<div class = "clear"></div>';
                                echo '</div>';
                            }
                            echo '</div>';
                        }
                    ?>

				<div id = "hdv_totals">
					<?php
                        echo '<h2>'.$hdv_customer->name.'</h2>';

                        echo '<div class="hdv_line_item">';
                        echo '<div class="two_third">SUBTOTAL</div>';
                        echo '<div class="one_third last">'.hdv_amount($hdv_invoice->invoice_subtotal).'</div>';
                        echo '<div class = "clear"></div>';
                        echo '</div>';

                        echo hdv_get_invoice_tax($hdv_invoice->invoice_subtotal, $hdv_invoice->tax_rate, $hdv_invoice->taxes, $hdv_customer->tax);

                        echo '<div class="hdv_line_item">';
                        echo '<div class="two_third">AMOUNT PAID</div>';
                        echo '<div class="one_third last">'.hdv_amount($hdv_invoice->invoice_paid).'</div>';
                        echo '<div class = "clear"></div>';
                        echo '</div>';

                        echo '<div class="hdv_line_item">';
                        echo '<div class="two_third">TOTAL</div>';
                        echo '<div class="one_third last">'.hdv_amount($hdv_invoice->invoice_total).'</div>';
                        echo '<div class = "clear"></div>';
                        echo '</div>';
                    ?>
				</div>
				<div class = "clear"></div>
				<div id = "hdv_due">
					AMOUNT DUE
					<div id = "hdv_amount_due">
						<?php echo hdv_amount($hdv_invoice->invoice_owed); ?>
					</div>
					<?php
                        if ($hdv_invoice->invoice_owed <= 0) {
                            echo '<br/>THANK YOU FOR YOUR PAYMENT';
                        }
                    ?>
				</div>


			<div id = "hdv_footer">
				<a href="javascript:window.print()" title="Print this invoice"><img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDYwIDYwIiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCA2MCA2MDsiIHhtbDpzcGFjZT0icHJlc2VydmUiIHdpZHRoPSI2NHB4IiBoZWlnaHQ9IjY0cHgiPgo8Zz4KCTxwYXRoIGQ9Ik01MCwzMmMyLjc1NywwLDUtMi4yNDMsNS01cy0yLjI0My01LTUtNXMtNSwyLjI0My01LDVTNDcuMjQzLDMyLDUwLDMyeiBNNTAsMjRjMS42NTQsMCwzLDEuMzQ2LDMsM3MtMS4zNDYsMy0zLDMgICBzLTMtMS4zNDYtMy0zUzQ4LjM0NiwyNCw1MCwyNHoiIGZpbGw9IiMwMDAwMDAiLz4KCTxwYXRoIGQ9Ik00Miw0M0gxOGMtMC41NTMsMC0xLDAuNDQ3LTEsMXMwLjQ0NywxLDEsMWgyNGMwLjU1MywwLDEtMC40NDcsMS0xUzQyLjU1Myw0Myw0Miw0M3oiIGZpbGw9IiMwMDAwMDAiLz4KCTxwYXRoIGQ9Ik00Miw0OEgxOGMtMC41NTMsMC0xLDAuNDQ3LTEsMXMwLjQ0NywxLDEsMWgyNGMwLjU1MywwLDEtMC40NDcsMS0xUzQyLjU1Myw0OCw0Miw0OHoiIGZpbGw9IiMwMDAwMDAiLz4KCTxwYXRoIGQ9Ik01MSwxN1YwSDl2MTdIMHYzNGg2djNoM3Y2aDQydi02aDN2LTNoNlYxN0g1MXogTTExLDJoMzh2MTVIMTFWMnogTTksMTloNDJoN3YxNkgyVjE5SDl6IE04LDUydi0ydi0xdi00ICAgYzAtMC41NTMtMC40NDctMS0xLTFzLTEsMC40NDctMSwxdjRIMlYzN2g3djE1SDh6IE00OSw1OEgxMXYtNFYzN2gzOHYxN1Y1OHogTTU0LDQ5di00YzAtMC41NTMtMC40NDctMS0xLTFzLTEsMC40NDctMSwxdjR2MXYyaC0xICAgVjM3aDd2MTJINTR6IiBmaWxsPSIjMDAwMDAwIi8+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPC9zdmc+Cg=="></a>
			</div>

			<div id = "hdv_footer_content">
				<?php
                    if ($hdv_settings->layout_address != "disable") {
                        ?>
				<div class = "one_half">
					<p><strong><?php echo $hdv_settings->name; ?></strong><br/>
					<?php
                        if ($hdv_settings->phone != "" &&  $hdv_settings->phone != null) {
                            echo $hdv_settings->phone.'<br/>';
                        }
                        if ($hdv_settings->email != "" &&  $hdv_settings->email != null) {
                            echo $hdv_settings->email.'<br/><br/>';
                        }
                        echo hdv_get_company_address(); ?>
					</p>
				</div>
				<div class = "one_half last">
					<?php
                        echo $hdv_settings->info; ?>
				</div>
				<div class = "clear"></div>
				<?php
                    } else {
                        echo $hdv_settings->info;
                    } ?>
			</div>

			</div>
				<?php
                    if ($hdv_settings->layout_love != "disable") {
                        echo '<p class = "hdv_love">powered by <a href = "https://hdinvoice.com" title = "Best WordPress Invoicing Plugin">HDInvoice</a></p>';
                    }
                ?>
		</div>
		<script type="text/javascript" src="<?php echo $script; ?>"></script>
	</body>
</html>
