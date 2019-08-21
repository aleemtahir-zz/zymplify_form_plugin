<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       zymplify-web-forms
 * @since      1.0.0
 *
 * @package    Zymplify_Web_Forms
 * @subpackage zymplify-Web-Forms/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<style>
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td, th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;
  width:30px;
}

tr:nth-child(even) {
  background-color: #dddddd;
}
.loader_slh {
position: fixed;
left: 0px;
top: 0px;
width: 100%;
height: 100%;
z-index: 9999;
background: url('../images/ajax-loader.gif') 50% 50% no-repeat rgb(249,249,249);
/*text-indent:-9999px;*/

}
</style>

<div class="wrap">
    <h1>Zymplify Web Form</h1>

    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
            <div id="post-body-content">
                
                <div id="authenticate-form">

                  <div class="row form-group">
                    <label for="username" class="col-md-2">Username *</label>
                    <input type="text" name="auth-username" id="auth-username" value="" class="col-md-6" />
                  </div>
                  
                  <div class="row">
                    <label for="password" class="col-md-2">Password *</label>
                    <input type="password" id="auth-password" name="auth-password" value="" class="col-md-6" />
                  </div>

                </div>

                <form method="post">
                    <br>
                    <div class="submit-wrap">
                        <?php echo '<button id="zwf_campaign_submit" type="button" class="btn btn-primary" style="float: left;">Submit</button>'; ?>
                    </div>
                    <div>
                      <img id="loading-image" src="<?php echo plugin_dir_url( __FILE__ ) ?>../images/ajax-loader.gif" style="display:none;width: 30px; float: left;"/>
                    </div>
                    <br>
                    <br>
                    <div>
                      <span id="zwf_admin_error" style="display: none;">*Data has not been synced. Please try again.</span>
                    </div>
                </form>

                <table id="campaing_table" width='100%' border='0' style="visibility: hidden;">
                  <tbody>
                    <tr>
                        <th>Campaign Id</th>
                        <th>Title</th>
                        <th>Type</th>
                    </tr>
                  </tbody>
                </table>
                <br/><br/>
                <br/><br/>
            </div>
        </div>
        <br>
    </div>
</div>