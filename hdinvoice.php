<?php
/*
 * Plugin Name: HDInvoice
 * Description: The ultimate invoicing solution.
 * Plugin URI: https://hdinvoice.com?utm_source=HDInvoice&utm_medium=pluginPage
 * Author: Harmonic Design
 * Author URI: https://harmonicdesign.ca?utm_source=HDinvoice&utm_medium=pluginPage
 * Version: 0.2
 * Notes: This plugin is still in the early stages of development, and as such, some features you require may not have been implimented yet. Please check the plugin page for a list of missing/upcoming features to ensure that HDInvoice can meet your needs.
*/


if (! defined('ABSPATH')) {
    die('Invalid request.');
}

if (!defined('HDV_PLUGIN_VERSION')) {
    define('HDV_PLUGIN_VERSION', '0.2');
}

/* Include the basic required files
------------------------------------------------------- */
require(dirname(__FILE__).'/includes/functions.php'); // main hooks and functions
require(dirname(__FILE__).'/includes/post_type.php'); // custom post types/taxonomies


// function to check if HDInvoice is active
function hdv_exists()
{
    return;
}

/* Make HDInvoice pages use custom templates
------------------------------------------------------- */
function hdv_add_dashboard_page($template)
{
    global $post;
    $hdv_dashboard = get_option('hdv_dashboard');

    if ($post->ID == $hdv_dashboard) {
        $template = dirname(__FILE__) . '/includes/templates/dashboard.php';
    }
    return $template;
}
add_filter('page_template', 'hdv_add_dashboard_page');



/* Make HDInvoice use custom invoice template
------------------------------------------------------- */
function hdv_add_invoice_template($template)
{
    global $post;
    // Product page
    if ($post->post_type == 'hdv_invoice') {
        $template = dirname(__FILE__) . '/includes/templates/invoice/invoice.php';
    }
    return $template;
}
add_filter('single_template', 'hdv_add_invoice_template');





/* Run the following on HDInvoice plugin activation
------------------------------------------------------- */
function hdv_activate_plugin()
{
    hdv_post_type_invoice(); // register post types
    hdv_taxonomy_customer(); // register customers
    flush_rewrite_rules(); // flush permalinks

    // update plugin version
    update_option("hdv_PLUGIN_VERSION", HDV_PLUGIN_VERSION);

    // Create HDInvoice required pages if they do not already exist
    $hdv_dashboard = get_option('hdv_dashboard');

    if ($hdv_dashboard == "" || $hdv_dashboard == null) {
        // Save the invoice - name and editor
        $post_information = array(
            'post_title' => "HDInvoice",
            'post_content' => '<p>This page is needed for HDInvoice. You can rename the page as long as you do not delete it.</p>',
            'post_type' => 'page',
            'post_status' => 'publish'
        );
        $post_id = wp_insert_post($post_information);
        update_option("hdv_dashboard", $post_id);
    }
}
register_activation_hook(__FILE__, 'hdv_activate_plugin');
