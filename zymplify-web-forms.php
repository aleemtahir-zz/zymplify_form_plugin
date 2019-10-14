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

    register_rest_route( 'zymplify-web-forms/v1', '/api-clients', array(
		'methods' => 'GET',
		'callback' => 'zymplify_get_all_api_clients',
	) );
});

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
							'post_status' => 'publish',
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
		
	if (!empty($_FILES['featured_image'])) {		

		if (is_array($upload_id = validateImage($_FILES))) 
		{
			return $upload_id;
		}
	}

	$new_inserted_post  = wp_insert_post($args_p);

	if ($new_inserted_post > 0) {

		if (!empty($_FILES)) {
			$attachment_id = uploadAttachmentImage($new_inserted_post);
		}

		return $new_inserted_post;
	}

	return $new_inserted_post;
}

function validateImage($files){

	$postPicture 	= $files['featured_image'];
	$new_file_mime  = mime_content_type( $postPicture['tmp_name'] );
	$error 			= array();
	if( empty( $postPicture ) )
		$error['error'][] =  'File is not selected.';
	 
	if( $postPicture['error'] )
		$error['error'][] =  $postPicture['error'];
	 
	if( $postPicture['size'] > wp_max_upload_size() )
		$error['error'][] =  'It is too large than expected.';
	 
	if( !in_array( $new_file_mime, get_allowed_mime_types() ) )
		$error['error'][] =  'WordPress doesn\'t allow this type of uploads.';
	
	if (count($error) > 0) {
		return $error;
	}
	return true;
}

function uploadAttachmentImage($post_id){

	$error = array();		
	$wordpress_upload_dir = wp_upload_dir();
	// $wordpress_upload_dir['path'] is the full server path to wp-content/uploads/2017/05, for multisite works good as well
	// $wordpress_upload_dir['url'] the absolute URL to the same folder, actually we do not need it, just to show the link to file
	$i = 1; // number of tries when the file with the same name is already exists
	 
	$postPicture 	= $_FILES['featured_image'];
	$new_file_path  = $wordpress_upload_dir['path'] . '/' . $postPicture['name'];
	$new_file_mime  = mime_content_type( $postPicture['tmp_name'] );

	while( file_exists( $new_file_path ) ) {
		$i++;
		$new_file_path = $wordpress_upload_dir['path'] . '/' . $i . '_' . $postPicture['name'];
	}
	 
	// looks like everything is OK
	if( move_uploaded_file( $postPicture['tmp_name'], $new_file_path ) ) {
	 
	 
		$upload_id = wp_insert_attachment( array(
			'guid'           => $new_file_path, 
			'post_mime_type' => $new_file_mime,
			'post_title'     => preg_replace( '/\.[^.]+$/', '', $postPicture['name'] ),
			'post_content'   => '',
			'post_status'    => 'inherit'
		), $new_file_path, $post_id);
	 
		// wp_generate_attachment_metadata() won't work if you do not include this file
		require_once( ABSPATH . 'wp-admin/includes/image.php' );
	 
		// Generate and save the attachment metas into the database
		wp_update_attachment_metadata( $upload_id, wp_generate_attachment_metadata( $upload_id, $new_file_path ) );
		set_post_thumbnail( $post_id, $upload_id );
	 	return $upload_id;
		// Show the uploaded file in browser
		// wp_redirect( $wordpress_upload_dir['url'] . '/' . basename( $new_file_path ) );
	 
	}
}

function zymplify_get_all_api_clients(WP_REST_Request $request){

	global $wpdb;
	$sql 	  = "SELECT * FROM wp_zymplify_api_clients";
	$response = $wpdb->query($sql);
	return $response;
}

wp_enqueue_script( 'bts', plugin_dir_url( __FILE__ ) . 'admin/js/bts.js', array(), '', false );
run_zymplify_web_forms();