<?php 
// check user capabilities
if (!current_user_can('manage_options')) {
    return;
}
?>

<div class="wrap">
    <?php settings_errors();?>
    <form method="POST" action="options.php">
        <?php settings_fields('settings-page');?>
        <?php do_settings_sections('settings-page')?>
        <?php submit_button();?>
    </form>
</div>
