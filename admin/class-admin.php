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

	public function zwf_js() { ?>
        <script type="text/javascript" >
        jQuery(document).ready(function($) {

            if('<?php echo get_option( "zwf_user_token")?>' != ''){
            	jQuery('#auth_token').val('<?php echo get_option( "zwf_user_token")?>');
            	jQuery('#zwf_sync_wrap').show();

            	var data = {
	                'action': 'get_old_campaigns',
	            };

            	$("#loading-image").show();
				var xhr = jQuery.post(ajaxurl, data, function(response) {
					if(response != "")
					{
						jQuery('#campaign_table .tbl_data').remove();
				    	var parsedMeta = JSON.parse(response)
				        jQuery.each(parsedMeta, function(i,data) {
				        	
				            $("#campaign_table").css('visibility','visible');
				            $("#campaign_table")
				            .append("<tr class='tbl_data'>"+
				                "<td>" + data.reference_id + "</td>"+
				                "<td>" + data.title + "</td>"+
				                "</tr>");
				        });

						jQuery("#zwf_admin_error").fadeOut().hide();
					}
					else
					{	
						jQuery("#zwf_admin_error").text('*No Data Found!').show();
					}
				});

				xhr.always(function() {
					$("#loading-image").hide();
				});
            }

            jQuery('#zwf_auth_btn').click(function(){

                event.preventDefault();
                var currentElement = this;
                var isValid = true;

                var token = jQuery('#auth_token').val();

	            var data = {
	                'action': 'check_auth',
	                'user_token': token,
	            };

                if(
                	jQuery("#auth_token").val() == ""
                	||
                	jQuery("#auth_token").val() == "undefined"
                	||
                	jQuery("#auth_token").val() == null
                  )
                {
                	isValid = false;
                }

                if(isValid)
                {
                	var xhr = jQuery.post(ajaxurl, data, function(response) {
		            	// jQuery("#authenticate-form").fadeOut().hide();
		            	// console.log(response);
		            	if(response == "done")
		            	{
		            		jQuery('#zwf_sync_wrap').show();
		            		jQuery('#zwf_admin_success')
		            		.show()
		            		.html("<b>*User Token has been saved. Now Sync the Data.</b><br><br>");
		            		jQuery("#zwf_admin_error").hide();
		            		jQuery("#campaign_table").css("visibility","hidden");
		            	}
		            	else
		            	{	
		            		// jQuery(currentElement).text("Submit");
		            		// jQuery("#zwf_admin_error").show();
		            	}
	            	});

		            xhr.done(function() {
					    // console.log("success");
					  })
					.fail(function() {
						// console.log( "error" );
					})
					.always(function() {
						// $("#loading-image").hide();
						// console.log( "finished" );
					});
                }
                else
                {
                	alert("Fill the requried fields!")
                	// jQuery(currentElement).text("Authenticate");
                	return false;
                }
            });

            jQuery('#zwf_sync_btn').click(function(){

            	var data = {
	                'action': 'get_campaigns',
	                // 'user_token': token,
	            };

            	$("#loading-image").show();
				var xhr = jQuery.post(ajaxurl, data, function(response) {
					// jQuery("#authenticate-form").fadeOut().show();
					// console.log(response);
					if(response != "")
					{
						// jQuery(currentElement).val("Authenticated");
						jQuery('#campaign_table .tbl_data').remove();
				    	var parsedMeta = JSON.parse(response)
				        jQuery.each(parsedMeta, function(i,data) {
				        	
				            $("#campaign_table").css('visibility','visible');
				            $("#campaign_table")
				            .append("<tr class='tbl_data'>"+
				                "<td>" + data.reference_id + "</td>"+
				                "<td>" + data.title + "</td>"+
				                // "<td>" + data.type + "</td>"+
				                "</tr>");
				        });

				        // jQuery(currentElement).fadeOut().hide();
						// jQuery("#authenticate-form").fadeOut().hide();
						jQuery("#zwf_admin_error").fadeOut().hide();
						jQuery("#zwf_admin_success").fadeOut().hide();
					}
					else
					{	
						jQuery('#campaign_table').css("visibility","hidden");
						jQuery("#zwf_admin_error").text('*Data has not been synced. Please try again.').show();
					}
				});

				xhr.done(function() {
					// console.log("success");
				})
				.fail(function() {
					// console.log( "error" );
				})
				.always(function() {
					$("#loading-image").hide();
					// console.log( "finished" );
				});
            });
        });
        </script> <?php
    }

    public function check_auth() {

    	$user_token = $_POST['user_token'];
    	$is_zwf_user = esc_attr(get_option('is_zwf_user', ''));
		 
		if ( get_option( 'zwf_user_token' ) !== false ) {
		    // The option already exists, so update it.
		    update_option( 'zwf_user_token', $user_token );
		    echo "done";
		 
		} else {
		    // The option hasn't been created yet, so add it with $autoload set to 'no'.
		    $deprecated = null;
		    $autoload = 'no';
		    // add_option( 'is_zwf_user', '1', $deprecated, $autoload );
		    add_option( 'zwf_user_token', $user_token, $deprecated, $autoload );
		    echo "done";
		}

        wp_die(); 
    }

    public function get_campaigns() {

    	// $is_zwf_user = esc_attr(get_option('is_zwf_user', ''));
    	// ini_set('display_errors',1);
    	// if(!$is_zwf_user){
	    	$deactivatorObj = new Zymplify_Web_Forms_Deactivator;
	    	$deactivatorObj->remove_data();
	    	activate_zwf();
    	// }

        global $wpdb;

        $results = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."zymplify_campaigns  ");
        
        if($wpdb->num_rows > 0){
        	print_r(json_encode($results));

   			// $option_name = 'is_zwf_user' ;
			// $new_value = '1';
			 
			// if ( get_option( $option_name ) !== false ) {
			//     // The option already exists, so update it.
			//     update_option( $option_name, $new_value );
			 
			// } else {
			//     // The option hasn't been created yet, so add it with $autoload set to 'no'.
			//     $deprecated = null;
			//     $autoload = 'no';
			//     add_option( $option_name, $new_value, $deprecated, $autoload );
			// }
        }
        else
        	echo "";
        wp_die(); 
    }

    public function get_old_campaigns() {

        global $wpdb;

        $results = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."zymplify_campaigns  ");
        
        if($wpdb->num_rows > 0){
        	print_r(json_encode($results));
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
