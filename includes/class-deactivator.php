<?php

/**
 * Fired during plugin deactivation
 *
 * @link       zymplify-web-forms
 * @since      1.0.0
 *
 * @package    Zymplify_web_forms
 * @subpackage Zymplify_Web_Forms/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Zymplify_web_forms
 * @subpackage Zymplify_web_forms/includes
 * @author     Asad <asad@webforms.com>
 */
class Zymplify_Web_Forms_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		global $wpdb;

		$table_name1 = $wpdb->prefix . 'zymplify_campaigns';
		$table_name2 = $wpdb->prefix . 'zymplify_campaigns_form_fields';
		$table_name3 = $wpdb->prefix . 'zymplify_campaigns_form_field_values';

		$sql = "DROP TABLE IF EXISTS $table_name1, $table_name2, $table_name3";
		$wpdb->query($sql);

		delete_option("jal_db_version");
		delete_option('is_zwf_user');
		delete_option('zwf_user_token');
	}

	public static function remove_data() {
		global $wpdb;

		$table_name1 = $wpdb->prefix . 'zymplify_campaigns';
		$table_name2 = $wpdb->prefix . 'zymplify_campaigns_form_fields';
		$table_name3 = $wpdb->prefix . 'zymplify_campaigns_form_field_values';

		$sql = "DROP TABLE IF EXISTS $table_name1, $table_name2, $table_name3";
		$wpdb->query($sql);

		delete_option("jal_db_version");
		delete_option('is_zwf_user');
	}

}
