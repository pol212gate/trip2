
function iniAceEditor(id) {
    var editor = ace.edit(id);
    editor.setTheme("ace/theme/twilight");
    editor.session.setMode("ace/mode/php");
    editor.setOptions({
        maxLines: Infinity,
        minLines: 20
    });

    return editor;
}

var AsfbStore={};

(function( $ ) {

    'use strict';

    $(document)
        .ready(function(){
            if ( $('#codeItemResult').length > 0 ) {
                $('#codeItemResult').after('<div id="ace_codeItemResult"></div>');
                AsfbStore.codeItemResult = iniAceEditor('ace_codeItemResult');
                AsfbStore.codeItemResult.setValue($('#codeItemResult').val());
                AsfbStore.codeItemResult.getSession().on('change', function(e) {
                    $('#codeItemResult').val(AsfbStore.codeItemResult.getValue());
                });
            }
        })

        .on('change', '.templateItemResult', function (event) {
            if ( $(event.target).is(':checked') ) {
                var id = $(event.target).val();
                var query = {
                    'action' : 'get_template',
                    'id' : id
                };
                var url = ajaxurl + '?' + $.param(query);
                $.get(url, function (data) {
                    AsfbStore.codeItemResult.setValue(data.data);
                    $('body, html').animate({
                        scrollTop: $('#ace_codeItemResult').offset().top - 100
                    }, 500);
                });
            } else {
                AsfbStore.codeItemResult.setValue('');
            }
        });

})(jQuery);