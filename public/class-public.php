<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       zymplify-web-forms
 * @since      1.0.0
 *
 * @package    Zymplify_Web_Forms
 * @subpackage Zymplify_Web_Forms/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Zymplify_Web_Forms
 * @subpackage Zymplify_Web_Forms/public
 * @author     Asad <asad@webforms.com>
 */
class zwf_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Zymplify_Web_Forms_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Zymplify_Web_Forms_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/zymplify-web-forms-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Zymplify_Web_Forms_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Zymplify_Web_Forms_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/zymplify-web-forms-public.js', array( 'jquery' ), $this->version, false );

		wp_localize_script( $this->plugin_name, 'frontend', array(
            // URL to wp-admin/admin-ajax.php to process the request
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            // generate a nonce with a unique ID "myajax-post-comment-nonce"
            // so that you can check it later when an AJAX request is sent
            // 'security' => wp_create_nonce( 'my-special-string' )
        ));

	}

/*	public function zwf_form_submit() {
        global $wpdb;

        // $results = $wpdb->get_results( "SELECT reference_id as id,title FROM ".$wpdb->prefix."zymplify_campaigns ");
        print_r("success");
        wp_die(); 
    }

    public function make_campaigns_form() {
        global $wpdb;

        if(!empty($_POST['campaign_id'])){
            $sql = "SELECT f.reference_id as field_id, f.name as field_name, f.label as field_label, f.type as field_type, v.value as field_value, v.order_id as field_order
                    FROM ".$wpdb->prefix."zymplify_campaigns c 
                    INNER JOIN ".$wpdb->prefix."zymplify_campaigns_form_fields f ON c.reference_id = f.campaign_id 
                    LEFT JOIN ".$wpdb->prefix."zymplify_campaigns_form_field_values v ON f.reference_id = v.form_id 
                    WHERE c.reference_id = $_POST[campaign_id]
                    ORDER BY f.reference_id ASC ";

            $results = $wpdb->get_results($sql);
            print_r(json_encode($results));
        }

        wp_die(); 
    }*/

    /**
     * Render the view using MVC pattern.
     */
    public function renderFormUsingShortcode($atts, $content = null) {

    	//set default attributes and values
	    $shortcodeValues = shortcode_atts( array(
	        'campaign'  => null
	    ), $atts );
	    
        require_once plugin_dir_path(dirname(__FILE__)).'public/partials/zymplify-web-forms-public-display.php';
        return ob_get_clean();
    }

}
