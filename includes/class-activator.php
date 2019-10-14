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
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Zymplify_Web_Forms
 * @subpackage Zymplify_Web_Forms/includes
 * @author     Asad <asad@webforms.com>
 */
class Zymplify_Web_Forms_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		global $wpdb,$jal_db_version;
		
		$jal_db_version 	= '1.0';
		$charset_collate 	= $wpdb->get_charset_collate();


		$table_name 	 = $wpdb->prefix . 'zymplify_campaigns';
		$current_user_id = get_current_user_id();

		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			reference_id int(9) NOT NULL,
			admin_id int(9) DEFAULT ".$current_user_id." NOT NULL,
			title tinytext NOT NULL,
			type int(9) NOT NULL,
			created_at datetime DEFAULT '".date("Y-m-d H:i:s")."' NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

		$table_name = $wpdb->prefix . 'zymplify_campaigns_form_fields';

		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			reference_id int(9) NOT NULL,
			campaign_id int(9) NOT NULL,
			name varchar(55) NOT NULL,
			label varchar(55) NOT NULL,
			required smallint(1) NOT NULL,
			type int(9) NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

		$table_name = $wpdb->prefix . 'zymplify_campaigns_form_field_values';

		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			reference_id int(9) NOT NULL,
			form_id int(9) NOT NULL,
			value varchar(55) NOT NULL,
			order_id varchar(55) NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

		/*
		* Creating API CLIENT AND TOKEN TABLES
		*/

		$table_name = $wpdb->prefix . 'zymplify_api_clients';

		$sql = "CREATE TABLE $table_name (
			  `id` INT(11) NOT NULL AUTO_INCREMENT,
			  `name` VARCHAR(255) DEFAULT NULL,
			  `client_secret` VARCHAR(80) DEFAULT NULL,
			  `date_added` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			  PRIMARY KEY (`id`) 
			)$charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

		$sql = "INSERT INTO $table_name (name,client_secret) VALUES('zymplify_core_client','". md5('ZymPliFy'.date('Y-m-d'))."','".date("Y-m-d H:i:s")."')";
		dbDelta( $sql );

		$client_id = $wpdb->insert_id;


		$table_name = $wpdb->prefix . 'zymplify_api_clients_tokens';

		$sql = "CREATE TABLE $table_name (
			  `act_id` INT(11) NOT NULL AUTO_INCREMENT,
			  `client_id` INT(11) DEFAULT NULL,
			  `access_token` varchar(255) DEFAULT NULL,
			  `expiry` varchar(255) DEFAULT NULL,
			  `date_added` VARCHAR(255) DEFAULT null,
			  `date_updated` VARCHAR(255) DEFAULT CURRENT_TIMESTAMP
			  PRIMARY KEY (`act_id`) 
			) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

		add_option( 'jal_db_version', $jal_db_version );


		/**
		* Populate Data in Tables
		*/

		$data = self::get_formatted_campaign_data($campaign_id);
		// print_r($data['fields']); die;
		self::bulk_insert( 
			$wpdb->prefix . 'zymplify_campaigns', 
			$data['campaigns']
		);

		self::bulk_insert( 
			$wpdb->prefix . 'zymplify_campaigns_form_fields', 
			$data['fields']
		);

		self::bulk_insert( 
			$wpdb->prefix . 'zymplify_campaigns_form_field_values', 
			$data['field_values']
		);
	}

	public function bulk_insert($table, $rows) {
		global $wpdb;
		
		// Extract column list from first row of data
		$columns = array_keys($rows[0]);
		asort($columns);
		$columnList = '`' . implode('`, `', $columns) . '`';
		// Start building SQL, initialise data and placeholder arrays
		$sql = "INSERT INTO `$table` ($columnList) VALUES\n";
		$placeholders = array();
		$data = array();
		// Build placeholders for each row, and add values to data array
		foreach ($rows as $row) {
			ksort($row);
			$rowPlaceholders = array();
			foreach ($row as $key => $value) {
				$data[] = $value;
				$rowPlaceholders[] = is_numeric($value) ? '%d' : '%s';
			}
			$placeholders[] = '(' . implode(', ', $rowPlaceholders) . ')';
		}
		// Stitch all rows together
		$sql .= implode(",\n", $placeholders);
		// Run the query.  Returns number of affected rows.
		return $wpdb->query($wpdb->prepare($sql, $data));
	}

	public function get_campaigns() {

	    $url = 'http://mpf48x2mxa.execute-api.eu-west-1.amazonaws.com/dev/api/campaigns/forms';  
	    
	    $response 			= wp_remote_get( $url,
	    						array(
	    							'headers' => array( 'Authorization' => 'Bearer '.get_option( "zwf_user_token") ) 
	    						));
	    $finalized_response = array();

	    if ( is_array( $response ) ) {
		  	$header = $response['headers']; // array of http header lines
		  	$body = $response['body']; // use the content
	    	$finalized_response = json_decode($body)->campaigns;
		}

		return $finalized_response;
	}

	public function get_formatted_campaign_data($c_id) {
	    
	    $campaigns = self::get_campaigns();
	    // print_r($campaigns); die;

	    $campaign_data 		= array();
	    $field_data 		= array();
	    $field_value_data 	= array();

	    $i = $j = $k = 0;

	    if (!empty($campaigns)) {
	    	
	    	foreach ($campaigns as $key1=>$campaign) {
		    	
		    	// if($campaign->id != $c_id)
		    	// 	continue;

		    	$campaign_data[$i]['reference_id'] 	= $campaign->id;  
		    	$campaign_data[$i]['title'] 		= $campaign->title;  
		    	$campaign_data[$i]['type'] 			= $campaign->campaignType;

		    	$i++;

		    	foreach ($campaign->FormFields as $key2 => $field) {

		    		$field_data[$j]['reference_id'] = $field->id;  
		    		$field_data[$j]['name'] 		= $field->fieldName;  
		    		$field_data[$j]['label'] 		= $field->fieldLabel;  
		    		$field_data[$j]['type'] 		= $field->fieldType;  
		    		$field_data[$j]['required'] 	= $field->required;  
		    		$field_data[$j]['campaign_id'] 	= $campaign->id;

		    		$j++;

		    		foreach ($field->FieldValues as $key3 => $field_value) {

		    			$field_value_data[$k]['reference_id'] 	= $field_value->id;  
		    			$field_value_data[$k]['value'] 			= $field_value->fieldValue;  
		    			$field_value_data[$k]['order_id'] 		= $field_value->orderId;  
		    			$field_value_data[$k]['form_id'] 		= $field->id; 
		    			$k++; 
		    		}
		    	}
		    }
	    }	    

	    return array(
	    	'campaigns' => $campaign_data,
	    	'fields' => $field_data,
	    	'field_values' => $field_value_data,
	    );
	}

}
