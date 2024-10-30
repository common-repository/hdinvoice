<?php
/*
    HDInvoice custom post types
    Creates the hdv_invoice post type
    as well as the hdv_customer taxonomy
*/

/* Register Invoice CPT
------------------------------------------------------- */
function hdv_post_type_invoice()
{
    $labels = array(
        'name'                  => _x('Invoices', 'Post Type General Name', 'text_domain'),
        'singular_name'         => _x('Invoice', 'Post Type Singular Name', 'text_domain'),
        'menu_name'             => __('Invoices', 'text_domain'),
        'name_admin_bar'        => __('Invoices', 'text_domain'),
        'archives'              => __('Item Archives', 'text_domain'),
        'attributes'            => __('Item Attributes', 'text_domain'),
        'parent_item_colon'     => __('Parent Item:', 'text_domain'),
        'all_items'             => __('All Items', 'text_domain'),
        'add_new_item'          => __('Add New Item', 'text_domain'),
        'add_new'               => __('Add New', 'text_domain'),
        'new_item'              => __('New Item', 'text_domain'),
        'edit_item'             => __('Edit Item', 'text_domain'),
        'update_item'           => __('Update Item', 'text_domain'),
        'view_item'             => __('View Item', 'text_domain'),
        'view_items'            => __('View Items', 'text_domain'),
        'search_items'          => __('Search Item', 'text_domain'),
        'not_found'             => __('Not found', 'text_domain'),
        'not_found_in_trash'    => __('Not found in Trash', 'text_domain'),
        'featured_image'        => __('Featured Image', 'text_domain'),
        'set_featured_image'    => __('Set featured image', 'text_domain'),
        'remove_featured_image' => __('Remove featured image', 'text_domain'),
        'use_featured_image'    => __('Use as featured image', 'text_domain'),
        'insert_into_item'      => __('Insert into item', 'text_domain'),
        'uploaded_to_this_item' => __('Uploaded to this item', 'text_domain'),
        'items_list'            => __('Items list', 'text_domain'),
        'items_list_navigation' => __('Items list navigation', 'text_domain'),
        'filter_items_list'     => __('Filter items list', 'text_domain'),
    );
    $rewrite = array(
        'slug'                  => 'invoice/customer/%hdv_customer%',
        'with_front'            => true,
        'pages'                 => true,
        'feeds'                 => false,
    );
    $args = array(
        'label'                 => __('Invoice', 'text_domain'),
        'description'           => __('Post Type Description', 'text_domain'),
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor' ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true, //
        'show_in_menu'          => false, // set these to true to see normal CPT
        'menu_position'         => 50,
        'show_in_admin_bar'     => false,
        'show_in_nav_menus'     => false,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => true,
        'publicly_queryable'    => true,
        'rewrite'               => $rewrite,
        'capability_type'       => 'page',
    );
    register_post_type('hdv_invoice', $args);
}
add_action('init', 'hdv_post_type_invoice', 0);


/* Register Customer Taxonomy
------------------------------------------------------- */
function hdv_taxonomy_customer()
{
    $labels = array(
        'name'                       => _x('Customers', 'Taxonomy General Name', 'text_domain'),
        'singular_name'              => _x('Customer', 'Taxonomy Singular Name', 'text_domain'),
        'menu_name'                  => __('Customers', 'text_domain'),
        'all_items'                  => __('All Customers', 'text_domain'),
        'parent_item'                => __('Parent Customer', 'text_domain'),
        'parent_item_colon'          => __('Parent Customer:', 'text_domain'),
        'new_item_name'              => __('New Customer Name', 'text_domain'),
        'add_new_item'               => __('Add A New Customer', 'text_domain'),
        'edit_item'                  => __('Edit Customer', 'text_domain'),
        'update_item'                => __('Update Customer', 'text_domain'),
        'view_item'                  => __('View Customer', 'text_domain'),
        'separate_items_with_commas' => __('Separate Customers with commas', 'text_domain'),
        'add_or_remove_items'        => __('Add or remove Customers', 'text_domain'),
        'choose_from_most_used'      => __('Choose from the most used', 'text_domain'),
        'popular_items'              => __('Popular Customers', 'text_domain'),
        'search_items'               => __('Search Customers', 'text_domain'),
        'not_found'                  => __('Not Found', 'text_domain'),
    );
    $rewrite = array(
        'slug'                       => 'customer',
        'with_front'                 => true,
        'hierarchical'               => false,
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                	=> true,
        'show_ui'               	=> true,
        'show_in_menu'          	=> false,
        'menu_position'         	=> 5,
        'show_in_admin_bar'     	=> false,
        'show_in_nav_menus'     	=> false,
        'can_export'            	=> true,
        'has_archive'           	=> false,
        'show_tagcloud'              => false,
        'rewrite'                    => $rewrite,
    );
    register_taxonomy('hdv_customer', array( 'hdv_invoice' ), $args);
}
add_action('init', 'hdv_taxonomy_customer', 0);


/* Allow customer name in invoice permalink
------------------------------------------------------- */
function hdv_invoice_permalink($permalink, $post_id, $leavename)
{
    if (strpos($permalink, '%hdv_customer%') === false) {
        return $permalink;
    }
    // Get post
    $post = get_post($post_id);
    if (!$post) {
        return $permalink;
    }
    $terms = wp_get_object_terms($post->ID, 'hdv_customer');
    if (!is_wp_error($terms) && !empty($terms) && is_object($terms[0])) {
        $taxonomy_slug = $terms[0]->slug;
    } else {
        $taxonomy_slug = 'hdinvoice';
    }
    return str_replace('%hdv_customer%', $taxonomy_slug, $permalink);
}
add_filter('post_link', 'hdv_invoice_permalink', 10, 3);
add_filter('post_type_link', 'hdv_invoice_permalink', 10, 3);


/* adding new rewrite rule
------------------------------------------------------- */
function hdv_insert_rewrite_rules($rules)
{
    $newrules = array();
    $newrules['(project)/(\d*)$'] = 'index.php?pagename=$matches[1]&id=$matches[2]';
    return $newrules + $rules;
}

/* adding new rewrite rule
------------------------------------------------------- */
function hdv_insert_query_vars($vars)
{
    array_push($vars, 'id');
    return $vars;
}
