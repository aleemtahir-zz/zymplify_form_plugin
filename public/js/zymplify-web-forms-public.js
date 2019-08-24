jQuery(document).ready(function($) {

	/*jQuery(".zwf_submit").on('click', function(){
		event.preventDefault();
        
        var currentElement = this;
		var campaign_id = jQuery(currentElement).data('campaign-id');
		var form_id 	= "#form_"+ campaign_id;
		var message 	= "#zwf_submit_msg_"+ campaign_id;
		
		jQuery(form_id+" input[name*='fields']").each(function(){
			console.log(this);
		});

		jQuery(currentElement).html("Submitting...");

		var data = {
	        'action': 'zwf_form_submit',
	    };

		jQuery.post(frontend.ajaxurl, data, function(response) {
	        var dd = '';
	        var flag = true;

	        jQuery(currentElement).html("Submit");

	        jQuery(form_id+' [required]').each(function(index)
			{
				if (!(jQuery(this).val())) {
					jQuery(this).addClass('error');
					flag = false;
				}
			});
	        
			if(flag){
				jQuery(currentElement).attr("disabled", true);
        		jQuery(message).removeClass('text-danger').addClass('text-primary').html("Your form has been submitted successfully.");
			}
			else{
				jQuery(message).removeClass('text-primary').addClass('text-danger').html("Please fill the required fields.");
			}
	    });
	});*/

	// For shortcode

    /*var data = {
        'action': 'get_campaigns',
    };

    jQuery.post(frontend.ajaxurl, data, function(response) {
        var dd = '';
        jQuery.each(JSON.parse(response), function(i,data) {

        	dd += "<option value='"+data.id+"' >"+data.title+"</option>";
        });

        jQuery("#campaign_dd")
            .append(dd);
    });

    jQuery('#campaign_dd').on('change',function(){
        var campaign_id = jQuery(this).val();
        
        var data = {
	        'action': 'make_campaigns_form',
	        'campaign_id': campaign_id,
	    };

        if(campaign_id > 0){

            jQuery.post(frontend.ajaxurl, data, function(response) {
		        var dd = '';
		        jQuery.each(JSON.parse(response), function(i,data) {
		        	
		        	if(data.field_value == null)
		        		data.field_value = '';

		        	dd += '<div class="row">'+
						    '<div class="col-25">'+
						      '<label for="'+data.field_name+'">'+data.field_label+'</label>'+
						    '</div>'+
						    '<div class="col-75">'+
						      '<input type="text" id="'+data.field_id+'" name="'+data.field_name+'" value="'+data.field_value+'">'+
						    '</div>'+
						  '</div>';
		        });

		        jQuery("#campaign_form")
		            .append(dd);
		        jQuery("#campaign_container").show();
		    });
        }
    });*/


    // jQuery('.zwf_submit').on('click', function(e){
    // 	e.preventDefault();
    // 	var id = jQuery(this).data('campaign-id');
    // 	// console.log(jQuery(this).data('campaign-id'));
    // 	jQuery('#zwf_submit_msg_'+id).html("Your form has been submitted successfully.");
    // });
});