<?php // (C) Copyright Bobbing Wide 2015-2018

genesis_hm_functions_loaded();


/**
 * Display footer credits for the genesis-hm theme
 */	
function hm_footer_creds_text( $text ) {
	do_action( "hm_add_shortcodes" );
	$text = "[bw_wpadmin]";
  $text .= '<br />';
	$text .= "[bw_copyright]"; 
	$text .= '<hr />';
	$text .= 'Website designed and developed by [bw_link text="Herb Miller" herbmiller.me] of';
	$text .= ' <a href="//www.bobbingwide.com" title="Bobbing Wide - web design, web development">[bw]</a>';
	$text .= '<br />';
	$text .= '[bw_power]';
  return( $text );
}

/**
 * Display the post info in our style
 *
 * We only want to display the post date and post modified date plus the post_edit link. 
 * 
 * Note: On some pages the post edit link appeared multiple times - so we had to find a fancy way
 * of turning it off, except when we really wanted it. 
 * Solution was to not use "genesis_post_info" but to expand shortcodes ourselves  
 *
 *
 */
function genesis_hm_post_info() {
	remove_filter( "genesis_edit_post_link", "__return_false" );
	$output = genesis_markup( array(
    'html5'   => '<p %s>',
    'xhtml'   => '<div class="post-info">',
    'context' => 'entry-meta-before-content',
    'echo'    => false,
	) );
	$string = sprintf( __( 'Published: %1$s', 'genesis-hm' ), '[post_date]' );
	$string .= '<span class="splitbar">';
	$string .= ' | ';
	$string .= '</span>';
	$string .= '<span class="lastupdated">';
	$string .= sprintf( __( 'Last updated: %1$s', 'genesis-hm' ), '[post_modified_date]' );
	$string .= '</span>';
  $string .= ' [post_edit]';
	//$output .= apply_filters( 'do_shortcodes', $string);
	$output .= do_shortcode( $string );
	$output .= genesis_html5() ? '</p>' : '</div>';  
	echo $output;
	add_filter( "genesis_edit_post_link", "__return_false" );
}

/**
 * Display the sidebar for the given post type
 *
 * Normally we just append -widget-area but for some post types we override it 
 *
 * Post type  | Sidebar used
 * ---------- | -------------
 * oik_premiumversion | oik_pluginversion-widget-area
 * oik_sc_param | sidebar-alt
 * 
 * 
 */
function genesis_hm_get_sidebar() {
	//* Output primary sidebar structure
	genesis_markup( array(
		'html5'   => '<aside %s>',
		'xhtml'   => '<div id="sidebar" class="sidebar widget-area">',
		'context' => 'sidebar-primary',
	) );
	do_action( 'genesis_before_sidebar_widget_area' );
	$post_type = get_post_type();
	$cpts = array( "oik_premiumversion" => "oik_pluginversion-widget-area" 
							 , "oik_sc_param" => "sidebar-alt"
							 , "attachment" => "sidebar-alt"
							 );
	$dynamic_sidebar = bw_array_get( $cpts, $post_type, "$post_type-widget-area" ); 
	dynamic_sidebar( $dynamic_sidebar );
	do_action( 'genesis_after_sidebar_widget_area' );
	genesis_markup( array(
		'html5' => '</aside>', //* end .sidebar-primary
		'xhtml' => '</div>', //* end #sidebar
	) );
} 

/**
 * Implement 'genesis_hm_pre_get_option_site_layout' filter 
 *
 * The _genesis_layout has not been defined so we need to decide based on the 
 * previous setting for the Artisteer theme.
 *
 * @param string $layout originally null
 * @param string $setting the current default setting 
 * @return string $layout which is either to have a sidebar or not
 */
function genesis_hm_pre_get_option_site_layout( $layout, $setting ) {
	//bw_trace2();
	$artisteer_sidebar = genesis_get_custom_field( "_theme_layout_template_default_sidebar" );
	if ( $artisteer_sidebar ) {	
		$layout = __genesis_return_content_sidebar();
	} else {
		// $layout = __genesis_return_full_width_content();
	}
	return( $layout );
}


/**
 * Register the hooks for this theme
 * 
 */
