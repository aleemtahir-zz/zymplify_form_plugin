<?php

/**
 * Fired during plugin activation
 *
 * @link       zymplify-web-forms
 * @since      1.0.0
 *
 * @package    Zymplify_Web_Forms
 * @subpackage Zymplify_Web_Forms/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run the api(s).
 *
 * @since      1.0.0
 * @package    Zymplify_Web_Forms
 * @subpackage Zymplify_Web_Forms/includes
 * @author     Asad <asad@webforms.com>
 */
class Zymplify_Web_Forms_RestApi {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		add_action( 'rest_api_init', function () {
		  // register_rest_route( 'hello/v1', '/categories/(?P<id>\d+)', array(
		  register_rest_route( 'zymplify-web-forms/v1', '/categories', array(
		    'methods' => 'GET',
		    'callback' => 'zymplify_get_all_categories',
		  ) );
		} );

	}

	/**
	 * Grab latest post title by an author!
	 *
	 * @param array $data Options for the function.
	 * @return string|null Post title for the latest,  * or null if none.
	 */
	public static function zymplify_get_all_categories(WP_REST_Request $request) {

		$args 			= $request->get_params();
		$default_params =  	self::categories_default_params();

  		$args_p = wp_parse_args($args,$default_params);
  		$categories = get_terms($args_p);

		if ( empty( $categories ) ) {
			return [];
		}
	 
	  	return $categories;

	}

	static function categories_default_params(){

		return array(
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
			            'name' => 'sdfs',
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
	}

}

function zymplify_get_all_categories(){
	$list = Zymplify_Web_Forms_RestApi::get_all_categories();
	return $list;
}
