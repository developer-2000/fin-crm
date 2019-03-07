/* JS Cookie lib
 JavaScript Cookie v2.1.4
 * https://github.com/js-cookie/js-cookie
 *
 * Copyright 2006, 2015 Klaus Hartl & Fagner Brack
 * Released under the MIT license
 * https://github.com/js-cookie/js-cookie/blob/master/src/js.cookie.js
 * */
(function (factory) {
	var registeredInModuleLoader = false;
	if (typeof define === 'function' && define.amd) {
		define(factory);
		registeredInModuleLoader = true;
	}
	if (typeof exports === 'object') {
		module.exports = factory();
		registeredInModuleLoader = true;
	}
	if (!registeredInModuleLoader) {
		var OldCookies = window.Cookies;
		var api = window.Cookies = factory();
		api.noConflict = function () {
			window.Cookies = OldCookies;
			return api;
		};
	}
}(function () {

	//��������� ���� ������ �� �������� � ��������
	function extend () {
		var i = 0;
		var result = {};
		//������ �� ����������
		for (; i < arguments.length; i++) {
			//������ �� ��������� ���������
			var attributes = arguments[ i ];

			for (var key in attributes) {
				result[key] = attributes[key];
			}

		}
		return result;
	}

	function init (converter) {

		function api (key, value, attributes) {
			var result;
			if (typeof document === 'undefined') {
				return;
			}

			// Write

			if (arguments.length > 1) {
				attributes = extend({
					path: '/'
				}, api.defaults, attributes);

				if (typeof attributes.expires === 'number') {
					var expires = new Date();
					expires.setMilliseconds(expires.getMilliseconds() + attributes.expires * 864e+5);
					attributes.expires = expires;
				}

				// We're using "expires" because "max-age" is not supported by IE
				attributes.expires = attributes.expires ? attributes.expires.toUTCString() : '';

				try {
					result = JSON.stringify(value);

					if (/^[\{\[]/.test(result)) {
						value = result;
					}
				} catch (e) {}

				if (!converter.write) {
					value = encodeURIComponent(String(value))
						.replace(/%(23|24|26|2B|3A|3C|3E|3D|2F|3F|40|5B|5D|5E|60|7B|7D|7C)/g, decodeURIComponent);
				} else {
					value = converter.write(value, key);
				}

				key = encodeURIComponent(String(key));
				key = key.replace(/%(23|24|26|2B|5E|60|7C)/g, decodeURIComponent);
				key = key.replace(/[\(\)]/g, escape);

				var stringifiedAttributes = '';

				for (var attributeName in attributes) {
					if (!attributes[attributeName]) {
						continue;
					}
					stringifiedAttributes += '; ' + attributeName;
					if (attributes[attributeName] === true) {
						continue;
					}
					stringifiedAttributes += '=' + attributes[attributeName];
				}
				return (document.cookie = key + '=' + value + stringifiedAttributes);
			}

			// Read

			if (!key) {
				result = {};
			}

			// To prevent the for loop in the first place assign an empty array
			// in case there are no cookies at all. Also prevents odd result when
			// calling "get()"
			var cookies = document.cookie ? document.cookie.split('; ') : [];

			//��������� ��� ������ � ���������� ������������ ���� ������ � ��
			var rdecode = /(%[0-9A-Z]{2})+/g;

			var i = 0;
			for (; i < cookies.length; i++) {
				var parts = cookies[i].split('=');
				var cookie = parts.slice(1).join('=');

				if (cookie.charAt(0) === '"') {
					cookie = cookie.slice(1, -1);
				}

				try {
					var name = parts[0].replace(rdecode, decodeURIComponent);
					cookie = converter.read ?
						converter.read(cookie, name) : converter(cookie, name) ||
					cookie.replace(rdecode, decodeURIComponent);

					if (this.json) {
						try {
							cookie = JSON.parse(cookie);
						} catch (e) {}
					}

					if (key === name) {
						result = cookie;
						break;
					}

					if (!key) {
						result[name] = cookie;
					}
				} catch (e) {}
			}

			return result;
		}

		api.set = api;
		api.get = function (key) {
			return api.call(api, key);
		};

		api.getJSON = function () {
			return api.apply({
				json: true
			}, [].slice.call(arguments));
		};

		api.defaults = {};

		api.remove = function (key, attributes) {
			api(key, '', extend(attributes, {
				expires: -1
			}));
		};

		api.withConverter = init;

		return api;
	}

	return init(function () {});
}));

/*------------------------------------------------------------------
Project:	Afro - Bootstrap Admin Template .
Version:	1.1
Last change:	28/09/16 
Autor:			Ravi Jaiswal
-------------------------------------------------------------------*/
$(function($) {
	setTimeout(function() {
		$('#content-wrapper > .row').css({
			opacity: 1
		});
	}, 200);
	
	$('#sidebar-nav,#nav-col-submenu').on('click', '.dropdown-toggle', function (e) {
		e.preventDefault();
		
		var $item = $(this).parent();

		if (!$item.hasClass('open')) {
			$item.parent().find('.open .submenu').slideUp('fast');
			$item.parent().find('.open').toggleClass('open');
		}
		
		$item.toggleClass('open');
		
		if ($item.hasClass('open')) {
			$item.children('.submenu').slideDown('fast');
		} 
		else {
			$item.children('.submenu').slideUp('fast');
		}
	});
	
	$('body').on('mouseenter', '#page-wrapper.nav-small #sidebar-nav .dropdown-toggle', function (e) {
		if ($( document ).width() >= 992) {
			var $item = $(this).parent();

			if ($('body').hasClass('fixed-leftmenu')) {
				var topPosition = $item.position().top;

				if ((topPosition + 4*$(this).outerHeight()) >= $(window).height()) {
					topPosition -= 6*$(this).outerHeight();
				}

				$('#nav-col-submenu').html($item.children('.submenu').clone());
				$('#nav-col-submenu > .submenu').css({'top' : topPosition});
			}

			$item.addClass('open');
			$item.children('.submenu').slideDown('fast');
		}
	});
	
	$('body').on('mouseleave', '#page-wrapper.nav-small #sidebar-nav > .nav-pills > li', function (e) {
		if ($( document ).width() >= 992) {
			var $item = $(this);
	
			if ($item.hasClass('open')) {
				$item.find('.open .submenu').slideUp('fast');
				$item.find('.open').removeClass('open');
				$item.children('.submenu').slideUp('fast');
			}
			
			$item.removeClass('open');
		}
	});
	$('body').on('mouseenter', '#page-wrapper.nav-small #sidebar-nav a:not(.dropdown-toggle)', function (e) {
		if ($('body').hasClass('fixed-leftmenu')) {
			$('#nav-col-submenu').html('');
		}
	});
	$('body').on('mouseleave', '#page-wrapper.nav-small #nav-col', function (e) {
		if ($('body').hasClass('fixed-leftmenu')) {
			$('#nav-col-submenu').html('');
		}
	});
	
	$('#make-small-nav').on('click',function (e) {
		$('#page-wrapper').toggleClass('nav-small');
		$('#logo').toggleClass('nav-small');
		if (!Cookies.get('navHidden'))
			Cookies.set('navHidden', '1', { expires: 1 });
		else
			Cookies.remove('navHidden');
	});
	
	$(window).smartresize(function(){
		if ($( document ).width() <= 991) {
			$('#page-wrapper').removeClass('nav-small');
		}
	});
	
	if ($( document ).width() <= 991) {
		$('#sidebar-nav').removeClass('navbar-collapse');
	}

	
	$('.mobile-search').on('click',function(e) {
		e.preventDefault();
		
		$('.mobile-search').addClass('active');
		$('.mobile-search form input.form-control').focus();
	});
	$(document).mouseup(function (e) {
		var container = $('.mobile-search');

		if (!container.is(e.target) // if the target of the click isn't the container...
			&& container.has(e.target).length === 0) // ... nor a descendant of the container
		{
			container.removeClass('active');
		}
	});
	
	$('.fixed-leftmenu #col-left').nanoScroller({
    	alwaysVisible: false,
    	iOSNativeScrolling: false,
    	preventPageScrolling: true,
    	contentClass: 'col-left-nano-content'
    });
	
	// build all tooltips from data-attributes
	$("[data-toggle='tooltip']").each(function (index, el) {
		$(el).tooltip({
			placement: $(this).data("placement") || 'top'
		});
	});
});

$.fn.removeClassPrefix = function(prefix) {
    this.each(function(i, el) {
        var classes = el.className.split(" ").filter(function(c) {
            return c.lastIndexOf(prefix, 0) !== 0;
        });
        el.className = classes.join(" ");
    });
    return this;
};

(function($,sr){
	// debouncing function from John Hann
	// http://unscriptable.com/index.php/2009/03/20/debouncing-javascript-methods/
	var debounce = function (func, threshold, execAsap) {
		var timeout;

		return function debounced () {
			var obj = this, args = arguments;
			function delayed () {
				if (!execAsap)
					func.apply(obj, args);
				timeout = null;
			};

			if (timeout)
				clearTimeout(timeout);
			else if (execAsap)
				func.apply(obj, args);

			timeout = setTimeout(delayed, threshold || 100);
		};
	}
	// smartresize 
	jQuery.fn[sr] = function(fn){	return fn ? this.bind('resize', debounce(fn)) : this.trigger(sr); };

})(jQuery,'smartresize');