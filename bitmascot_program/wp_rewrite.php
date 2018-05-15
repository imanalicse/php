<?php
//SOURCE: http://wordpress.stackexchange.com/questions/5413/need-help-with-add-rewrite-rule
// put these code in function.php
// this is for http://localhost/wordpress34/designers/aa/2/
// or
// this is for http://localhost/wordpress34/designers/2/aa/

add_action( 'init', 'wa_init' );
function wa_init()
{
    add_rewrite_rule(
        'product-detail/([^/]+)/?',
        'index.php?pagename=product-detail&product_slug=$matches[1]',
        'top' );

    flush_rewrite_rules();
}

add_filter( 'query_vars', 'wa_query_vars' );
function wa_query_vars( $query_vars )
{
    $query_vars[] = 'product_slug';
    return $query_vars;
}

// get these value use

echo $orderby = get_query_var('product_slug');
echo '<pre>';
print_r($wp_query);
echo '</pre>';

// study now here
//http://wordpress.stackexchange.com/questions/59827/using-wp-rewrite-to-rewrite-custom-urls-in-this-scenario