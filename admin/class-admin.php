<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       zymplify-web-form
 * @since      1.0.0
 *
 * @package    Zymplify_web_form
 * @subpackage Zymplify_web_form/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Zymplify_web_form
 * @subpackage Zymplify_web_form/admin
 * @author     Asad <asad@webforms.com>
 */
class zwf_Admin {
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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Zymplify_web_form_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Zymplify_web_form_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Zymplify_web_form_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Zymplify_web_form_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/admin.js', array( 'jquery' ), $this->version, false );

	}

	/*On Click Event - authenticate button in admin panel*/
	public function authenticate_user_ajax() { ?>
        <script type="text/javascript" >
        jQuery(document).ready(function($) {

            var data = {
                'action': 'my_action',
                'whatever': 1234
            };

            jQuery('#zwf_campaign_submit').click(function(){

                event.preventDefault();
                var currentElement = this;
                var isValid = true;

                if(
                	jQuery("#auth-username").val() == ""
                	||
                	jQuery("#auth-username").val() == "undefined"
                	||
                	jQuery("#auth-username").val() == null
                  )
                {
                	isValid = false;
                }

                if(
                	jQuery("#auth-password").val() == ""
                	||
                	jQuery("#auth-password").val() == "undefined"
                	||
                	jQuery("#auth-password").val() == null
                  )
                {
                	isValid = false;
                }

                if (isValid) {
                	
                	jQuery(currentElement).text("Authenticating ...");

	                $("#loading-image").show();
	                var xhr = jQuery.post(ajaxurl, data, function(response) {
	                	// jQuery("#authenticate-form").fadeOut().show();
	                	
	                	if(response != "")
	                	{

	                		// jQuery(currentElement).val("Authenticated");
		                	var parsedMeta = JSON.parse(response)
		                    jQuery.each(parsedMeta, function(i,data) {
		                    	
		                        $("#campaing_table").css('visibility','visible');
		                        $("#campaing_table")
		                        .append("<tr>"+
		                            "<td>" + data.reference_id + "</td>"+
		                            "<td>" + data.title + "</td>"+
		                            "<td>" + data.type + "</td>"+
		                            "</tr>");
		                    });

		                    jQuery(currentElement).fadeOut().hide();
	                		jQuery("#authenticate-form").fadeOut().hide();
	                	}
	                	else
	                	{	
	                		jQuery(currentElement).text("Submit");
	                		jQuery("#zwf_admin_error").show();
	                	}


	                });

	                xhr.done(function() {
					    console.log("success");
					  })
					  .fail(function() {
					    console.log( "error" );
					  })
					  .always(function() {
	                	$("#loading-image").hide();
					    console.log( "finished" );
					  });
                }else{
                	alert("Fill the requried fields!")
                	jQuery(currentElement).text("Authenticate");
                	return false;
                }
                
            });
        });
        </script> <?php
    }

    public function authenticate_user_ajax_response() {

    	$is_zymplify_user_authenticated = esc_attr(get_option('is_zymplify_user_authenticated', ''));

    	if(!$is_zymplify_user_authenticated){
	    	$activatorObj = new Zymplify_Web_Forms_Activator;
	    	$activatorObj->activate();
    	}

        global $wpdb;

        $results = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."zymplify_campaigns LIMIT 0,50 ");
        
        if($wpdb->num_rows > 0){
        	print_r(json_encode($results));

        	$option_name = 'is_zymplify_user_authenticated' ;
			$new_value = '1';
			 
			if ( get_option( $option_name ) !== false ) {
			    // The option already exists, so update it.
			    update_option( $option_name, $new_value );
			 
			} else {
			    // The option hasn't been created yet, so add it with $autoload set to 'no'.
			    $deprecated = null;
			    $autoload = 'no';
			    add_option( $option_name, $new_value, $deprecated, $autoload );
			}
        }
        else
        	echo "";
        wp_die(); 
    }

	public function add_menus() {

        add_menu_page(
            'options-general.php',
            $this->plugin_name,
            'manage_options',
            'zymplify-web-form',
            [$this, 'render']
        );
     //    add_submenu_page(
	    //     $this->plugin_name, // top level menu page
	    //     'Zymplify Web Form Settings', // title of the settings page
	    //     'Settings', // title of the submenu
	    //     'manage_options', // capability of the user to see this page
	    //     'settings-page', // slug of the settings page
	    //     [$this, 'render_settings_page'] // callback function when rendering the page
	    // );
	    
    }

    public function settings_init() {
	    add_settings_section(
	        'settings-section', // id of the section
	        'My Settings', // title to be displayed
	        '', // callback function to be called when opening section, currently empty
	        'settings-page' // page on which to display the section
	    );

	    register_setting(
		    'settings-page', // option group
		    'setting_campaign_id'
		);

		add_settings_field(
		    'setting-campaign-id', // id of the settings field
		    'Select campaign for shortcode [zymplify-web-form]', // title
		    [$this, 'settings_cb'], // callback function
		    'settings-page', // page on which settings display
		    'settings-section' // section on which to show settings
		);
	}

	public function settings_cb() {
	    $setting_campaign_id = esc_attr(get_option('setting_campaign_id', ''));
	    ?>

		<div class="row" style="width: 240px;">
	      <select name="setting_campaign_id" id="setting_campaign_id" class="widefat col-md-6">
	      <?php
	      // Your options array
	      $options = array(
	        ''        => __( 'Select', 'text_domain' ),
	      );
	      global $wpdb;
	      $results = $wpdb->get_results( "SELECT reference_id as id,title FROM ".$wpdb->prefix."zymplify_campaigns ");

	      foreach ($results as $key => $value) {
	        $options[$value->id] = __( $value->title, 'text_domain' );
	      }
	      // echo "<pre>"; echo print_r($options); echo "</pre>"; die;

	      // Loop through options and add each one to the select dropdown
	      foreach ( $options as $key => $name ) {
	        echo '<option value="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" '. selected( $setting_campaign_id, $key, false ) . '>'. $name . '</option>';
	      } ?>
	      </select>
	    </div>

		<?php
	}

    public function render() {
        require_once plugin_dir_path(dirname(__FILE__)).'admin/partials/zwf-admin-display.php';
    }

    public function render_settings_page() {

        require_once plugin_dir_path(dirname(__FILE__)).'admin/partials/settings-display.php';
    }

}
