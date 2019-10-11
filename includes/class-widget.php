<?php 


// The widget class
class ZWF_Widget extends WP_Widget {

	// Main constructor
	public function __construct() {
		parent::__construct(
			'zwf_widget',
			__( 'Zymplify Widget', 'text_domain' ),
			array(
				'customize_selective_refresh' => true,
			)
		);
	}

	// The widget form (for the backend )
	public function form( $instance ) {
		// Set widget defaults
		$defaults = array(
			'title'    => '',
			'text'     => '',
			'textarea' => '',
			'checkbox' => '',
			'select'   => '',
		);
		
		// Parse current settings with defaults
		extract( wp_parse_args( ( array ) $instance, $defaults ) ); ?>

		<?php // Widget Title ?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Widget Title', 'text_domain' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>

		<?php // Dropdown ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'select' ); ?>"><?php _e( 'Select', 'text_domain' ); ?></label>
			<select name="<?php echo $this->get_field_name( 'select' ); ?>" id="<?php echo $this->get_field_id( 'select' ); ?>" class="widefat">
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

			// Loop through options and add each one to the select dropdown
			foreach ( $options as $key => $name ) {
				echo '<option value="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" '. selected( $select, $key, false ) . '>'. $name . '</option>';
			} ?>
			</select>
		</p>

	<?php }

