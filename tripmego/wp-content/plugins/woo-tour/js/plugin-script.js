;(function($){
	$(window).load(function() {
		var $container = $('.wt-grid-shortcode:not(.wt-grid-column-1) .grid-container');
		$container.imagesLoaded( function() {
			$container.masonry({
				itemSelector: '.item-post-n',
			});
		});
	});
		function ex_carousel(){
			$(".is-carousel").each(function(){
				var carousel_id = $(this).attr('id');
				var auto_play = $(this).data('autoplay');
				var items = $(this).data('items');
				var navigation = $(this).data('navigation');
				var pagination = $(this).data('pagination');
				var paginationNumbers = $(this).data('paginationNumbers');
				if($(this).hasClass('single-carousel')){ //single style
					$(this).owlCarousel({
						singleItem:true,
						autoHeight: true,
						autoPlay: auto_play?true:false,
						navigation: navigation?true:false,
						autoHeight : true,
						navigationText:["<i class='fa fa-angle-left'></i>","<i class='fa fa-angle-right'></i>"],
						addClassActive : true,
						pagination:pagination?true:false,
						paginationNumbers:paginationNumbers?true:false,
						stopOnHover: true,
						slideSpeed : 600,
						transitionStyle : "fade"
					});
				}else{
					$(this).owlCarousel({
						autoPlay: auto_play?true:false,
						items: items?items:4,
						itemsDesktop: items?false:4,
						itemsDesktopSmall: items?(items>3?3:false):3,
						singleItem: items==1?true:false,
						//autoHeight : true,
						navigation: navigation?true:false,
						paginationNumbers:paginationNumbers?true:false,
						navigationText:["<i class='fa fa-angle-left'></i>","<i class='fa fa-angle-right'></i>"],
						pagination:pagination?true:false,
						slideSpeed: 500,
						addClassActive : true
					});
				}
			});
		}
		function wt_check_ticket_status(wt_sldate){
			if(wt_sldate==''){ return;}
			var ajaxurl = jQuery('input[name=wt_ajax_url]').val();
			var wt_tourid = jQuery('input[name=wt_tourid]').val();
			jQuery('.single-product form.cart .single_add_to_cart_button').addClass('loading');
			jQuery.ajax({
				url: ajaxurl,
				type: "POST",
				dataType: 'json',
				data: {
					action:'wt_check_ticket_status',
					wt_sldate : wt_sldate,
					wt_tourid : wt_tourid,
				},
				success: function(data){
					if(data!=0){
						// console.log(data);
						if(data.status!=''){
							if(data.status==0){
								if(jQuery(".product:not(.product-type-external) .single_add_to_cart_button").length>0){
									jQuery('.product:not(.product-type-external) .single_add_to_cart_button').addClass('wtdisabled');
								}else{
									jQuery('.single-product .single_add_to_cart_button').addClass('wtdisabled');
								}
							}else{
								if(jQuery(".product:not(.product-type-external) .single_add_to_cart_button").length>0){
									jQuery('.product:not(.product-type-external) .single_add_to_cart_button').removeClass('wtdisabled');
								}else{
									jQuery('.single-product .single_add_to_cart_button').removeClass('wtdisabled');
								}
							}
						}
						jQuery('.wt-departure .wt-tickets-status').html(data.massage);
						
						if(jQuery(".product:not(.product-type-external) .single_add_to_cart_button").length>0){
							jQuery('.product:not(.product-type-external) .single_add_to_cart_button').removeClass('loading');
						}else{
							jQuery('.single-product .single_add_to_cart_button').removeClass('loading');
						}
					}
				}
			});
		}
		$(document).ready(function() {
			jQuery('.woocommerce-variation, .wtsl-text').on('click', '.wt-quantity #wtminus_ticket', function(e) {
				var value = parseInt(jQuery(this).next().val()) - 1;
				if(jQuery(this).next().attr('name')=='wt_number_adult'){
					if(value > 0){
						jQuery(this).next().val(value);
					}
				}else{
					if(value >= 0){
						jQuery(this).next().val(value);
					}
				}
			});
			jQuery('.woocommerce-variation').on('click', '.wt-quantity #wtadd_ticket', function(e) {
				var value = parseInt(jQuery(this).prev().val()) + 1;
				jQuery(this).prev().val(value);
			});
			
			
			jQuery('.wt-quantity #wtminus_ticket').on('click', function(e) {
				var value = parseInt(jQuery(this).next().val()) - 1;
				if(jQuery(this).next().attr('name')=='wt_number_adult'){
					if(value > 0){
						jQuery(this).next().val(value);
					}
				}else{
					if(value >= 0){
						jQuery(this).next().val(value);
					}
				}
				if(!jQuery(this).next().hasClass('wt-qf')){
					var value = parseInt(jQuery(this).next().children('[type="text"]').val()) - 1;
					if(jQuery(this).next().children('[type="text"]').attr('name')=='wt_number_adult'){
						if(value > 0){
							jQuery(this).next().children('[type="text"]').val(value);
						}
					}else{
						if(value >= 0){
							jQuery(this).next().children('[type="text"]').val(value);
						}
					}
				}
			});
			jQuery('.wt-quantity #wtadd_ticket').on('click', function(e) {
				var value = parseInt(jQuery(this).prev().val()) + 1;
				jQuery(this).prev().val(value);
				if(!jQuery(this).prev().hasClass('wt-qf')){
					var value = parseInt(jQuery(this).prev().children('[type="text"]').val()) + 1;
					jQuery(this).prev().children('[type="text"]').val(value);
				}
				
			});
			
			/* Ajax add session*/
			jQuery('.single-product form.cart .single_add_to_cart_button').on('click',function(event) {
				//event.preventDefault();
				//event.stopPropagation();
				var $this = $(this);
				if($this.hasClass('wt-redr')){ return;}
				$this.addClass('loading');
				$this.addClass('wt-redr');
				//$this.closest('.cart').addClass('loading');
				/*code to add validation, if any*/
				/* If all values are proper, then send AJAX request*/
				var wt_date = jQuery('input[name=wt_date]').val();
				var wt_sldate = jQuery('input[name=wt_sldate]').val();
				var wt_number_adult = jQuery('[name=wt_number_adult]').val();
				wt_number_adult = wt_number_adult > 0 ? wt_number_adult : '';
				var wt_number_child = jQuery('[name=wt_number_child]').val();
				wt_number_child = wt_number_child > 0 ? wt_number_child : '';
				var wt_number_infant = jQuery('[name=wt_number_infant]').val();
				wt_number_infant = wt_number_infant > 0 ? wt_number_infant : ''
				var ajaxurl = jQuery('input[name=wt_ajax_url]').val();
				//alert(picker.get('select').pick/1000);
				jQuery.ajax({
					url: ajaxurl,
					type: "POST",
					data: {
						action:'wdm_add_user_custom_data_options',
						wt_date : wt_date,
						wt_number_adult : wt_number_adult,
						wt_number_child : wt_number_child,
						wt_number_infant : wt_number_infant,
						wt_sldate : wt_sldate,
					},
					success: function(data){
						$this.trigger( "click" );
						//$this.removeClass('loading');
					}
				});
				return false;
			})
			/* Carousel */
			ex_carousel();
			
			/* Front end time picker */
			if(jQuery(".tour-info-select input[name=wt_date]:not(.wt-custom-date)").length>0){
				jQuery(".tour-info-select input[name=wt_date] + i").click(function() {
					event.preventDefault();
    				jQuery('.tour-info-select input[name=wt_date]').focus();
				});
				var $day_disable = jQuery('.tour-info-select input[name=wt_weekday_disable]').val();
				var $date_disable = jQuery('.tour-info-select input[name=wt_date_disable]').val();
				var $wt_cust_date = jQuery('.tour-info-select input[name=wt_cust_date]').val();
				var $wt_expired = jQuery('.tour-info-select input[name=wt_expired]').val();
				var $wt_book_before = jQuery('.tour-info-select input[name=wt_book_before]').val();
				var $wt_firstday = jQuery('.tour-info-select input[name=wt_firstday]').val();
				var datepicker_language = jQuery('.tour-info-select input[name=wt_langu]').val();
				$day_disable = $.parseJSON($day_disable);
				$date_disable = $.parseJSON($date_disable);
				$cust_date = $.parseJSON($wt_cust_date);
				if(datepicker_language ==''){
					$wt_daytrsl = jQuery('.tour-info-select input[name=wt_daytrsl]').val();
					$wt_montrsl = jQuery('.tour-info-select input[name=wt_montrsl]').val();
					$wt_montrsl = $.parseJSON($wt_montrsl);
					$wt_daytrsl = $.parseJSON($wt_daytrsl);
					var $trsl_m =[];
					$.each($wt_montrsl, function(i, item) {
						$trsl_m.push(item);
					});
					var $trsl_d =[];
					$.each($wt_daytrsl, function(i, item) {
						$trsl_d.push(item);
					});
					var  $input = jQuery(".tour-info-select input[name=wt_date]").pickadate({
						monthsFull:$trsl_m,
						weekdaysShort:$trsl_d,
						firstDay: $wt_firstday!='' ? true : false,
						container: '.wt-departure',
						selectYears: true,
						selectMonths: true,
						min: true,
						//max: + $wt_expired,
						onSet: function(thingSet) {
							if (typeof thingSet.select !== 'undefined') {
								var wt_sldate = picker.get('select','yyyy_mm_dd');
								jQuery(".tour-info-select input[name=wt_sldate]").val(wt_sldate);
								wt_check_ticket_status(wt_sldate);
							}
						},
						onRender: function() {
							var dateobj = new Date();
    						var offset = -dateobj.getTimezoneOffset();
							if($wt_cust_date!=''){
								$('.wt-departure .picker__table tbody .picker__day').each(function(){
									var $this_cl = $(this);
									$data_pick = $this_cl.data('pick') + (offset*60*1000);
									$.each($cust_date, function(i, item) {
										if((item * 1000) == $data_pick){
											$($this_cl).removeClass('picker__day--disabled');
										}
									});
								});
							}
						},
					});
				}else{
					var  $input = jQuery(".tour-info-select input[name=wt_date]").pickadate({
						container: '.wt-departure',
						firstDay: $wt_firstday!='' ? true : false,
						selectYears: true,
						selectMonths: true,
						min: true,
						//max: + $wt_expired,
						onSet: function(thingSet) {
							if (typeof thingSet.select !== 'undefined') {
								var wt_sldate = picker.get('select','yyyy_mm_dd');
								jQuery(".tour-info-select input[name=wt_sldate]").val(wt_sldate);
								wt_check_ticket_status(wt_sldate);
							}
						},
						onRender: function() {
							var dateobj = new Date();
    						var offset = -dateobj.getTimezoneOffset();
							if($wt_cust_date!=''){
								$('.wt-departure .picker__table tbody .picker__day').each(function(){
									var $this_cl = $(this);
									$data_pick = $this_cl.data('pick') + (offset*60*1000);
									$.each($cust_date, function(i, item) {
										if((item * 1000) == $data_pick){
											$($this_cl).removeClass('picker__day--disabled');
										}
									});
								});
							}
						},
					});
				}
				var $disable =[];
				/* disable date*/
				$.each($date_disable, function(i, item) {
					$disable.push(new Date(item * 1000));
				});
				/* Disable day*/
				$.each($day_disable, function(i, item) {
					$disable.push(item);
				});
				
				var picker = $input.pickadate('picker');
				if($wt_cust_date!='' && $wt_cust_date!='[]' && (typeof $wt_cust_date != 'undefined')){
					picker.set('disable', true);
				}else{
					picker.set('disable', $disable);
				}
				if($wt_expired!=''){picker.set('max', new Date($wt_expired));}
				if($wt_book_before!=''){picker.set('min', new Date($wt_book_before));}
				/*
				jQuery(".tour-info-select input[name=wt_date]").datepicker({
					"language": datepicker_language,
					"todayHighlight" : true,
					datesDisabled: ['01/06/2017', '01/21/2017'],
					"daysOfWeekDisabled": $day_disable,
					"allowInputToggle": true,
					"startDate": new Date(),
					"endDate": $wt_expired,
					"autoclose": true,
				});
				*/
				jQuery(".wt-departure >span").on('click', function (e) {
					e.stopPropagation();
					// prevent the default click action
					e.preventDefault();
					// open the date picker
					picker.open();
				});
				
			}else if(jQuery(".tour-info-select input[name=wt_date].wt-custom-date").length>0){
				jQuery(".tour-info-select .wt-list-date + i").click(function() {
					if(jQuery(".tour-info-select .wt-list-date").hasClass('active')){
						jQuery(".tour-info-select .wt-list-date").removeClass('active');
					}else{
						jQuery(".tour-info-select .wt-list-date").addClass('active');
					}
				});
				jQuery(".tour-info-select .wt-list-date li").click(function() {
					jQuery(".tour-info-select input[name=wt_date].wt-custom-date").val($(this).data('value'));
					jQuery(".tour-info-select input[name=wt_sldate]").val($(this).data('date'));
					wt_check_ticket_status($(this).data('date'));
					jQuery(".tour-info-select .wt-list-date").removeClass('active');
				});
				jQuery(document).on('click', function (e) {
					if (jQuery(e.target).closest(".wt-bticon").length === 0 && jQuery(e.target).closest(".wt-custom-date").length === 0) {
						jQuery(".wt-list-date").removeClass('active');
					}
				});
			}
			jQuery(".wt-departure .wt-custom-date").on('click', function (e) {
					jQuery(".wt-list-date").addClass('active');
			});
			//search
			jQuery(".wt-search-dropdown:not(.wt-sfilter)").on('click', 'li a', function(){
				jQuery(".wt-search-dropdown:not(.wt-sfilter) .wt-search-dropdown-button .button-label").html(jQuery(this).text());
				jQuery(".wt-product-cat").val(jQuery(this).data('value'));
				jQuery(".wt-search-dropdown").removeClass('open');
				return false;
			});
			jQuery(".button-scroll").click(function() {
				var $scrtop = jQuery(".summary").offset().top;
				jQuery('html, body').animate({
					scrollTop: ($scrtop-100)
				}, 500);
			});
			
			$('.input-group-btn:not(.wt-sfilter)').on('click', function(e) {
				$menu = $(this);
				//e.preventDefault();
			
				if (!$menu.hasClass('open')) {
					$menu.addClass('open');
			
					$(document).one('click', function closeTooltip(e) {
						if ($menu.has(e.target).length === 0 && $('.input-group-btn').has(e.target).length === 0) {
							$menu.removeClass('open');
						} else if ($menu.hasClass('open')) {
							$(document).one('click', closeTooltip);
						}
					});
				} else {
					//$menu.removeClass('open');
				}
			}); 
			
			$('.input-group-btn.wt-sfilter').on('click', function(e) {
				$this = $(this);
				if(!$this.hasClass('wt-sfilter-close')){
					$this.addClass('wt-sfilter-close');
					$('.wt-filter-expand').addClass('active');
				}else{
					$this = $(this);
					$this.removeClass('wt-sfilter-close');
					$('.wt-filter-expand').removeClass('active');
				}
				//e.preventDefault();
			});
			// ajax load morer
			$('.loadmore-grid').on('click',function() {
				var $this_click = $(this);
				if($this_click.hasClass('table-loadmore')){ return;}
				$this_click.addClass('disable-click');
				var id_crsc  		= $this_click.data('id');
				var n_page = $('#'+id_crsc+' input[name=num_page_uu]').val();
				//alert(n_page);
				$('#'+id_crsc+' .loadmore-grid').addClass("loading");
				var param_query  		= $('#'+id_crsc+' input[name=param_query]').val();
				var page  		= $('#'+id_crsc+' input[name=current_page]').val();
				var num_page  		= $('#'+id_crsc+' input[name=num_page]').val();
				var ajax_url  		= $('#'+id_crsc+' input[name=ajax_url]').val();
				//alert(num_page);
				var param_shortcode  		= $('#'+id_crsc+' input[name=param_shortcode]').val();
				//alert(param_shortcode);
					var param = {
						action: 'ex_loadmore_grid',
						param_query: param_query,
						page: page*1+1,
						param_shortcode: param_shortcode,
					};
		
					$.ajax({
						type: "post",
						url: ajax_url,
						dataType: 'html',
						data: (param),
						success: function(data){
							if(data != '0')
							{
								n_page = n_page*1+1;
								$('#'+id_crsc+' input[name=num_page_uu]').val(n_page)
								if(data == ''){ 
									$('#'+id_crsc+' .loadmore-grid').remove();
								}
								else{
									$('#'+id_crsc+' input[name=current_page]').val(page*1+1);
									var $g_container = $('#'+id_crsc+' .grid-container');
									if($('#'+id_crsc).hasClass('wt-grid-column-1')){
										$g_container.append(data);
									}else{
										$g_container.append(data).imagesLoaded( function() {
											$g_container.masonry('reloadItems');
											$g_container.masonry({
												isInitLayout : false,
												itemSelector: '.item-post-n'
											});
										});
									}
									$('#'+id_crsc+' .loadmore-grid').removeClass("loading");
									$this_click.removeClass('disable-click');
								}
								if(n_page == num_page){
									$('#'+id_crsc+' .loadmore-grid').remove();
								}
								
							}else{$('.row.loadmore').html('error');}
						}
					});
				return false;	
			});
			//table load
			$('.loadmore-grid.table-loadmore').on('click',function() {
				var $this_click = $(this);
				$this_click.addClass('disable-click');
				var id_crsc  		= $this_click.data('id');
				var n_page = $('#'+id_crsc+' input[name=num_page_uu]').val();
				//alert(n_page);
				$('#'+id_crsc+' .loadmore-grid').addClass("loading");
				var param_query  		= $('#'+id_crsc+' input[name=param_query]').val();
				var page  		= $('#'+id_crsc+' input[name=current_page]').val();
				var num_page  		= $('#'+id_crsc+' input[name=num_page]').val();
				var ajax_url  		= $('#'+id_crsc+' input[name=ajax_url]').val();
				//alert(num_page);
				var param_shortcode  		= $('#'+id_crsc+' input[name=param_shortcode]').val();
				//alert(param_shortcode);
					var param = {
						action: 'ex_loadmore_table',
						param_query: param_query,
						page: page*1+1,
						param_shortcode: param_shortcode,
					};
		
					$.ajax({
						type: "post",
						url: ajax_url,
						dataType: 'html',
						data: (param),
						success: function(data){
							if(data != '0')
							{
								n_page = n_page*1+1;
								$('#'+id_crsc+' input[name=num_page_uu]').val(n_page)
								if(data == ''){ 
									$('#'+id_crsc+' .loadmore-grid').remove();
								}
								else{
									$('#'+id_crsc+' input[name=current_page]').val(page*1+1);
									var $g_container = $('#'+id_crsc+' tbody');
									$g_container.append(data);
									setTimeout(function(){ 
										$('#'+id_crsc+' tbody .tb-load-item').addClass("active");
									}, 200);
									$('#'+id_crsc+' .loadmore-grid').removeClass("loading");
									$this_click.removeClass('disable-click');
								}
								if(n_page == num_page){
									$('#'+id_crsc+' .loadmore-grid').remove();
								}
								
							}else{$('.row.loadmore').html('error');}
						}
					});
				return false;	
			});
			
			$('#wt-ajax-search button.wt-search-submit').on('click',function() {
				var $this_click = $(this);
				var id_crsc  		= $this_click.data('id');
				$this_click.addClass('disable-click');
				wt_ajax_search(id_crsc,$this_click);
				return false;	
			});
			if(jQuery(".woocommerce-cart:not(.wt-unremove-qtn) table.shop_table tbody tr:last-child td.actions").length>0){
				var col_nb = $(".woocommerce-cart:not(.wt-unremove-qtn) table.shop_table tbody tr:last-child td.actions").attr("colspan");
				if(col_nb==6){
					$(".woocommerce-cart:not(.wt-unremove-qtn) table.shop_table tbody tr:last-child td.actions").attr('colspan',5)
				}
			};
	});
	$(document).ajaxSuccess(function() {
		if(jQuery(".woocommerce-cart:not(.wt-unremove-qtn) table.shop_table tbody tr:last-child td.actions").length>0){
			var col_nb = $(".woocommerce-cart:not(.wt-unremove-qtn) table.shop_table tbody tr:last-child td.actions").attr("colspan");
			if(col_nb==6){
				$(".woocommerce-cart:not(.wt-unremove-qtn) table.shop_table tbody tr:last-child td.actions").attr('colspan',5)
			}
		};
	});
}(jQuery));