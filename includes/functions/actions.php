<?php
/*
    HDInvoice actions file
    Most actions are listed here - primarily for ajax calls from the Dashboard
    TODO: Could probably combine a lot of these actions and use a $POST var to simplify
*/

/* Initial view dashboard
------------------------------------------------------- */
function hdv_view_dashboard()
{
    if (current_user_can('edit_others_pages')) {
        include(dirname(__FILE__).'/../templates/view-dashboard.php');

        if (isset($_POST['hdv_dashboard_nonce'])) {
            die();
        }
    } else {
        echo 'error: You have insufficient user privilege'; // insufficient user privilege
    }
}
add_action('wp_ajax_hdv_view_dashboard', 'hdv_view_dashboard');

/* CUSTOMERS
 * Below are actions related to invoices
------------------------------------------------------- */

/* Start adding a new customer
------------------------------------------------------- */
function hdv_add_new_customer()
{
    if (current_user_can('edit_others_pages')) {
        $hdv_nonce = sanitize_text_field($_POST['hdv_dashboard_nonce']);

        if (wp_verify_nonce($hdv_nonce, 'hdv_dashboard_nonce') != false) {
            // permission granted
            // send the correct file to load data from
            include(dirname(__FILE__).'/../templates/add-new-customer.php');
        } else {
            echo 'error: Nonce failed to validate'; // failed nonce
        }
    } else {
        echo 'error: You have insufficient user privilege'; // insufficient user privilege
    }
    die();
}
add_action('wp_ajax_hdv_add_new_customer', 'hdv_add_new_customer');

/* Edit a customer
------------------------------------------------------- */
function hdv_edit_customer()
{
    if (current_user_can('edit_others_pages')) {
        $hdv_nonce = sanitize_text_field($_POST['hdv_dashboard_nonce']);

        if (wp_verify_nonce($hdv_nonce, 'hdv_dashboard_nonce') != false) {
            // permission granted
            // send the correct file to load data from
            include(dirname(__FILE__).'/../templates/edit-customer.php');
        } else {
            echo 'error: Nonce failed to validate'; // failed nonce
        }
    } else {
        echo 'error: You have insufficient user privilege'; // insufficient user privilege
    }
    die();
}
add_action('wp_ajax_hdv_edit_customer', 'hdv_edit_customer');

/* Save a new customer
------------------------------------------------------- */
function hdv_save_new_customer()
{
    if (current_user_can('edit_others_pages')) {
        $hdv_nonce = sanitize_text_field($_POST['hdv_dashboard_nonce']);

        if (wp_verify_nonce($hdv_nonce, 'hdv_dashboard_nonce') != false) {
            // permission granted
            // send the correct file to load data from
            require(dirname(__FILE__).'/../templates/save-new-customer.php');
        } else {
            echo 'error: Nonce failed to validate'; // failed nonce
        }
    } else {
        echo 'error: You have insufficient user privilege'; // insufficient user privilege
    }
    die();
}
add_action('wp_ajax_hdv_save_new_customer', 'hdv_save_new_customer');

/* Save an edited customer
------------------------------------------------------- */
function hdv_save_current_customer()
{
    if (current_user_can('edit_others_pages')) {
        $hdv_nonce = sanitize_text_field($_POST['hdv_dashboard_nonce']);
        $hdv_customer_id = intval($_POST['hdv_customer_id']);
        if (wp_verify_nonce($hdv_nonce, 'hdv_dashboard_nonce') != false && $hdv_customer_id != "" && $hdv_customer_id != null) {
            // permission granted
            // send the correct file to load data from
            require(dirname(__FILE__).'/../templates/save-current-customer.php');
        } else {
            echo 'error: Nonce failed to validate'; // failed nonce
        }
    } else {
        echo 'error: You have insufficient user privilege'; // insufficient user privilege
    }
    die();
}
add_action('wp_ajax_hdv_save_current_customer', 'hdv_save_current_customer');

