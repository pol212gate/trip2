/**
 * Select2_351 Turkish translation.
 * 
 * Author: Salim KAYABAŞI <salim.kayabasi@gmail.com>
 */
(function ($) {
    "use strict";

    $.fn.select2_351.locales['tr'] = {
        formatNoMatches: function () { return "Sonuç bulunamadı"; },
        formatInputTooShort: function (input, min) { var n = min - input.length; return "En az " + n + " karakter daha girmelisiniz"; },
        formatInputTooLong: function (input, max) { var n = input.length - max; return n + " karakter azaltmalısınız"; },
        formatSelectionTooBig: function (limit) { return "Sadece " + limit + " seçim yapabilirsiniz"; },
        formatLoadMore: function (pageNumber) { return "Daha fazla…"; },
        formatSearching: function () { return "Aranıyor…"; }
    };

    $.extend($.fn.select2_351.defaults, $.fn.select2_351.locales['tr']);
})(jQuery);