function genesis_hm_functions_loaded() {
	//* Child theme (do not remove) - is this really necessary? 
	define( 'CHILD_THEME_NAME', 'Herb Miller' );
	define( 'CHILD_THEME_URL', 'http://www.bobbingwide.com/oik-themes/genesis-hm' );
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		$timestamp = filemtime( get_stylesheet_directory() . "/style.css" );
		define( 'CHILD_THEME_VERSION', $timestamp );
	} else { 
		define( 'CHILD_THEME_VERSION', '0.0.2' );
	}
	// Start the engine	- @TODO Is this necessary?
	include_once( get_template_directory() . '/lib/init.php' );
	
	if ( defined( "GENESIS_ALL" ) && GENESIS_ALL ) {
  	add_action( "all", "genesis_all", 10, 2 );
	}
	//* Add HTML5 markup structure
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list' ) );

	//* Add viewport meta tag for mobile browsers
	add_theme_support( 'genesis-responsive-viewport' );
	
	// Add support for structural wraps
	add_theme_support( 'genesis-structural-wraps', array(
	 'header',
	//	'nav',
	//        'subnav',
		'site-inner',
		'footer-widgets'
	) );

	//* Add support for custom background
	add_theme_support( 'custom-background' );

	//* Add support for 4-column footer widgets - requires extra CSS
	add_theme_support( 'genesis-footer-widgets', 4 );

	add_filter( 'genesis_footer_creds_text', "hm_footer_creds_text" );
	
  add_filter( 'genesis_pre_get_option_site_layout', 'genesis_hm_pre_get_option_site_layout', 10, 2 );
	
	//remove_action( 'genesis_entry_footer', 'genesis_post_meta' );
	
	// Remove post info
	remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
	add_action( 'genesis_entry_footer', 'genesis_hm_post_info' );
	//add_filter( "genesis_edit_post_link", "__return_false" );
	
	
  //genesis_hm_register_sidebars();
	//add_filter( 'wp_nav_menu_items', 'genesis_hm_wp_nav_menu_items', 10, 2 );
	
	//* Enqueue scripts and styles
	add_action( 'wp_enqueue_scripts', 'genesis_hm_wp_enqueue_scripts' );
	
	//* Customize the entry meta in the entry footer
	add_filter( 'genesis_post_meta', 'genesis_hm_post_meta_filter' );
	
	add_action( 'oik_loaded', 'genesis_hm_oik_loaded' );
	
	// Let this be controlled by the individual author
	//add_filter( 'get_the_author_genesis_author_box_single', '__return_true' );
	//add_filter( 'get_the_author_genesis_author_box_archive', '__return_true' );
	add_action( "after_setup_theme", "genesis_hm_after_setup_theme" );
	
}

/**
 * Create a responsive menu without JavaScript
 *
 * Is this sensible?
 * It's not as easy as using the global.js which already exists
 
 *
 * 
 */ 
function genesis_hm_wp_nav_menu_items( $menu, $args ) {
	bw_trace2();
	if ( 'primary' === $args->menu->name ) {
		$rmi = '<div class="responsive-menu-icon"></div>';
		$menu = $rmi . $menu ;
	}
	return( $menu );
}


/**
 * Enqueue scripts and styles
 *
 * @TODO We seem to be half way between removing global.js and using CSS.
 * 
 * 
 */
function genesis_hm_wp_enqueue_scripts() {
	wp_enqueue_script( 'hm-global', get_bloginfo( 'stylesheet_directory' ) . '/js/global.js', array( 'jquery' ), '1.0.0' );
	wp_enqueue_style( 'dashicons' );
	//wp_enqueue_style( 'rjdap-google-fonts', '//fonts.googleapis.com/css?family=Ek+Mukta:200,800', array(), CHILD_THEME_VERSION );
}

function genesis_hm_oik_loaded() {
	//do_action( "oik_add_shortcodes" );
	add_filter( "wp_title", "genesis_hm_wp_title", 16, 3 ); 
	do_action( "oik_add_shortcodes" );
}

/** 
 * Implement 'wp_title' filter after WPSEO_Frontend::title
 * 
 */
function genesis_hm_wp_title( $title, $sep, $seplocation ) {
	if ( false !== strpos( $title, "[" ) ) {
		do_action( "oik_add_shortcodes" );
		$title = bw_do_shortcode( $title );
		$title = strip_tags( $title );
	}
	return( $title );
}

/**
 * Implement "genesis_post_meta" filter
 * 
 * @param string $post_meta
 * @return string what we want it to be
 * 
 */
function genesis_hm_post_meta_filter( $post_meta ) {
	$post_meta = '[post_categories before="Categories: "][post_tags before="Tags: "]';
	return $post_meta;
}



function genesis_hm_after_setup_theme() {
	add_theme_support( 'editor-color-palette',
        '#d2d28e',	// Nav bar menu background
        '#5a5f21',	// Hovered links
        '#eee',     //
        '#d5e0c1'		// Footer background
    );
		
	add_theme_support( 'align-wide' );

}