	// Update widget settings
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title']    = isset( $new_instance['title'] ) ? wp_strip_all_tags( $new_instance['title'] ) : '';
		$instance['select']   = isset( $new_instance['select'] ) ? wp_strip_all_tags( $new_instance['select'] ) : '';
		return $instance;
	}

	// Display the widget
	public function widget( $args, $instance ) {
		extract( $args );

		//Form Processing Logic here  
        if (isset($_POST['fields'])){

			$post = json_encode($_POST['fields']);
			// print_r($post); //die;

			$url 		= "https://mpf48x2mxa.execute-api.eu-west-1.amazonaws.com/dev/api/contacts";
			$response 	= wp_remote_post( $url,
	    						array(
	    							'headers' => array( 
	    								'Content-Type' => 'application/json' 
	    							),
	    							'body' => $post 
	    						));

			if ( is_array( $response ) ) {
			  	$header = $response['headers']; // array of http header lines
			  	$body = $response['body']; // use the content
		    	$finalized_response = json_decode($body);
			}

			// print_r($body); die;
       		?>
       		<script type="text/javascript">
       			function showSuccessMessage(id)
			    {
			    	// console.log(jQuery('#zwf_submit_msg_'+id));
			    	jQuery('#zwf_submit_msg_'+id).addClass('text-primary').html("Your form has been submitted successfully.");
			    }

       			showSuccessMessage(<?php echo $_POST['fields']['campaignId'] ?>);
       			// jQuery(message).addClass('text-primary').html("Your form has been submitted successfully.");
       		</script>
       		<?php

        }

		// Check the widget options
		$title    = isset( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : '';
		$text     = isset( $instance['text'] ) ? $instance['text'] : '';
		$select   = isset( $instance['select'] ) ? $instance['select'] : '';

		global $wpdb;
		$sql = "SELECT f.reference_id as field_id, f.name as field_name, f.label as field_label, f.type as field_type, f.required as field_required,GROUP_CONCAT(v.value ORDER BY v.order_id ASC) AS field_value
                FROM ".$wpdb->prefix."zymplify_campaigns c 
                INNER JOIN ".$wpdb->prefix."zymplify_campaigns_form_fields f ON c.reference_id = f.campaign_id 
                LEFT JOIN ".$wpdb->prefix."zymplify_campaigns_form_field_values v ON f.reference_id = v.form_id 
                WHERE c.reference_id = $select
                GROUP BY f.reference_id
                ORDER BY f.reference_id ASC ";

        $results = $wpdb->get_results($sql);

		$this->show_form($results, $select);
	}

	public function show_form($results, $campaign_id, $is_shortcode=0){
		$content = '';
		// WordPress core before_widget hook (always include )
		$content .= $before_widget;
		// Display the widget
		$content .= '<div class="widget-text wp_widget_plugin_box">';
			// Display widget title if defined
			if ( $title ) {
				$content .= $before_title . $title . $after_title;
			}
			// Display text field
			// if ( $text ) {
			// 	$content .= '<p>' . $text . '</p>';
			// }
			// Display select field
			if ( $results ) {
				$content .= '<form method="post" action="" class="zwf_form" id="form_'.$campaign_id.'" >';
				foreach ($results as $key => $value) {
					$required = '';

					if($value->field_required == 1)
						$required = 'required';

					if($value->field_type == 1){
						$content .= '<p>'.
						'<div class="form-group" >'.
						'<label for="'.$value->field_name.'">'.$value->field_label.'</label>'.
						'<input class="form-control widefat" id="'.$value->field_id.'" name="fields['.$value->field_name.']" type="text" value="'.$value->field_value.'"'.$required.'/>'.
						'</div>'.
					  	'</p>';
					}
					elseif($value->field_type == 2){
						$content .= '<p>'.
						'<div class="form-group">'.
						'<label for="'.$value->field_name.'">'.$value->field_label.'</label>'.
						'<textarea  class="form-control widefat" 
							id="'.$value->field_id.'" 
							name="fields['.$value->field_name.']" type="text" '.$required.'> '.$value->field_value.'</textarea>'.
						'</div>'.
					  	'</p>';
					}
					elseif($value->field_type == 3){
						
						$content .= '<p>'.
						'<div class="form-group">'.
						'<label for="'.$value->field_id.'">'.$value->field_label.'</label>';
						
						$values = explode(',', $value->field_value);
						foreach ($values as $v) {
							
							$content .= '<input class="form-control widefat" id="'.$value->field_id.'" name="fields['.$value->field_name.']" type="checkbox" value="'.$v.'" '.$required.' />'.$v;
							// $content .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
						}
					  	$content .= '</div>';
					  	$content .= '</p>';
					}
					elseif($value->field_type == 4){
						
						$content .= '<p>'.
						'<div class="form-group">'.
						'<label for="'.$value->field_id.'">'.$value->field_label.'</label>';
						
						$values = explode(',', $value->field_value);
						foreach ($values as $v) {
							
							$content .= '<input class="form-control widefat" id="'.$value->field_id.'" name="fields['.$value->field_name.']" type="radio" value="'.$v.'" '.$required.' />'.$v;
							// $content .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
						}
						$content .= '</div>';
					  	$content .= '</p>';
					}
					elseif($value->field_type == 5){
						
						$content .= '<p>'.
						'<div class="form-group">'.
						'<label for="'.$value->field_id.'">'.$value->field_label.'</label>'.
						'<select name="fields['.$value->field_name.']" id="'.$value->field_id.'" class="form-control widefat" '.$required.'>';
						
						$values = explode(',', $value->field_value);

						foreach ($values as $v) {
							
							$content .= '<option value="'.$v.'" >'. $v . '</option>';
						}
					  	$content .= '</select>';
					  	$content .= '</div>';
					  	$content .= '</p>';
					}
					elseif($value->field_type == 6){
						
						$content .= '<p>'.
						'<div class="form-group">'.
						'<label for="'.$value->field_id.'">'.$value->field_label.'</label>'.
						'<input name="fields['.$value->field_name.']" id="'.$value->field_id.'" class="form-control widefat" type="checkbox" data-toggle="toggle" '.$required.'>';
					  	$content .= '</div>';
					  	$content .= '</p>';
					}
					elseif($value->field_type == 7){
						
						$content .= '<p>'.
						'<div class="form-group">'.
						'<label for="'.$value->field_id.'">'.$value->field_label.'</label>'.
						'<input name="fields['.$value->field_name.']" id="'.$value->field_id.'" class="form-control widefat" type="file" '.$required.'>';
					  	$content .= '</div>';
					  	$content .= '</p>';
					}
					elseif($value->field_type == 8){
						
						$content .= '<p>'.
						'<div class="form-group">'.
						'<label for="'.$value->field_id.'">'.$value->field_label.'</label>'.
						'<input name="fields['.$value->field_name.']" id="'.$value->field_id.'" class="form-control widefat" type="number" '.$required.'>';
					  	$content .= '</div>';
					  	$content .= '</p>';
					}
					elseif($value->field_type == 9){
						
						$content .= '<p>'.
						'<div class="form-group">'.
						'<label for="'.$value->field_id.'">'.$value->field_label.'</label>'.
						'<input name="fields['.$value->field_name.']" id="'.$value->field_id.'" class="form-control widefat" type="datetime-local" '.$required.'>';
					  	$content .= '</div>';
					  	$content .= '</p>';
					}
					elseif($value->field_type == 10){
						
						$content .= '<p>'.
						'<div class="form-group">'.
						'<label for="'.$value->field_id.'">'.$value->field_label.'</label>'.
						'<input name="fields['.$value->field_name.']" id="'.$value->field_id.'" class="form-control widefat" type="tel" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" '.$required.'>';
					  	$content .= '</div>';
					  	$content .= '</p>';
					}
					elseif($value->field_type == 11){
						
						$content .= '<p>'.
						'<div class="form-group">'.
						'<label for="'.$value->field_id.'">'.$value->field_label.'</label>'.
						'<input name="fields['.$value->field_name.']" id="'.$value->field_id.'" class="form-control widefat" type="date" '.$required.'>';
					  	$content .= '</div>';
					  	$content .= '</p>';
					}
					elseif($value->field_type == 12){
						
						$content .= '<p>'.
						'<div class="form-group">'.
						'<label for="'.$value->field_id.'">'.$value->field_label.'</label>'.
						'<input name="fields['.$value->field_name.']" id="'.$value->field_id.'" class="form-control widefat" type="email" '.$required.'>';
					  	$content .= '</div>';
					  	$content .= '</p>';
					}
					elseif($value->field_type == 13){
						
						$content .= '<p>'.
						'<div class="form-group">'.
						'<label for="'.$value->field_id.'">'.$value->field_label.'</label>'.
						'<input name="fields['.$value->field_name.']" id="'.$value->field_id.'" class="form-control widefat" type="url" '.$required.'>';
					  	$content .= '</div>';
					  	$content .= '</p>';
					}
				}

				$content .= '<input type="hidden" name="fields[campaignId]" value="'.$campaign_id.'">';
				$content .= '<input type="hidden" name="fields[channel]" value="155471">';
				$content .= '<input type="hidden" name="fields[client]" value="12">';
				$content .= '<button class="btn btn-default zwf_submit" data-campaign-id="'.$campaign_id.'" type="submit" class="btn btn-primary" name="send">Submit</button>';
				$content .= '<br><br><p id="zwf_submit_msg_'.$campaign_id.'"></p><br><br>';
				$content .= '</form>';
			}
		$content .= '</div>';
		// WordPress core after_widget hook (always include )
		$content .= $after_widget;

		if($is_shortcode)
		{
			ob_start();
			echo $content;
			$output = ob_get_clean();
			return $output;
		}
		else
			echo $content;
	}
}

// Register the widget
function register_zwf_widget() {
	register_widget( 'ZWF_Widget' );
}

add_action( 'widgets_init', 'register_zwf_widget' );

?>