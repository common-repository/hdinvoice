<?php
    /* HDInvoice Dashboard */

    // STOP PAGE CACHING
    // WP Fastest Cache
    echo '<!-- [wpfcNOT] -->';
    // W3 Total Cache, WP Super Cache, WP Rocket, Comet Cache, Cachify
    define('DONOTCACHEPAGE', true);
    define('DONOTCACHEDB', true);

    if (!is_user_logged_in()) {
        // TODO: Perhaps show a login form instead?
        // Will need to create an settings option incase site owner
        // doesn't want front-facing login forms
        wp_redirect(home_url());
        exit;
    }

    // remove all scripts and style created by other plugins or the theme
    // then ensure that only HDInvoice stuff is running
    function hdv_denqueue_enqueue()
    {
        // start with the scripts
        global $wp_scripts;
        $wp_scripts->queue = array();
        wp_enqueue_script('hdv_main_script', plugin_dir_url(__FILE__) .'script.js', array('jquery'), null, true);
        wp_enqueue_script('hdv_ve', plugin_dir_url(__FILE__) .'../ve/trumbowyg.min.js', array('jquery'), null, true);
        wp_enqueue_script('hdv_chart', plugin_dir_url(__FILE__).'chart.js', array('jquery'), null, true);
        $hdv_dashboard = intval(get_option('hdv_dashboard'));
        wp_localize_script('hdv_main_script', 'hdv_ajax', admin_url('admin-ajax.php'));
        wp_localize_script('hdv_main_script', 'hdv_dashboard_url', get_the_permalink($hdv_dashboard));	 // used to redirect after inporter finishes
        // now styles
        global $wp_styles;
        $wp_styles = new stdClass(); // stops "Creating default object from empty value" warning. Not sure why styles shows this error but scripts does not
        $wp_styles->queue = array();
        wp_enqueue_style('hdv_ve_css', plugin_dir_url(__FILE__) .'../ve/ui/trumbowyg.min.css');
        wp_enqueue_style('dashicons');

        // scripts and styles needed to use WP uploader
        wp_enqueue_media();

        // NOTE: Might be worth implimenting my own asset caching solution. wp_enqueue_media() loads a lot files
    }
    add_action('wp_print_scripts', 'hdv_denqueue_enqueue', 100); // I could also use wp_print_styles, but see no reason to separate them

?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>HDInvoice Dashboard</title>
	<meta name="theme-color" content="#222">
	<link rel='stylesheet' id='hdv_main_style'  href='<?php echo plugin_dir_url(__FILE__) .'style.css'; ?>' type='text/css' media='all' />
	<?php wp_head(); ?>
</head>

<body class="three_col">
	<div id="wrapper">
		<div id="header">
			<div id="header_1">
				<div id = "add_customer" class="hd_button">Add Customer</div>
			</div>
			<div id="header_2">
				<input type = "text" id = "search" placeholder="search"/>
			</div>
			<div id="header_3">
				<h1>HD INVOICE DASHBOARD</h1>
			</div>
		</div>
		<div id="main">
			<div id="sidebar_1">
				<ul>
					<li class="active" id = "hdv_nav_dashboard">Dashboard</li>
					<li id = "hdv_nav_tools">Tools</li>
					<li id = "hdv_nav_settings">Settings</li>
					<li id = "hdv_nav_help">Help</li>
					<a href = "<?php echo get_admin_url(); ?>"><li>Site Admin</li></a>
				</ul>
				<div id = "hdv_logo">
					<img src = "<?php echo plugins_url('../../images/logo.png', __FILE__); ?>" alt = "Harmonic Design"/>
				</div>
			</div>
			<div id="sidebar_2">
				<div id = "hdv_customers_list">
					<?php
                    $taxonomy = 'hdv_customer';
                    $term_args=array(
                      'hide_empty' => false,
                      'orderby' => 'name',
                      'order' => 'ASC'
                    );
                    $tax_terms = get_terms($taxonomy, $term_args);
                    if (empty($tax_terms)) {
                        $noCustomers = "yes";
                    }
                    if (! empty($tax_terms) && ! is_wp_error($tax_terms)) {
                        $noCustomers = "no";
                        foreach ($tax_terms as $tax_terms) {
                            ?>
								<div class = "customer_item" data-id = "<?php echo $tax_terms->term_id; ?>" data-name = "<?php echo $tax_terms->name; ?>">
									<?php echo mb_strimwidth($tax_terms->name, 0, 28, "..."); ?>
								</div>
					<?php
                        }
                    }
                    if ($noCustomers == "yes") {
                        echo '<div class = "customer_item_first">Please add a customer to being creating invoices</div>';
                    }
                    ?>
				</div>
				<div id = "hdv_tools_list">
					<div class = "customer_item" data-id = "hdv_import">
						Import Invoices
					</div>
					<div class = "customer_item" data-id = "hdv_export">
						Export Invoices
					</div>
					<div class = "customer_item" data-id = "hdv_admin_cpt">
						<a href = "<?php echo get_admin_url(); ?>edit.php?post_type=hdv_invoice" target = "_blank">Admin CPT</a>
					</div>
					<div class = "customer_item" data-id = "hdv_admin_taxonomy">
						<a href = "<?php echo get_admin_url(); ?>edit-tags.php?taxonomy=hdv_customer" target = "_blank">Admin Taxonomy</a>
					</div>					
				</div>
			</div>
			<div id = "hdv_loading">
			  <div class="sk-folding-cube">
				<div class="sk-cube1 sk-cube"></div>
				<div class="sk-cube2 sk-cube"></div>
				<div class="sk-cube4 sk-cube"></div>
				<div class="sk-cube3 sk-cube"></div>
			  </div>
			</div>
			<div id="content">
				<?php
                    if (isset($_GET['import']) && !empty($_GET['import'])) {
                        if ($_GET['import'] == "true") {
                            hdv_view_import();
                        }
                    } else {
                        hdv_view_dashboard();
                    }
                ?>
			</div>
		</div>
	</div>
	<?php
        wp_nonce_field('hdv_dashboard_nonce', 'hdv_dashboard_nonce');
        wp_footer();
    ?>
</body>
</html>