/* View a customer
------------------------------------------------------- */
function hdv_view_customer()
{
    if (current_user_can('edit_others_pages')) {
        $hdv_nonce = sanitize_text_field($_POST['hdv_dashboard_nonce']);

        if (wp_verify_nonce($hdv_nonce, 'hdv_dashboard_nonce') != false) {
            // permission granted
            // send the correct file to load data from
            include(dirname(__FILE__).'/../templates/view-customer.php');
        } else {
            echo 'error: Nonce failed to validate'; // failed nonce
        }
    } else {
        echo 'error: You have insufficient user privilege'; // insufficient user privilege
    }
    die();
}
add_action('wp_ajax_hdv_view_customer', 'hdv_view_customer');


/* INVOICES
 * Below are actions related to invoices
------------------------------------------------------- */

/* Start adding a new invoice
------------------------------------------------------- */
function hdv_add_new_invoice()
{
    if (current_user_can('edit_others_pages')) {
        $hdv_nonce = sanitize_text_field($_POST['hdv_dashboard_nonce']);

        if (wp_verify_nonce($hdv_nonce, 'hdv_dashboard_nonce') != false) {
            // permission granted
            // send the correct file to load data from
            include(dirname(__FILE__).'/../templates/add-invoice.php');
        } else {
            echo 'error: Nonce failed to validate'; // failed nonce
        }
    } else {
        echo 'error: You have insufficient user privilege'; // insufficient user privilege
    }
    die();
}
add_action('wp_ajax_hdv_add_new_invoice', 'hdv_add_new_invoice');

/* Save new invoice
------------------------------------------------------- */
function hdv_save_new_invoice()
{
    if (current_user_can('edit_others_pages')) {
        $hdv_nonce = sanitize_text_field($_POST['hdv_dashboard_nonce']);

        if (wp_verify_nonce($hdv_nonce, 'hdv_dashboard_nonce') != false) {
            // permission granted
            // send the correct file to load data from
            include(dirname(__FILE__).'/../templates/save-new-invoice.php');
        } else {
            echo 'error: Nonce failed to validate'; // failed nonce
        }
    } else {
        echo 'error: You have insufficient user privilege'; // insufficient user privilege
    }
    die();
}
add_action('wp_ajax_hdv_save_new_invoice', 'hdv_save_new_invoice');

/* Save an edited invoice
------------------------------------------------------- */
function hdv_save_current_invoice()
{
    if (current_user_can('edit_others_pages')) {
        $hdv_nonce = sanitize_text_field($_POST['hdv_dashboard_nonce']);

        if (wp_verify_nonce($hdv_nonce, 'hdv_dashboard_nonce') != false) {
            // permission granted
            // send the correct file to load data from
            include(dirname(__FILE__).'/../templates/save-current-invoice.php');
        } else {
            echo 'error: Nonce failed to validate'; // failed nonce
        }
    } else {
        echo 'error: You have insufficient user privilege'; // insufficient user privilege
    }
    die();
}
add_action('wp_ajax_hdv_save_current_invoice', 'hdv_save_current_invoice');

/* View an invoice
------------------------------------------------------- */
function hdv_view_invoice()
{
    if (current_user_can('edit_others_pages')) {
        $hdv_nonce = sanitize_text_field($_POST['hdv_dashboard_nonce']);

        if (wp_verify_nonce($hdv_nonce, 'hdv_dashboard_nonce') != false) {
            // permission granted
            // send the correct file to load data from
            include(dirname(__FILE__).'/../templates/edit-invoice.php');
        } else {
            echo 'error: Nonce failed to validate'; // failed nonce
        }
    } else {
        echo 'error: You have insufficient user privilege'; // insufficient user privilege
    }
    die();
}
add_action('wp_ajax_hdv_view_invoice', 'hdv_view_invoice');


/* SETTINGS
 * Below are actions related to settings
------------------------------------------------------- */

/* View settings
------------------------------------------------------- */
function hdv_view_settings()
{
    if (current_user_can('edit_others_pages')) {
        $hdv_nonce = sanitize_text_field($_POST['hdv_dashboard_nonce']);

        if (wp_verify_nonce($hdv_nonce, 'hdv_dashboard_nonce') != false) {
            // permission granted
            // send the correct file to load data from
            include(dirname(__FILE__).'/../templates/settings.php');
        } else {
            echo 'error: Nonce failed to validate'; // failed nonce
        }
    } else {
        echo 'error: You have insufficient user privilege'; // insufficient user privilege
    }
    die();
}
add_action('wp_ajax_hdv_view_settings', 'hdv_view_settings');

