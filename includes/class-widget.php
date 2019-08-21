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

			$post = $_POST['fields'];
			// print_r($post); die;
       		$ch = curl_init('https://mpf48x2mxa.execute-api.eu-west-1.amazonaws.com/dev/api/contacts');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			$response = curl_exec($ch);
			curl_close($ch);

       		?>
       		<script type="text/javascript">
       			var message 	= "#zwf_submit_msg_"+ "<?php echo $_POST['fields']['campaignId'] ?>";
       			jQuery(message).addClass('text-primary').html("Your form has been submitted successfully.");
       		</script>
       		<?php

        }

		// Check the widget options
		$title    = isset( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : '';
		$text     = isset( $instance['text'] ) ? $instance['text'] : '';
		$select   = isset( $instance['select'] ) ? $instance['select'] : '';

		global $wpdb;
		$sql = "SELECT f.reference_id as field_id, f.name as field_name, f.label as field_label, f.type as field_type, GROUP_CONCAT(v.value ORDER BY v.order_id ASC) AS field_value
                FROM ".$wpdb->prefix."zymplify_campaigns c 
                INNER JOIN ".$wpdb->prefix."zymplify_campaigns_form_fields f ON c.reference_id = f.campaign_id 
                LEFT JOIN ".$wpdb->prefix."zymplify_campaigns_form_field_values v ON f.reference_id = v.form_id 
                WHERE c.reference_id = $select
                GROUP BY f.reference_id
                ORDER BY f.reference_id ASC ";

        $results = $wpdb->get_results($sql);

		$this->show_form($results, $select);
	}

	public function show_form($results, $campaign_id){
		// WordPress core before_widget hook (always include )
		echo $before_widget;
		// Display the widget
		echo '<div class="widget-text wp_widget_plugin_box">';
			// Display widget title if defined
			if ( $title ) {
				echo $before_title . $title . $after_title;
			}
			// Display text field
			if ( $text ) {
				echo '<p>' . $text . '</p>';
			}
			// Display select field
			if ( $results ) {
				echo '<form method="post" action="" class="zwf_form" id="form_'.$campaign_id.'" >';
				foreach ($results as $key => $value) {
					if($value->field_type == 1){
						echo '<p>'.
						'<div class="form-group" >'.
						'<label for="'.$value->field_name.'">'.$value->field_label.'*</label>'.
						'<input class="form-control widefat" id="'.$value->field_id.'" name="fields['.$value->field_name.']" type="text" value="'.$value->field_value.'" required/>'.
						'</div>'.
					  	'</p>';
					}
					elseif($value->field_type == 2){
						echo '<p>'.
						'<div class="form-group">'.
						'<label for="'.$value->field_name.'">'.$value->field_label.'</label>'.
						'<textarea  class="form-control widefat" id="'.$value->field_id.'" name="fields['.$value->field_name.']" type="text">'.$value->field_value.'</textarea '.
						'</div>'.
					  	'</p>';
					}
					elseif($value->field_type == 3){
						
						echo '<p>'.
						'<div class="form-group">'.
						'<label for="'.$value->field_id.'">'.$value->field_label.'</label>';
						
						$values = explode(',', $value->field_value);
						foreach ($values as $v) {
							
							echo '<input class="form-control widefat" id="'.$value->field_id.'" name="fields['.$value->field_name.']" type="checkbox" value="'.$v.'"  />'.$v;
							echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
						}
					  	echo '</div>';
					  	echo '</p>';
					}
					elseif($value->field_type == 4){
						
						echo '<p>'.
						'<div class="form-group">'.
						'<label for="'.$value->field_id.'">'.$value->field_label.'</label>';
						
						$values = explode(',', $value->field_value);
						foreach ($values as $v) {
							
							echo '<input class="form-control widefat" id="'.$value->field_id.'" name="fields['.$value->field_name.']" type="radio" value="'.$v.'"  />'.$v;
							echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
						}
						echo '</div>';
					  	echo '</p>';
					}
					elseif($value->field_type == 5){
						
						echo '<p>'.
						'<div class="form-group">'.
						'<label for="'.$value->field_id.'">'.$value->field_label.'</label>'.
						'<select name="fields['.$value->field_name.']" id="'.$value->field_id.'" class="form-control widefat">';
						
						$values = explode(',', $value->field_value);

						foreach ($values as $v) {
							
							echo '<option value="'.$v.'" >'. $v . '</option>';
						}
					  	echo '</select>';
					  	echo '</div>';
					  	echo '</p>';
					}
					elseif($value->field_type == 6){
						
						echo '<p>'.
						'<div class="form-group">'.
						'<label for="'.$value->field_id.'">'.$value->field_label.'</label>'.
						'<input name="fields['.$value->field_name.']" id="'.$value->field_id.'" class="form-control widefat" type="checkbox" data-toggle="toggle">';
					  	echo '</div>';
					  	echo '</p>';
					}
					elseif($value->field_type == 7){
						
						echo '<p>'.
						'<div class="form-group">'.
						'<label for="'.$value->field_id.'">'.$value->field_label.'</label>'.
						'<input name="fields['.$value->field_name.']" id="'.$value->field_id.'" class="form-control widefat" type="file">';
					  	echo '</div>';
					  	echo '</p>';
					}
					elseif($value->field_type == 8){
						
						echo '<p>'.
						'<div class="form-group">'.
						'<label for="'.$value->field_id.'">'.$value->field_label.'</label>'.
						'<input name="fields['.$value->field_name.']" id="'.$value->field_id.'" class="form-control widefat" type="number">';
					  	echo '</div>';
					  	echo '</p>';
					}
					elseif($value->field_type == 9){
						
						echo '<p>'.
						'<div class="form-group">'.
						'<label for="'.$value->field_id.'">'.$value->field_label.'</label>'.
						'<input name="fields['.$value->field_name.']" id="'.$value->field_id.'" class="form-control widefat" type="datetime-local">';
					  	echo '</div>';
					  	echo '</p>';
					}
					elseif($value->field_type == 10){
						
						echo '<p>'.
						'<div class="form-group">'.
						'<label for="'.$value->field_id.'">'.$value->field_label.'</label>'.
						'<input name="fields['.$value->field_name.']" id="'.$value->field_id.'" class="form-control widefat" type="tel" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}">';
					  	echo '</div>';
					  	echo '</p>';
					}
					elseif($value->field_type == 11){
						
						echo '<p>'.
						'<div class="form-group">'.
						'<label for="'.$value->field_id.'">'.$value->field_label.'</label>'.
						'<input name="fields['.$value->field_name.']" id="'.$value->field_id.'" class="form-control widefat" type="date">';
					  	echo '</div>';
					  	echo '</p>';
					}
					elseif($value->field_type == 12){
						
						echo '<p>'.
						'<div class="form-group">'.
						'<label for="'.$value->field_id.'">'.$value->field_label.'</label>'.
						'<input name="fields['.$value->field_name.']" id="'.$value->field_id.'" class="form-control widefat" type="email">';
					  	echo '</div>';
					  	echo '</p>';
					}
					elseif($value->field_type == 13){
						
						echo '<p>'.
						'<div class="form-group">'.
						'<label for="'.$value->field_id.'">'.$value->field_label.'</label>'.
						'<input name="fields['.$value->field_name.']" id="'.$value->field_id.'" class="form-control widefat" type="url">';
					  	echo '</div>';
					  	echo '</p>';
					}
				}

				echo '<input type="hidden" name="fields[campaignId]" value="'.$campaign_id.'">';
				echo '<input type="hidden" name="fields[channel]" value="155471">';
				echo '<input type="hidden" name="fields[client]" value="12">';
				echo '<button class="zwf_submit" data-campaign-id="'.$campaign_id.'" type="submit" class="btn btn-primary" name="send">Submit</button>';
				echo '<br><br><p id="zwf_submit_msg_'.$campaign_id.'"></p><br><br>';
				echo '</form>';
			}
		echo '</div>';
		// WordPress core after_widget hook (always include )
		echo $after_widget;
	}
}

// Register the widget
function register_zwf_widget() {
	register_widget( 'ZWF_Widget' );
}

add_action( 'widgets_init', 'register_zwf_widget' );

?>