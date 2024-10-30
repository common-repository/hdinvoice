<?php
/* view Dashboard */

// get stats
$total_invoices = wp_count_posts('hdv_invoice')->publish;
$total_customers = wp_count_terms('hdv_customer');
$hdv_stats = hdv_get_stats();
$this_year = date("Y");

// get dashboard chart stats
// should not enqueue to dashboard.php because this will be refreshed
// each time a new invoice is added or edited
?>
<script>
	// this year
	var hdv_chart_data = [{
                        y: <?php echo $hdv_stats->jan; ?>
                    },
                    {
                        y: <?php echo $hdv_stats->jan; ?>,
                        label: "Jan " + "<?php echo $this_year;?>"
                    },
                    {
                        y: <?php echo $hdv_stats->feb; ?>,
                        label: "Feb " + "<?php echo $this_year;?>"
                    },
                    {
                        y: <?php echo $hdv_stats->mar; ?>,
                        label: "Mar " + "<?php echo $this_year;?>"
                    },
                    {
                        y: <?php echo $hdv_stats->apr; ?>,
                        label: "Apr " + "<?php echo $this_year;?>"
                    },
                    {
                        y: <?php echo $hdv_stats->may; ?>,
                        label: "May " + "<?php echo $this_year;?>"
                    },
                    {
                        y: <?php echo $hdv_stats->jun; ?>,
                        label: "Jun " + "<?php echo $this_year;?>"
                    },
                    {
                        y: <?php echo $hdv_stats->jul; ?>,
                        label: "Jul " + "<?php echo $this_year;?>"
                    },
                    {
                        y: <?php echo $hdv_stats->aug; ?>,
                        label: "Aug " + "<?php echo $this_year;?>"
                    },
                    {
                        y: <?php echo $hdv_stats->sep; ?>,
                        label: "Sep " + "<?php echo $this_year;?>"
                    },
                    {
                        y: <?php echo $hdv_stats->oct; ?>,
                        label: "Oct " + "<?php echo $this_year;?>"
                    },
                    {
                        y: <?php echo $hdv_stats->nov; ?>,
                        label: "Nov " + "<?php echo $this_year;?>"
                    },
                    {
                        y: <?php echo $hdv_stats->dec; ?>,
                        label: "Dec " + "<?php echo $this_year;?>"
                    },
                    {
                        y: <?php echo $hdv_stats->dec; ?>
                    },
                ];
	// last year
	var hdv_chart_data2 = [{
                        y: <?php echo $hdv_stats->jan; ?>
                    },
                    {
                        y: <?php echo $hdv_stats->jan; ?>,
                        label: "Jan " + "<?php echo $this_year - 1;?>"
                    },
                    {
                        y: <?php echo $hdv_stats->feb2; ?>,
                        label: "Feb " + "<?php echo $this_year - 1;?>"
                    },
                    {
                        y: <?php echo $hdv_stats->mar2; ?>,
                        label: "Mar " + "<?php echo $this_year - 1;?>"
                    },
                    {
                        y: <?php echo $hdv_stats->apr2; ?>,
                        label: "Apr " + "<?php echo $this_year - 1;?>"
                    },
                    {
                        y: <?php echo $hdv_stats->may2; ?>,
                        label: "May " + "<?php echo $this_year - 1;?>"
                    },
                    {
                        y: <?php echo $hdv_stats->jun2; ?>,
                        label: "Jun " + "<?php echo $this_year - 1;?>"
                    },
                    {
                        y: <?php echo $hdv_stats->jul2; ?>,
                        label: "Jul " + "<?php echo $this_year - 1;?>"
                    },
                    {
                        y: <?php echo $hdv_stats->aug2; ?>,
                        label: "Aug " + "<?php echo $this_year - 1;?>"
                    },
                    {
                        y: <?php echo $hdv_stats->sep2; ?>,
                        label: "Sep " + "<?php echo $this_year - 1;?>"
                    },
                    {
                        y: <?php echo $hdv_stats->oct2; ?>,
                        label: "Oct " + "<?php echo $this_year - 1;?>"
                    },
                    {
                        y: <?php echo $hdv_stats->nov2; ?>,
                        label: "Nov " + "<?php echo $this_year - 1;?>"
                    },
                    {
                        y: <?php echo $hdv_stats->dec2; ?>,
                        label: "Dec " + "<?php echo $this_year - 1;?>"
                    },
                    {
                        y: <?php echo $hdv_stats->dec2; ?>
                    }
                ];

