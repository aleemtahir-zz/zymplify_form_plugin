<?php 
  
  $setting_campaign_id = esc_attr($shortcodeValues['campaign']);

  if($setting_campaign_id)
  {
    global $wpdb;
    $sql = "SELECT f.reference_id as field_id, f.name as field_name, f.label as field_label, f.type as field_type, GROUP_CONCAT(v.value ORDER BY v.order_id ASC) AS field_value
                FROM ".$wpdb->prefix."zymplify_campaigns c 
                INNER JOIN ".$wpdb->prefix."zymplify_campaigns_form_fields f ON c.reference_id = f.campaign_id 
                LEFT JOIN ".$wpdb->prefix."zymplify_campaigns_form_field_values v ON f.reference_id = v.form_id 
                WHERE c.reference_id = '$setting_campaign_id'
                GROUP BY f.reference_id
                ORDER BY f.reference_id ASC ";

    $results = $wpdb->get_results($sql);

    $widgetObject = new ZWF_Widget;
    return $widgetObject->show_form($results, $setting_campaign_id);
  }


?>