/* Save settings
------------------------------------------------------- */
function hdv_save_settings()
{
    if (current_user_can('edit_others_pages')) {
        $hdv_nonce = sanitize_text_field($_POST['hdv_dashboard_nonce']);

        if (wp_verify_nonce($hdv_nonce, 'hdv_dashboard_nonce') != false) {
            // permission granted
            // send the correct file to load data from
            include(dirname(__FILE__).'/../templates/save-settings.php');
        } else {
            echo 'error: Nonce failed to validate'; // failed nonce
        }
    } else {
        echo 'error: You have insufficient user privilege'; // insufficient user privilege
    }
    die();
}
add_action('wp_ajax_hdv_save_settings', 'hdv_save_settings');

/* TOOLS
 * Below are actions related to tools
------------------------------------------------------- */

/* View tools
------------------------------------------------------- */
function hdv_view_tools()
{
    if (current_user_can('edit_others_pages')) {
        $hdv_nonce = sanitize_text_field($_POST['hdv_dashboard_nonce']);

        if (wp_verify_nonce($hdv_nonce, 'hdv_dashboard_nonce') != false) {
            // permission granted
            // send the correct file to load data from
            include(dirname(__FILE__).'/../templates/tools.php');
        } else {
            echo 'error: Nonce failed to validate'; // failed nonce
        }
    } else {
        echo 'error: You have insufficient user privilege'; // insufficient user privilege
    }
    die();
}
add_action('wp_ajax_hdv_view_tools', 'hdv_view_tools');

/* User selects a tool
------------------------------------------------------- */
function hdv_view_tool()
{
    if (current_user_can('edit_others_pages')) {
        $hdv_nonce = sanitize_text_field($_POST['hdv_dashboard_nonce']);
        $hdv_tool_id = sanitize_text_field($_POST['hdv_tool_id']);
        if (wp_verify_nonce($hdv_nonce, 'hdv_dashboard_nonce') != false) {
            // permission granted
            // send the correct file to load data from
            if ($hdv_tool_id == "hdv_import") {
                include(dirname(__FILE__).'/../templates/import.php');
            } elseif ($hdv_tool_id == "hdv_export") {
                include(dirname(__FILE__).'/../templates/tools-export.php');
            } else {
				echo '<p>Loading admin page</p>';
			}
        } else {
            echo 'error: Nonce failed to validate'; // failed nonce
        }
    } else {
        echo 'error: You have insufficient user privilege'; // insufficient user privilege
    }
    die();
}
add_action('wp_ajax_hdv_view_tool', 'hdv_view_tool');

/* Start CSV import
------------------------------------------------------- */
function hdv_view_import()
{
    if (current_user_can('edit_others_pages')) {
        include(dirname(__FILE__).'/../templates/import.php');
    } else {
        echo 'error: You have insufficient user privilege'; // insufficient user privilege
    }
}

/* Continue the import (User has selected that the data looks good)
------------------------------------------------------- */
function hdv_continue_import()
{
    if (current_user_can('edit_others_pages')) {
        $hdv_nonce = sanitize_text_field($_POST['hdv_dashboard_nonce']);
        $hdv_csv_path = sanitize_text_field($_POST['hdv_csv_path']);
        if (wp_verify_nonce($hdv_nonce, 'hdv_dashboard_nonce') != false) {
            include(dirname(__FILE__).'/../templates/import-save.php');
        } else {
            echo 'error: Nonce failed to validate'; // failed nonce
        }
    } else {
        echo 'error: You have insufficient user privilege'; // insufficient user privilege
    }
    die();
}
add_action('wp_ajax_hdv_continue_import', 'hdv_continue_import');



/* Help file
------------------------------------------------------- */
function hdv_view_help()
{
    if (current_user_can('edit_others_pages')) {
		// no real functionalty here, so no need to check nonce
        include(dirname(__FILE__).'/../templates/help.php');
    } else {
        echo 'error: You have insufficient user privilege'; // insufficient user privilege
    }
	die();
}
add_action('wp_ajax_hdv_view_help', 'hdv_view_help');