</script>

		<div id="dashboard_main">
			<div class="one_half">
				<div class="hdDashboardWidget" id="dashboard-stats">
					<div class="one_half hdWidgetWrapper">
						<span class="dashicons dashicons-admin-page dashicons-widget" style="background:#177bbb;"></span>
						<span class="dashPanel1Inner"><?php echo $total_invoices; ?><span class="dashContentSmall">INVOICES</span></span>
					</div>
					<div class="one_half last hdWidgetWrapper">
						<span class="dashicons dashicons-groups dashicons-widget" style="background:#1DC499;"></span>
						<span class="dashPanel1Inner"><?php echo $total_customers; ?><span class="dashContentSmall">CUSTOMERS</span></span>
					</div>
					<div class="clear"></div>
					<br>
					<div class="one_half hdWidgetWrapper">
						<span class="dashicons dashicons-chart-area dashicons-widget" style="background:#1CCACC;"></span>
						<span class="dashPanel1Inner"><?php echo hdv_amount($hdv_stats->invoice_total); ?><span class="dashContentSmall">INVOICED</span></span>
					</div>
					<div class="one_half last hdWidgetWrapper">
						<span class="dashicons dashicons-calendar dashicons-widget" style="background:#32e37d;"></span>
						<span class="dashPanel1Inner"><?php echo $hdv_stats->this_month; ?><span class="dashContentSmall">THIS MONTH</span></span>
					</div>
					<div class="clear"></div>
				</div>
			</div>
			<div class="one_half last">
				<div class="hdDashboardWidget" id="dashboard-recent">
				<?php
                // WP_Query arguments
                $args = array(
                    'post_type'              => array( 'hdv_invoice' ),
                    'posts_per_page'         => '5',
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
                        $terms = get_the_terms(get_the_ID(), 'hdv_customer');
                        $customer_name = $terms[0]->name;
                        $customer_id = $terms[0]->term_id;
                        $hdv_invoice_state = get_post_meta(get_the_ID(), 'hdv_invoice_state', true); ?>
					<div class="dashboard_item hdv_<?php echo $hdv_invoice_state; ?>" data-id = "<?php echo $customer_id; ?>" data-name = "<?php echo $customer_name; ?>">
						<div class="one_third">#<?php echo $hdv_invoice_number; ?> | <?php echo $invoice_date; ?></div>
						<div class="one_third"><?php echo $customer_name; ?></div>
						<div class="one_third last"><?php echo hdv_amount($invoice_subtotal); ?></div>
						<div class="clear"></div>
					</div>
					<?php
                    }
                } else {
                    // no posts found
                    echo '<div class="dashboard_item">Recently added invoices will appear here</div>';
                }

                // Restore original Post Data
                wp_reset_postdata();
                ?>
				</div>
			</div>
			<div class="clear"></div>

			<div id ="hdChart">
				<div id="chartContainer"></div>
				<div class ="hdChartDate">
					JAN
				</div>
				<div class ="hdChartDate">
					FEB
				</div>
				<div class ="hdChartDate">
					MAR
				</div>
				<div class ="hdChartDate">
					APR
				</div>
				<div class ="hdChartDate">
					MAY
				</div>
				<div class ="hdChartDate">
					JUN
				</div>
				<div class ="hdChartDate">
					JUL
				</div>
				<div class ="hdChartDate">
					AUG
				</div>
				<div class ="hdChartDate">
					SEP
				</div>
				<div class ="hdChartDate">
					OCT
				</div>
				<div class ="hdChartDate">
					NOV
				</div>
				<div class ="hdChartDate">
					DEC
				</div>
				<div class ="clear"></div>
			</div>
		</div>
