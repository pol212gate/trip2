;(function($){
	$(document).ready(function() {
	
		var wt_layout_purpose_obj  = jQuery('.postbox-container #wt_layout_purpose select');
		var wt_layout_purpose = jQuery('.postbox-container #wt_layout_purpose select').val();
		var wt_time_settings = jQuery('#time-settings.postbox');
		var wt_tour_settings = jQuery('#tour-info.postbox');
		
		var wt_add_settings = jQuery('#additional-information.postbox');
		var wt_lay_settings = jQuery('#layout-settings.postbox');
		var wt_ct_settings = jQuery('#custom-field.postbox');
		if(typeof(wt_layout_purpose)!='undefined'){
			if(wt_layout_purpose == 'tour'){
				wt_time_settings.show();
				wt_tour_settings.show();
				wt_add_settings.show();
				wt_lay_settings.show();
				wt_ct_settings.show();
			}else if(wt_layout_purpose == 'woo'){				
				wt_time_settings.hide();
				wt_tour_settings.hide();
				wt_add_settings.hide();
				wt_lay_settings.hide();
				wt_ct_settings.hide();
			}
			wt_layout_purpose_obj.change(function(event) {
				if(jQuery(this).val() == 'tour'){
					wt_time_settings.show(200);
					wt_tour_settings.show(200);
					wt_add_settings.show(200);
					wt_lay_settings.show(200);
					wt_ct_settings.show(200);
				}else if(jQuery(this).val() == 'woo'){
					wt_time_settings.hide(200);
					wt_tour_settings.hide(200);
					wt_add_settings.hide(200);
					wt_lay_settings.hide(200);
					wt_ct_settings.hide(200);
				}
			});
		}
		jQuery(document).on('change', '#wt_disc_start .field-item .exc_mb_datepicker:first-child', function() {
			fieldItem = jQuery(this).closest('.exc_mb-row' );
			jQuery('#wt_disc_end .field-item .exc_mb_datepicker:first-child', fieldItem).val(this.value);
		});
	
	});
}(jQuery));