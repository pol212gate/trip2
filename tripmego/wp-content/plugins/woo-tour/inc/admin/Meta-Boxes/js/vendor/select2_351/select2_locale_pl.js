/**
 * Select2_351 Polish translation.
 * 
 * @author  Jan Kondratowicz <jan@kondratowicz.pl>
 * @author  Uriy Efremochkin <efremochkin@uriy.me>
 * @author  Michał Połtyn <mike@poltyn.com>
 */
(function ($) {
    "use strict";

    $.fn.select2_351.locales['pl'] = {
        formatNoMatches: function () { return "Brak wyników"; },
        formatInputTooShort: function (input, min) { return "Wpisz co najmniej" + character(min - input.length, "znak", "i"); },
        formatInputTooLong: function (input, max) { return "Wpisana fraza jest za długa o" + character(input.length - max, "znak", "i"); },
        formatSelectionTooBig: function (limit) { return "Możesz zaznaczyć najwyżej" + character(limit, "element", "y"); },
        formatLoadMore: function (pageNumber) { return "Ładowanie wyników…"; },
        formatSearching: function () { return "Szukanie…"; }
    };

    $.extend($.fn.select2_351.defaults, $.fn.select2_351.locales['pl']);

    function character (n, word, pluralSuffix) {
        return " " + n + " " + word + (n == 1 ? "" : n%10 < 5 && n%10 > 1 && (n%100 < 5 || n%100 > 20) ? pluralSuffix : "ów");
    }
})(jQuery);
