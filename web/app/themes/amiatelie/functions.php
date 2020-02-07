<?php
function my_theme_enqueue_styles() {

    $parent_style = 'parent-style'; // This is 'twentyfifteen-style' for the Twenty Fifteen theme.

    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'amiatelie-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style ),
        wp_get_theme()->get('Version')
    );
}
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );

// add_action( 'init', 'jk_remove_wc_breadcrumbs' );
// function jk_remove_wc_breadcrumbs() {
//     remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );
// }

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function amiatelie_widgets_init() {

  register_sidebar( array(
		'name'          => __( 'Footer 3', 'twentyseventeen' ),
		'id'            => 'sidebar-4',
		'description'   => __( 'Add widgets here to appear in your footer.', 'twentyseventeen' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'amiatelie_widgets_init' );

function remove_woo_header_footer($object){
   remove_action('woocommerce_email_header', array( $object, 'email_header' ));
   remove_action('woocommerce_email_footer', array( $object, 'email_footer' ));
}
add_action( 'woocommerce_email', 'remove_woo_header_footer' );
// Determine if it's an email using the WooCommerce email header
add_action( 'woocommerce_email_header', function(){ add_filter( "better_wc_email", "__return_true" ); } );

// Hide the WooCommerce Email header and footer
add_action( 'woocommerce_email_header', function(){ ob_start(); }, 1 );
add_action( 'woocommerce_email_header', function(){ ob_get_clean(); }, 100 );
add_action( 'woocommerce_email_footer', function(){ ob_start(); }, 1 );
add_action( 'woocommerce_email_footer', function(){ ob_get_clean(); }, 100 );
// Selectively apply WPBE template if it's a WooCommerce email
add_action( 'phpmailer_init', 'better_phpmailer_init', 20 );
function better_phpmailer_init( $phpmailer ){
	// this filter will return true if the woocommerce_email_header action has run
	if ( apply_filters( 'better_wc_email', false ) ){
		global $wp_better_emails;

		// Add template to message
		$phpmailer->Body = $wp_better_emails->set_email_template( $phpmailer->Body );

		// Replace variables in email
		$phpmailer->Body = apply_filters( 'wpbe_html_body', $wp_better_emails->template_vars_replacement( $phpmailer->Body ) );
	}
}

/**
 * WooCommerce Extra Feature
 * --------------------------
 *
 * Change number of related products on product page
 * Set your own value for 'posts_per_page'
 *
 */
function woo_related_products_limit() {
  global $product;

	$args['posts_per_page'] = 6;
	return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'jk_related_products_args' );
  function jk_related_products_args( $args ) {
	$args['posts_per_page'] = 3; // 4 related products
	$args['columns'] = 3; // arranged in 2 columns
	return $args;
}

add_filter('loop_shop_columns', 'loop_columns');
if (!function_exists('loop_columns')) {
	function loop_columns() {
		return 3; // 3 products per row
	}
}

add_action('woocommerce_before_shop_loop','rev_slider_in_shop');
function rev_slider_in_shop() {
  echo is_home() || is_front_page() ? do_shortcode( '[smartslider3 slider=3]' ) : '';
}

add_action( 'woocommerce_archive_description', 'woocommerce_category_image', 2 );
function woocommerce_category_image() {
    if ( is_product_category() ){
	    global $wp_query;
	    $cat = $wp_query->get_queried_object();
	    $thumbnail_id = get_woocommerce_term_meta( $cat->term_id, 'thumbnail_id', true );
	    $image = wp_get_attachment_url( $thumbnail_id );
	    if ( $image ) {
		    echo '<img style="padding-bottom: 1.5em;" src="' . $image . '" alt="' . $cat->name . '" />';
		}
	}
}

?>
