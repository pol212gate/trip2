(function($j) {
	var selector = ac_param.selector;
	var urlDestination = ac_param.urlDestination;
	var autoFocus = ac_param.autoFocus;
	var limitDisplay = ac_param.limitDisplay;
	var multiple = ac_param.multiple;
	$j(selector).autocomplete(urlDestination, { selectFirst:autoFocus, max:limitDisplay, multiple:multiple, multipleSeparator:' ', delay:100, noRecord:''});
})(jQuery);