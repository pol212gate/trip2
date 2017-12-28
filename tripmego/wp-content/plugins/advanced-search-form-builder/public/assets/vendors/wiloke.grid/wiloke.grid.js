(function(factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
        define(['jquery'], factory);
    } else if (typeof exports === 'object') {
        // CommonJS
        factory(require('jquery'));
    } else {
        // Browser globals
        factory(jQuery);
    }
}(function($) {
	'use strict';

	var WilokeIsotope = function(el, options) {
		this.$el = $(el);
		this.options = $.extend({}, WilokeIsotope.default, options);
		this.styles = this.options.style,
		this.container = this.$el.find(this.options.container);
		this.filterClass = this.$el.find(this.options.filterClass);
		this.gridSize = this.options.gridSize;
		this.gridItem = this.options.gridItem;
		this.beforeSetItemElem =  this.options.beforeSetItemElem;
		this.afterSetItemElem = this.options.afterSetItemElem;
		this.init();
	}

	WilokeIsotope.default = {
		style: 'masonry',
		filterClass: '.wil_filter',
		container: '.wil_masonry',
		gridSize: '.grid-sizer',
		gridItem: '.grid-item',
		gridItemInner: '.grid-item__inner',
		isotopeTransition: {
			hiddenStyle: '',
			visibleStyle: '',
			transitionDuration: ''
		},
		beforeSetItemElem: false,
		afterSetItemElem: false,
	}

	WilokeIsotope.prototype = {

		init: function() {
			this.beforeCreateIsotope();
			this.createIsotope();
			this.afterCreateIsotope();
			this.resizeBound();
			this.filter();
			this.onResize();
		},

		createIsotope: function() {
			
			var options = {
				itemSelector: this.gridItem,
				percentPosition: true,
				masonry: {
					columnWidth: this.gridSize
				},
			};

			if(typeof this.options.isotopeTransition['transitionDuration'] != 'undefined' && this.options.isotopeTransition['transitionDuration'] != '') {
				options['transitionDuration'] = this.options.isotopeTransition['transitionDuration'];
			}

			if(typeof this.options.isotopeTransition['hiddenStyle'] != 'undefined' && this.options.isotopeTransition['hiddenStyle'] != '') {
				options['hiddenStyle'] = this.isotopeTransition['hiddenStyle'];
			}

			if(typeof this.options.isotopeTransition['visibleStyle'] != 'undefined' && this.options.isotopeTransition['visibleStyle'] != '') {
				options['visibleStyle'] = this.options.isotopeTransition['visibleStyle'];
			}

			this.container.imagesLoaded($.proxy(function() {
				this.container.isotope(options);
				if(typeof display !='undefined' && display == 'lazy') {
					Waypoint.refreshAll();
				}
			}, this));

		},

		resizeBound: function() {

			if( typeof Outlayer != 'undefined' ) {
				
				$.extend(Outlayer.prototype, {

					resize: function() {

						if ( !this.isResizeBound ) {
							return;
						}

						this.layout();
					}
				});
			}
		},

		beforeCreateIsotope: function() {
			this.$el.addClass('wil-grid--'+this.styles + ' wil-grid--loading');
		},

		afterCreateIsotope: function() {

			var point = this.breakpoint();

			this.load(point);
			
			this.$el.addClass('wil-grid--loaded').removeClass('wil-grid--loading');

		},

		load: function(point) {
			var point = this.breakpoint(),
				size = this.getSize(point),
				itemElems = this.getItemElems();

			this.setSize(point);
			this.setContainerSize(point);
			this.setItemElems(itemElems, point, size);
		},

		setContainerSize: function(point) {

			this.container.css({
				'margin-left': -point.horizontal/2,
				'margin-right': -point.horizontal/2 
			});
		},

		getContainerSize: function() {
			var containerSize = this.container.width();
			return containerSize;
		},

		setSize: function(point) {
			this.container.find(this.gridSize).css('width', 100/point.column + '%');
		},

		getSize: function(point) {

			var containerSize = this.getContainerSize(),
				size = containerSize/point.column;
			return size;
		},

		getItemElems: function() {
			return this.container.find(this.gridItem);
		},

		setItemElems: function(itemElems, point, size) {

			var index = 0,
				_this = this;

			itemElems.each(function(i, itemElem) {

				var self = $(this);


				if(point.items && index > point.items.length - 1) {
					index = 0;
				}

				_this.setItemElem(self, point, size, index);

				index ++;

			});
		},

		setItemElem: function(itemElem, point, size, index) {

			var sizes = {width: 1, height: 1},
				ratio = point.ratio,
				width = (100/point.column),
				height = '';

			if(typeof point.items != 'undefined' && typeof point.items[index] != 'undefined') {
				sizes = point.items[index];
			}

			if(typeof ratio == 'undefined' || isNaN(parseFloat(ratio)) ) {
				ratio = 100;
			}

			ratio = (size * ratio)/100;

			if(typeof this.beforeSetItemElem === 'function') {
				this.beforeSetItemElem.apply(this, [itemElem, point, size, index]);
			}

			if( this.styles == 'creative' ) {
				width = sizes.width * width;
				height = Math.floor(ratio) * sizes.height;
			} else if(this.styles == 'grid') {
				height = Math.floor(ratio);
			}

			itemElem.css({
				'width':  width + '%',
				'height': height,
			});

			this.setSpacing(itemElem, point);

			if(typeof this.afterSetItemElem === 'function') {
				this.afterSetItemElem.apply(this, [itemElem, point, size, index]);
			}
		},

		setSpacing: function(itemElem, point) {

			if(this.styles == 'creative' || this.styles == 'grid') {

				itemElem.find(this.options.gridItemInner).css({
					'position': 'absolute',
					'top': 0,
					'bottom': point.vertical,
					'left': point.horizontal/2,
					'right': point.horizontal/2,
				});

			} else {

				itemElem.find(this.options.gridItemInner).css({
					'position': 'relative',
					'padding-top': 0,
					'padding-bottom': point.vertical,
					'padding-left': point.horizontal/2,
					'padding-right': point.horizontal/2,
				});
			}
		},

		appendItemElems: function(itemElems, point, size) {

			var index = 0,
				_this = this;

			itemElems.imagesLoaded( function() { 

				itemElems.each(function(i, itemElem) {

					var $itemElem = $(itemElem);

					if(index > point.items.length - 1) {
						index = 0;
					}

					_this.setItemElem($itemElem, point, size, index);

					_this.container.append( $itemElem ).isotope( 'appended', $itemElem ).isotope('layout');

					index ++;

				});

			});
		},

		filter: function() {

			if( this.filterClass.length ) {

				var _this = this;

				this.filterClass.on('click', 'a', function(event) {

					var self = $(event.currentTarget),
						filter = self.data('filter');

					_this.container.isotope({
						filter: filter
					});

					_this.filterClass.find('.active').removeClass('active');
					self.closest('li').addClass('active');

					return false;

				});

			}
		},

		breakpoint: function() {

			var match = -1, point,
				width = window.innerWidth,
				overwrites = this.options.devices;

			if( overwrites ) {

				$.each(overwrites, function(breakpoint) {
					if (breakpoint <= width && breakpoint > match) {
						match = Number(breakpoint);
					}
				});

				if( match != -1 ) {
					point = overwrites[match];
					point['breakpoint'] = match;
				} else {
					var keys = Object.keys(overwrites),
						small =	Math.min(...keys);

					point = overwrites[small];
					point['breakpoint'] = small;
				}

			} else {

				point = {
					breakpoint: 0,
					vertical: 0,
					horizontal: 0,
					column: 1,
					items: 5,
					grid: [{width: 1, height: 1}]
				}
			}

			return point;
		},

		onResize: function() {

			var _this = this,
				setimeout,
				point = _this.breakpoint(),
				breakpoint = point.breakpoint,
				containerSize = _this.getContainerSize();

			$(window).on('resize', function(event) {

				if(setimeout) {
					clearTimeout(setimeout);
				}

				setimeout = setTimeout(function() {

					var _point = _this.breakpoint(),
						_breakpoint = _point.breakpoint,
						_containerSize = _this.getContainerSize();
					
					if( _breakpoint != breakpoint ) {
						point = _point;
						breakpoint = _breakpoint;
						containerSize = _containerSize;
						_this.settings(_point);
					} else {
						containerSize = _containerSize;
						_this.load();
					}

					clearTimeout(setimeout);

				}, 50);
			});
		}
	}

	$.fn.WilokeIsotope = function(options) {

        return this.each(function() {

        	var $this = $(this),
        		data = $this.data('WilokeIsotope');

    		if(!data) {
            	data = new WilokeIsotope(this, options);
            	$this.data('WilokeIsotope', data);
            }

        });
    };

}));


