var $ = jQuery;
(function( $ ) {
	'use strict';

	$(document).on('change', '[data-changedata="1"]', function() {
        var data = $(this).data();

        if (data.typeaction == 'syncterms') {
            syncTerm($, data, event.target);
        }
    }).on('click', function (event) {
		// OFF RESULT LIST
    	var listResult = $('.listResult');
        if (!listResult.is(event.target) && listResult.has(event.target).length === 0) {
            listResult.remove();
        }

    }).on('blur', '[data-searchajax]', function (event) {

    }).on('input', '[data-searchajax]', function (event) {
		var data = $(event.target).data();
		if (data.type === 'custom_fields') {
			autocompleteCf($, event.target);
		}
	}).ready(function(){
		$('[data-changedata="1"]').each(function () {
            var data = $(this).data();
          	syncTerm($, data, this);
        });
	});

	// SWATCH COLOR
	$(document).ready(function(event){
		$('.taxonomySelect').trigger('change');

	}).on('change', '.taxonomySelect', function(event){
		var targetCurent = event.target;

        var query = {
            action: 'get_terms',
            taxonomy: $(targetCurent).val()
        };
        var url = ajaxurl + '?' + $.param(query);

        $.get(url, function (res) {
           var $target = $(targetCurent).closest('.blockSwatchColor').find('.termsSelect');
            var oldJson = $(targetCurent).closest('.blockSwatchColor').find('.jsonData').text();
            oldJson = JSON.parse(oldJson);

            if (res.success) {
				var options = '';

				$.each(res.data, function (key, val) {
					var _status = '';
					if ( typeof oldJson['color'] != 'undefined') {
						if ( typeof oldJson['color'][val.term_id] != 'undefined') {
							_status = 'selected';
						}
					} 
					
					options += '<option '+ _status +' value="'+ val.term_id +'">'+ val.name +'</option>';
                });

				$target.html(options);
            } else {
                $target.html('');
			}
			$target.trigger('change');
			$target.trigger('chosen:updated');
        });
	}).on('change', '.blockSwatchColor .termsSelect', function (event) {
    	var value = $(event.target).val();
		var html = '';	
		var nameInput = $(event.target).attr('name');
		nameInput = nameInput.replace('terms', 'color');
		
		var oldJson = $(event.target).closest('.blockSwatchColor').find('.jsonData').text();
		var dataColor = JSON.parse(oldJson);

    	$.each(value, function(i, v) {
    		var termName = $(event.target).find('[value="'+ v +'"]').text();
    		var _nameInput =  nameInput.replace('[]', '['+ v +']');
    		
    		var oldValue = '';
    		if ( typeof dataColor['color'] != 'undefined') {
    			if ( typeof dataColor['color'][v] != 'undefined') {
    				oldValue = dataColor['color'][v] ;
    			}
    		} 

    		html += '<tr>';
	    		html += '<td>';
	    			html += '<label>'+ termName +'</label>';
	    		html += '</td>';
	    		html += '<td>';
	    			html += '&#160<b>#</b><input class="inputColor" data-id="'+ v +'" placeholder="Color Hex" name="'+ _nameInput +'" value="'+ oldValue +'" />';
	    		html += '</td>';
    		html += '</tr>';
    	});
    	$(event.target).closest('.blockSwatchColor').find('.tableInputColor').html(html);

	});
})( jQuery );

function autocompleteCf($, target) {
	var url = ajaxurl + '?' + $.param({
            post_type : $('[data-depend-id="filter_post_type_source"]').val(),
			q : $(target).val(),
			action: 'get_keys'
		});
	$.get(url, function (res) {
        var $parent = $(target).closest('.cs-fieldset');
        if ($parent.find('.listResult').length <= 0) {
            var html = '<div class="listResult"></div>';
            $parent.append(html);
        } else {
            $parent.find('.listResult').html('');
        }

		if (res.success) {
			html = '';
            $.each(res.data, function(k, v) {
                html += '<button class="itemResultLiveSearch" onclick="liveSearchPin(event)" type="button"> '+ v +' </button>'
            });

            $parent.find('.listResult').html(html);
		} else {
            $parent.find('.listResult').html('');
		}
    });

}

function liveSearchPin(event) {
	var vl = $(event.target).text();
    $(event.target).closest('.cs-fieldset').find('[data-searchajax]').val(vl);
    $(event.target).closest('.listResult').remove();
}

function syncTerm($, data, targetCurent) {

	if (
		typeof data.target === 'string'
		&& typeof data.wrapper === 'string'
		&& $(targetCurent).closest(data.wrapper).length > 0
		&& $(targetCurent).closest(data.wrapper).find('[data-id='+ data.target +']').length > 0
	)
	{
        var query = {
            action: 'get_terms',
            taxonomy: $(targetCurent).val()
        };
        url = ajaxurl + '?' + $.param(query);

        $.get(url, function (res) {
            $target = $(targetCurent).closest(data.wrapper).find('[data-id='+ data.target +']');
            if (res.success) {
				var options = '';
				var _data_element = $target.data();
                _data_element = _data_element.valueselected.toString();

                var _data = _data_element.split(',');
                _data_element = {};

				$.each(_data, function (i, v) {
					_data_element[v] = v;
                });

				$.each(res.data, function (key, val) {
					var _status = ( typeof  _data_element[val.term_id] !== 'undefined' ? 'selected' : '' );
					options += '<option '+ _status +' value="'+ val.term_id +'">'+ val.name +'</option>';
                });

				$target.html(options);
            } else {
                $target.html('');
			}
			$target.trigger('change');
			$target.trigger('chosen:updated');
        });
	}

}


function ActionSelectAll(event) {
    jQuery(event.target).closest('.cs-fieldset').find('select').find('option').prop('selected', true);
    jQuery(event.target).closest('.cs-fieldset').find('select').trigger('chosen:updated');
}
function ActionUnSelectAll(event) {
    jQuery(event.target).closest('.cs-fieldset').find('select').find('option').prop('selected', false);
    jQuery(event.target).closest('.cs-fieldset').find('select').trigger('chosen:updated');
}