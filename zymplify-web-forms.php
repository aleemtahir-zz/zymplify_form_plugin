<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              zymplify-web-form
 * @since             1.0.0
 * @package           Zymplify_Web_Forms
 *
 * @wordpress-plugin
 * Plugin Name:       Zymplify Web Forms 
 * Plugin URI:        zymplify-web-form
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Asad
 * Author URI:        zymplify-web-form
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       zymplify-web-form
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'ZYMPLIFY_WEB_FORM_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-zymplify-web-form-activator.php
 */
function activate_zwf() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-activator.php';
	Zymplify_Web_Forms_Activator::activate();
	// require_once plugin_dir_path( __FILE__ ) . 'includes/class-restapi.php';
	// Zymplify_Web_Forms_RestApi::activate();

	// run script after activation
	wp_enqueue_script( 'bts', plugin_dir_url( __FILE__ ) . 'admin/js/bts.js', array(), false, false );
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-zymplify-web-form-deactivator.php
 */
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-deactivator.php';
function deactivate_zwf() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-deactivator.php';
	Zymplify_Web_Forms_Deactivator::deactivate();
}

// Active will work from Authenticate Button
// register_activation_hook( __FILE__, 'activate_zwf' );
register_deactivation_hook( __FILE__, 'deactivate_zwf' );
register_uninstall_hook( __FILE__, 'deactivate_zwf' );


/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-forms.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_zymplify_web_forms() {

	$plugin = new ZWF_Forms();
	$plugin->run();

}

add_action( 'rest_api_init', function () {

    register_rest_route( 'zymplify-web-forms/v1', '/categories', array(
		'methods' => 'GET',
		'callback' => 'zymplify_get_all_categories',
	) );

	register_rest_route('zymplify-web-forms/v1', '/post', 
        array(
            array('methods' => 'GET',
                 'callback' => 'zymplify_get_all_posts',
            ), 
            array('methods' => 'POST',
                 'callback' => 'zymplify_add_post'
            )
        ) 
    );
});

/*add_action( 'rest_api_init', function () {
	// register_rest_route( 'hello/v1', '/categories/(?P<id>\d+)', array(
	register_rest_route( 'zymplify-web-forms/v1', '/categories', array(
		'methods' => 'GET',
		'callback' => 'zymplify_get_all_categories',
	) );
});*/

function zymplify_get_all_categories(WP_REST_Request $request){
	$result 		= array( 'rows' => 0, 'result' => array()  );
	$args 			= $request->get_params();
	$default_params =  array(
					    'taxonomy' => 'category',
					    'hide_empty' => false,
					    'orderby' => 'count',
			            'order' => 'ASC',
			            'hide_empty' => true, //can be 1, '1' too
			            'include' => 'all', //empty string(''), false, 0 don't work, and return empty array
			            'exclude' => 'all', //empty string(''), false, 0 don't work, and return empty array
			            'exclude_tree' => 'all', //empty string(''), false, 0 don't work, and return empty array
			            'number' => false, //can be 0, '0', '' too
			            'offset' => '',
			            'fields' => 'all',
			            'name' => '',
			            'slug' => '',
			            'hierarchical' => true, //can be 1, '1' too
			            'search' => '',
			            'name__like' => '',
			            'description__like' => '',
			            'pad_counts' => false, //can be 0, '0', '' too
			            'get' => '',
			            'child_of' => false, //can be 0, '0', '' too
			            'childless' => false,
			            'cache_domain' => 'core',
			            'update_term_meta_cache' => true, //can be 1, '1' too
			            'meta_query' => '',
			            'meta_key' => array(),
			            'meta_value'=> '',
				);

		$args_p 			= wp_parse_args($args,$default_params);
		$result['result'] 	= get_terms($args_p);
		$result['rows']     = count($result['result']);

	return $result;
}

function zymplify_get_all_posts(WP_REST_Request $request){

	$result 		= array( 'rows' => 0, 'result' => array()  );
	$args 			= 	$request->get_params();
	/*$default_params =  	array(
				    		'numberposts'      => 5,
					        'category'         => 0,
					        'orderby'          => 'date',
					        'order'            => 'DESC',
					        'include'          => array(),
					        'exclude'          => array(),
					        'meta_key'         => '',
					        'meta_value'       => '',
					        'post_type'        => 'post',
					        'suppress_filters' => true,
						);*/

	$default_params =  	array(
				    		'posts_per_page'   => 5,
					        'category'         => 0,
					        'name'         	   => '',
					        'orderby'          => 'date',
					        'order'            => 'DESC',
					        'include'          => array(),
					        'exclude'          => array(),
					        'meta_key'         => '',
					        'meta_value'       => '',
					        'post_type'        => 'post',
					        'suppress_filters' => true,
						);

	$args_p     	  = wp_parse_args($args,$default_params);
	$my_query   	  = new WP_Query( $args );
	$result['rows']   = intval($my_query->found_posts);
	$result['result'] = $my_query->posts;
	
	// $posts  = get_posts($args_p);
	
	// if (empty($posts )) {
	// 	return [];
	// }

	return $result;
}

function zymplify_add_post(WP_REST_Request $request){

	$args 			= 	$request->get_params();
	$default_params =  	array(
				    		'post_date' => '',
							'post_date_gmt' => '',
							'post_content' => '',
							'post_content_filtered' => '',
							'post_title' => '',
							'post_excerpt' => '',
							'post_status' => '',
							'post_type' => '',
							'comment_status' => '',
							'ping_status' => '',
							'post_password' => '',
							'post_name' => '',
							'to_ping' => '',
							'pinged' => '',
							'post_modified' => '',
							'post_modified_gmt' => '',
							'post_parent' => '',
							'menu_order' => '',
							'post_mime_type' => '',
							'guid' => '',
							'post_category' => array(),
							'tags_input' => '',
							'tax_input' => '',
							'meta_input' => ''

						);

	/*'post_author'           => '',
    'post_content'          => '',
    'post_content_filtered' => '',
    'post_title'            => '',
    'post_excerpt'          => '',
    'post_status'           => 'draft',
    'post_type'             => 'post',
    'comment_status'        => '',
    'ping_status'           => '',
    'post_password'         => '',
    'to_ping'               => '',
    'pinged'                => '',
    'post_parent'           => 0,
    'menu_order'            => 0,
    'guid'                  => '',
    'import_id'             => 0,
    'context'               => '',*/

	$args_p 			= wp_parse_args($args,$default_params);
	$new_inserted_post  = wp_insert_post($args_p);
	if ($new_inserted_post == 1) {
		return $new_inserted_post;
	}
	return $new_inserted_post;
}




wp_enqueue_script( 'bts', plugin_dir_url( __FILE__ ) . 'admin/js/bts.js', array(), '', false );
run_zymplify_web_forms();