<?php
/*
    HDInvoice functions admin
    Functions required for backend admin are here
*/

/* Add admin menu pages
------------------------------------------------------- */
function hdv_register_menu_page()
{
    if (current_user_can('edit_others_pages')) {
        add_menu_page('HDInvoice', 'HDInvoice', 'edit_posts', 'hdv_options', 'hdv_create_options_page', plugins_url('../../images/logo-16.png', __FILE__), 10);
    }
}
add_action('admin_menu', 'hdv_register_menu_page', 1);

/* Redirect the menu page to the Dashboard
------------------------------------------------------- */
function hdv_create_options_page()
{
    // NOTE: because we cannot resend headers at this point, we cannot use
    // wp_redirect(). Feels too hacky to hook into this earlier
    // ATTN WP PLUGIN REVIEWER: Is there a clean way to use wp_redirect() instead?
    $hdv_dashboard = intval(get_option('hdv_dashboard'));
    $perm = get_permalink($hdv_dashboard);
    echo '<meta http-equiv="refresh" content="0;URL=\''.$perm.'\'" />';
}

/* Add custom page subtitle/post status to
*  HDInvoice Dashboard page so users easily know
*  that the new page is because of this plugin
------------------------------------------------------- */
function hdv_filter_display_post_states($post_states, $post)
{
    $hdv_dashboard = get_option('hdv_dashboard');
    if ($hdv_dashboard == $post->ID) {
        echo ' - HDInvoice Dashboard';
    }
    return $post_states;
};
add_filter('display_post_states', 'hdv_filter_display_post_states', 10, 2);
