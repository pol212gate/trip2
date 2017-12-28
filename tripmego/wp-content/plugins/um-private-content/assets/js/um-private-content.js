jQuery( document ).ready(function() {

	jQuery( document).on( 'click', '#um_options_private_content_generate', function(e){
		
		e.preventDefault();

		var obj = jQuery(this);
		obj.prop('disabled', true);

		jQuery.ajax({
			url: ultimatemember_ajax_url,
			type: 'post',
			data: {
				action: 'um_generate_private_pages'
			},
			success: function( data ) {
				obj.siblings( '.um_setting_ajax_button_response' ).addClass('description complete').html( data.data.message );

				setTimeout( function() {
					obj.siblings( '.um_setting_ajax_button_response' ).removeClass('description complete').html( '' );
				}, 2000 );

				obj.prop('disabled', false);
			}
		});
		
		return false;
		
	});


	jQuery( '#tab_private_content_icon' ).after(
		'<div class="postbox" style="background: none !important;box-shadow: none !important;border:none !important;"><a href="#" class="button" data-modal="UM_fonticons" data-modal-size="normal" data-dynamic-content="um_admin_fonticon_selector" data-arg1="" data-arg2="">Choose Icon</a>' +
		'<span class="um-admin-icon-value"><i class="' + jQuery( '#tab_private_content_icon' ).val() + '"></i></span>'+
		'<input type="hidden" name="_icon" id="_icon" value="' + jQuery( '#tab_private_content_icon' ).val() + '" /></div>' );
/*
	jQuery(document).on('input change paste', '#_icon', function() {
		console.log('123132131');
		jQuery( '#tab_private_content_icon' ).val( jQuery(this) );
	});*/


	jQuery(document).on('click', '#UM_fonticons a.um-admin-modal-back:not(.um-admin-modal-cancel)', function(){
		var icon_selected = jQuery(this).attr('data-code');
		if (icon_selected != ''){
			jQuery( '#tab_private_content_icon' ).val( icon_selected );
		}
	});
});