/* APS Products Javacripts */
(function($) {
	"use strict";
	var dir = ($("html").attr("dir") == "rtl" ? "rtl" : "ltr");
	
	$(window).on("load scroll rating", function() {
		/* animate rating bars */
		$('[data-bar="true"]').each(function() {
			$(this).apsAnimateBar(3000);
		});
		/* just apply CSS width */
		$('[data-bar="false"]').each(function() {
			var elbar = $(this).find('[data-type="bar"]'),
			elbar_width = elbar.data("width");
			elbar.css("width", elbar_width);
		});
	});
	
	/* aps tooltip function */
	$(document).on({
		mouseenter: function() {
			var info = $(this).next(".aps-tooltip-data").html();
			$("body").append('<span class="aps-tooltip-display">' + info + '</span>').show(300);
		},

		mouseleave: function() {
			$(".aps-tooltip-display").hide(50, function() {
				$(this).remove();
			});
		},
		
		mousemove: function(ev) {
			var relY = ev.pageY + 20,
			relX = ev.pageX + 15,
			container = $(".aps-tooltip-display");
			
			if (dir == "rtl") {
				var right_pos = $(window).width() - relX;
				container.css({"top":relY, "right":right_pos});
			} else {
				container.css({"top":relY, "left":relX});
			}
		}
	}, ".aps-tooltip");
	
	/* check element's visibility */
	$.fn.apsIsVisible = function() {
		var win = $(window),
		apsViewport = {
			top : win.scrollTop(),
			left : win.scrollLeft()
		};
		apsViewport.right = apsViewport.left + win.width();
		apsViewport.bottom = apsViewport.top + win.height();
		
		if (this.is(":visible")) {
			var bounds = this.offset();
			bounds.right = bounds.left + this.outerWidth();
			bounds.bottom = bounds.top + this.outerHeight();
		
			return (!(apsViewport.right < bounds.left || apsViewport.left > bounds.right || apsViewport.bottom < bounds.top || apsViewport.top > bounds.bottom));
		}
	}
	
	/* animate rating bar */
	$.fn.apsAnimateBar = function(dur) {
		var elnum = this.find('[data-type="num"]'),
		elbar = this.find('[data-type="bar"]'),
		rating = this.data("rating"),
		sPoint = { num: 0, wid: 0 },
		ePoint = { num: rating, wid: rating * 10 };
		if (elbar.apsIsVisible( true ) && !this.hasClass("aps-animated")) {
			this.addClass("aps-animated");
			$(sPoint).animate(ePoint, {
				duration: dur,
				step: function() {
					elnum.html(Number(this.num.toFixed(1)));
					elbar.css("width", this.wid +"%");
				}
			});
		}
	}
	
	$.cookie = function (key, value, options) {
		/* key and value given, set cookie... */
		if (arguments.length > 1 && (value === null || typeof value !== "object")) {
			var options = jQuery.extend({}, options);

			if (value === null) {
				options.expires = -1;
			}

			if (typeof options.expires === 'number') {
				var days = options.expires, t = options.expires = new Date();
				t.setDate(t.getDate() + days);
			}

			return (document.cookie = [
				encodeURIComponent(key), '=',
				options.raw ? String(value) : encodeURIComponent(String(value)),
				options.expires ? '; expires=' + options.expires.toUTCString() : '',
				options.path ? '; path=' + options.path : '',
				options.domain ? '; domain=' + options.domain : '',
				options.secure ? '; secure' : ''
			].join(''));
		}
		
		/* key and possibly options given, get cookie... */
		var options = value || {};
		var result, decode = options.raw ? function (s) { return s; } : decodeURIComponent;
		return (result = new RegExp('(?:^|; )' + encodeURIComponent(key) + '=([^;]*)').exec(document.cookie)) ? decode(result[1]) : null;
	}

	/* add / remove to compare */
	$(document).on("change", ".aps-compare-cb", function(e) {
		
		/* check if add to compares */
		var this_cb = $(this),
		pid = this_cb.val().toString(),
		ctd = this_cb.data("ctd").toString(),
		this_lb = this_cb.parent(".aps-compare-btn"),
		title = this_lb.data("title");
		
		/* check if add to compares */
		if (this_cb.is(":checked")) {
			if (aps_allow_comp(ctd)) {
				$.apsAddToCompare(pid, ctd);
				
				setTimeout(function() {
					this_lb.find(".aps-compare-txt").text(aps_vars.comp_rem);
				}, 500);
			} else {
				this_cb.prop("checked", false);
			}
		} else {
			$.apsRemoveFrmCompare(pid, ctd);
			
			setTimeout(function() {
				this_lb.find(".aps-compare-txt").text(aps_vars.comp_add);
			}, 500);
		}
		
		/* reload compares list */
		aps_comparison_list(true);
	});

	/* add to compare */
	$(document).on("click", ".aps-add-compare", function(e) {
		e.preventDefault();
		/* add to compare list */
		var this_btn = $(this),
		pid = this_btn.data("pid").toString(),
		ctd = this_btn.data("ctd").toString(),
		reload = this_btn.data("reload"),
		title = this_btn.data("title"),
		comp_arr = $.apsAddToCompare(pid, ctd);
		
		/* reload page if we are on compare page */
		if (typeof reload !== "undefined" && reload == true) {
			setTimeout(function() {
				location = aps_comp_link(comp_arr);
			}, 1000);
		}
	});
	
	/* remove from compare */
	$(document).on("click", ".aps-remove-compare", function(e) {
		e.preventDefault();
		
		var this_btn = $(this),
		pid = this_btn.data("pid").toString(),
		ctd = this_btn.data("ctd").toString(),
		reload = this_btn.data("load"),
		comp_arr = $.apsRemoveFrmCompare(pid, ctd);
		
		/* reload page if we are on compare page */
		if (typeof reload !== "undefined" && reload == true) {
			setTimeout(function() {
				location = aps_comp_link(comp_arr);
			}, 1000);
		} else {
			var this_cb = $("input[name='compare-id-" + pid +"']:checkbox"),
			this_lb = this_cb.parent(".aps-compare-btn");
			this_cb.prop("checked", false);
			setTimeout(function() {
				this_lb.find(".aps-compare-txt").text(aps_vars.comp_add);
			}, 500);
			
			/* reload compares list */
			aps_comparison_list(true);
		}
	});
	
	/* add to compare */
	$.apsAddToCompare = function(pid, ctd) {
		var comp_arr = "",
		comp = $.cookie(aps_vars.comp_cn);
		
		if (comp) {
			var comp_obj = "",
			comp_arrs = comp.split(","),
			comp_arrs = $.grep(comp_arrs, function(n){ return (n); });
			
			for (var i = 0; i < comp_arrs.length; i++) {
				var cat_comp = comp_arrs[i],
				cat_arr = cat_comp.split("_");
				if (cat_arr[0] === ctd) {
					comp_arr = cat_arr[1];
					var comp_obj = cat_comp;
				}
			}
			
			comp_arr = comp_arr.split("-");
			comp_arr = $.grep(comp_arr, function(n){ return (n); });
			
			if ($.inArray(pid, comp_arr) < 0) {
				if (comp_arr.length < aps_vars.comp_max) {
					comp_arr.push(pid);
				}
				var values = comp_arr.join("-");
				values = [ctd, values];
				values = values.join("_");
				
				if (comp_arr.length > 1) {
					comp_arrs[comp_arrs.indexOf(comp_obj)] = values;
				} else {
					comp_arrs.push(values);
				}
				comp_arrs = $.grep(comp_arrs, function(n){ return (n); });
				ck_value = comp_arrs.join(",");
				$.cookie(aps_vars.comp_cn, ck_value, {expires: 7, path: "/"});
			}
		} else {
			comp_arr = pid;
			var ck_value = ctd + "_" + pid;
			$.cookie(aps_vars.comp_cn, ck_value, {expires: 7, path: "/"});
		}
		return comp_arr;
	}
	
	/* remove from compare */
	$.apsRemoveFrmCompare = function(pid, ctd) {
		var comp_arrs = $.cookie(aps_vars.comp_cn).split(","),
		comp_arrs = $.grep(comp_arrs, function(n){ return (n); }),
		comp_arr = "",
		comp_obj = "";
		
		for (var i = 0; i < comp_arrs.length; i++) {
			var cat_comp = comp_arrs[i].split("_");
			if (cat_comp[0] === ctd) {
				comp_arr = cat_comp[1];
				comp_obj = cat_comp;
			}
		}
		
		comp_arr = comp_arr.split("-");
		comp_arr = $.grep(comp_arr, function(n){ return (n); });
		if ($.inArray(pid, comp_arr) > -1) {
			comp_arr.splice($.inArray(pid, comp_arr), 1);
		}
		var values = (comp_arr.length > 0) ? ctd + "_" + comp_arr.join("-") : "";
		comp_arrs[comp_arrs.indexOf(comp_obj.join("_"))] = values;
		comp_arrs = $.grep(comp_arrs, function(n){ return (n); });
		var ck_value = comp_arrs.join(",");
		$.cookie(aps_vars.comp_cn, ck_value, {expires: 7, path: "/"});
		return comp_arr;
	}
	
	/* get compare items hashid */
	function aps_comp_hashid(comp) {
		if (comp) {
			var hashids = new Hashids(aps_vars.comp_cn, 10);
			return hashids.encode(comp);
		}
	}
	
	/* get compare link */
	function aps_comp_link(compList) {
		if (compList) {
			return aps_vars.comp_link + aps_comp_hashid(compList) + "/";
		} else {
			return aps_vars.comp_link;
		}
	}
	
	/* check if max number of compares less */
	function aps_allow_comp(ctd) {
		var comp_arr = "",
		comp = $.cookie(aps_vars.comp_cn);
		
		if (comp) {
			var comp_obj = "",
			comp_arrs = comp.split(","),
			comp_arrs = $.grep(comp_arrs, function(n){ return (n); });
			
			for (var i = 0; i < comp_arrs.length; i++) {
				var cat_comp = comp_arrs[i],
				cat_arr = cat_comp.split("_");
				if (cat_arr[0] === ctd) {
					comp_arr = cat_arr[1];
					var comp_obj = cat_comp;
				}
			}
			
			comp_arr = comp_arr.split("-");
			comp_arr = $.grep(comp_arr, function(n){ return (n); });
			
			if (comp_arr.length == aps_vars.comp_max) {
				return false;
			}
		}
		return true;
	}
	
	/* brands dropdown */
	$(document).on("mouseenter touchstart", ".aps-dropdown", function() {
		$(this).find("ul").stop().slideDown();
	});
	$(document).on("mouseleave touchend", ".aps-dropdown", function() {
		$(this).find("ul").stop().slideUp();
	});
	
	/* display switching (list <> grid) */
	$(document).on("click", ".aps-display-controls li a", function(e) {
		var elmList = $(".aps-products"),
		gridClass = "aps-products-grid",
		listClass = "aps-products-list";
		
		$(".aps-display-controls li a").removeClass("selected");
		$(this).addClass("selected");
		
		if ($(this).hasClass("aps-display-list")) {
			elmList.removeClass(gridClass).addClass(listClass);
			$.cookie("aps_display", "list", {expires: 30, path: "/"});
		} else {
			elmList.removeClass(listClass).addClass(gridClass);
			$.cookie("aps_display", "grid", {expires: 30, path: "/"});
		}
		e.preventDefault();
	});
	
	/* submit review data using ajax */
	$(document).on("submit", "#apsReviewForm", function(e) {
		var rvform = $(this),
		button = rvform.find(".aps-button"),
		rvdata = rvform.serialize();
		$.ajax({
			url: aps_vars.ajaxurl,
			type: "POST",
			data: rvdata,
			dataType: "json",
			beforeSend: function() {
				button.hide();
				button.after('<span class="aps-loading alignright"></span>');
			},
			success: function(res) {
				if (res.success) {
					aps_response_msg("success", res.success, true);
					rvform.trigger("reset");
				} else {
					aps_response_msg("error", res.error, true);
				}
			},
			complete: function() {
				button.next(".aps-loading").remove();
				button.show();
			}
		});
		
		e.preventDefault();
	});
	
	/* display ajax response message */
	function aps_response_msg(icn, msg, auto) {
		if (icn == "success") {
			var content = '<span class="aps-msg-success"><span class="aps-icon-check"></span>' + msg + '</span>';
		} else if (icn == "error") {
			var content = '<span class="aps-msg-errors"><span class="aps-icon-attention"></span>' + msg + '</span>';
		} else {
			var content = msg;
		}
		
		$("body").append('<div class="aps-msg-overlay"></div><div class="aps-res-msg"><span class="aps-icon-cancel aps-close-box aps-close-icon"></span>' + content + '</div>');
		var msg_box = $(".aps-res-msg"),
		msg_overlay = $(".aps-msg-overlay"),
		box_height = msg_box.outerHeight() / 2,
		box_width = msg_box.outerWidth() / 2;
		msg_box.css({marginTop: "-" + box_height + "px", marginLeft: "-" + box_width + "px"});
		msg_overlay.fadeIn(200);
		msg_box.fadeIn(300);
		
		if (auto) {
			setTimeout(remove_box, 3000);
		}
		$(".aps-close-box").click(remove_box);
		
		function remove_box() {
			msg_box.fadeOut("slow", function() {
				$(this).remove();
				msg_overlay.fadeOut("fast", function() {
					$(this).remove();
				});
			});
		}
	}
	
	$(document).on("click", ".aps-comps-handle", function() {
		if ($(this).hasClass("opened")) {
			$(this).removeClass("opened");
			if (dir == "rtl") {
				$(this).parent().animate({"left": "-262px"}, 200);
			} else {
				$(this).parent().animate({"right": "-262px"}, 200);
			}
		} else {
			$(this).addClass("opened");
			if (dir == "rtl") {
				$(this).parent().animate({"left": "0"}, 300);
			} else {
				$(this).parent().animate({"right": "0"}, 300);
			}
		}
	});
	
	/* comparison list overlay */
	function aps_comparison_list(display) {
		if (aps_vars.show_panel) {
			var comps = $.cookie(aps_vars.comp_cn),
			comp_arr = (comps) ? comps.split(",") : [];
			
			if (comp_arr.length > 0) {
				if (!$(".aps-comps-overlay").length) {
					$("body").append('<div class="aps-comps-overlay"></div>');
				}
				
				$(".aps-comps-overlay").addClass("aps-comps-loading");
				var active_list = $(".aps-comps-list.active-list").data("id"),
				cldata = {action: "aps-comps", pos:comps, active:active_list};
				
				$.ajax({
					url: aps_vars.ajaxurl,
					type: "GET",
					data: cldata,
					beforeSend: function() {
						if (display == true) {
							if (dir == "rtl") {
								$(".aps-comps-overlay").animate({"left": "-262px"}, 200);
							} else {
								$(".aps-comps-overlay").animate({"right": "-262px"}, 200);
							}
						}
					},
					success: function(res) {
						if (res) {
							$(".aps-comps-overlay").html(res);
						}
					},
					complete: function() {
						if (display == true) {
							$(".aps-comps-handle").addClass("opened");
							if (dir == "rtl") {
								$(".aps-comps-overlay").animate({"left": "0"}, 300);
							} else {
								$(".aps-comps-overlay").animate({"right": "0"}, 300);
							}
						}
						$(".aps-comps-overlay").removeClass("aps-comps-loading");
					}
				});
			} else {
				if (dir == "rtl") {
					$(".aps-comps-overlay").animate({"left": "-262px"}, 300, function() {
						$(this).remove();
					});
				} else {
					$(".aps-comps-overlay").animate({"right": "-262px"}, 300, function() {
						$(this).remove();
					});
				}
			}
		}
	}
	
	/* display comps list on window load */
	$(window).on("load", function() { aps_comparison_list(false); });
	
	/* compare list carousel */
	$(document).on("click", ".aps-comps-nav span", function(e) {
		var this_btn = $(this),
		comp_list = $(".aps-comps-list.active-list");
		comp_list.removeClass("active-list");
		
		if (this_btn.hasClass("aps-comps-next")) {
			if (comp_list.next(".aps-comps-list").length > 0) {
				comp_list.next(".aps-comps-list").addClass("active-list");
			} else {
				$(".aps-comps-list:first").addClass("active-list");
			}
		} else if (this_btn.hasClass("aps-comps-prev")) {
			if (comp_list.prev(".aps-comps-list").length > 0) {
				comp_list.prev(".aps-comps-list").addClass("active-list");
			} else {
				$(".aps-comps-list:last").addClass("active-list");
			}
		}
	});
	
	$(document).on("click", ".aps-table-fold", function(e) {
		/* toggle display hidden infold attributes */
		var table_fold = $(this),
		fold_open = table_fold.find(".aps-tb-fold-open"),
		fold_close = table_fold.find(".aps-tb-fold-close"),
		attr_tr = table_fold.parent().find(".aps-attr-infold");
		
		if (attr_tr.hasClass("aps-attr-exfold")) {
			attr_tr.removeClass("aps-attr-exfold");
			fold_open.css("display", "block");
			fold_close.css("display", "none");
		} else {
			attr_tr.addClass("aps-attr-exfold");
			fold_open.css("display", "none");
			fold_close.css("display", "block");
		}
		e.preventDefault();
	});
})(jQuery);

jQuery(document).ready(function($) {
	"use strict";
	/* aps tabs */
	if ($(".aps-tab-container").hasClass("aps-tabs-init")) {
		var hashTab = window.location.hash;
		
		if (hashTab) {
			$(hashTab).show();
			$(".aps-tabs li[data-id='" +hashTab+ "']").addClass("active");
		} else {
			$(".aps-tab-content:first").show();
			$(".aps-tabs li:first").addClass("active");
			$(".aps-tabs-bottom li:first").addClass("active");
		}
		
		$("ul.aps-tabs li").on("click", function(e) {
			var tab_li = $(this),
			tab_li_id = tab_li.data("id");
			$("ul.aps-tabs li").removeClass("active");
			$(".aps-tabs li[data-id='" +tab_li_id+ "']").addClass("active");
			var activeTab = tab_li.find("a").attr("href");
			$(activeTab).fadeIn(300);
			$(".aps-tab-content").not(activeTab).hide();
			$("html, body").animate({
				scrollTop: ($(activeTab).offset().top - 80)
			}, 500);
			
			/* set hash for active tab */
			if (history.pushState) {
				history.pushState(null, null, activeTab);
			} else {
				window.location.hash = activeTab;
			}
			
			$(window).trigger("rating");
			e.preventDefault();
		});
	}
	
	/* init image zoom "ImageViewer" */
	if ($(".aps-main-image").hasClass("aps-main-img-zoom")) {
		var viewer = ImageViewer();
		$(".aps-main-image").on("click", function() {
			var zoom_image = $(this).find(".aps-image-zoom"),
			imgSrc = zoom_image.attr("src"),
			highRes = zoom_image.data("src");
			
			viewer.show(imgSrc, highRes);
		});
	}
	
	/* init thumbnail carousel */
	$('[data-carousel="1"]').each(function() {
		var owl_carousel = $(this),
		owl_options = {
			items: (owl_carousel.data("items")) ? owl_carousel.data("items") : 4,
			autoplay: (owl_carousel.data("auto") === 1) ? true : false,
			autoplayTimeout: (owl_carousel.data("timeout")) ? owl_carousel.data("timeout") * 1000 : 5000,
			autoplayHoverPause: (owl_carousel.data("hover") === 1) ? true : false,
			loop: (owl_carousel.data("loop") === 1) ? true : false,
			nav: (owl_carousel.data("nav") === 1) ? true : false,
			margin: (owl_carousel.data("margin")) ? owl_carousel.data("margin") : 10,
			rtl: ($("html").attr("dir") == "rtl" ? true : false),
			navText: (owl_carousel.data("navtext")) ? owl_carousel.data("navtext") : ["&lsaquo;", "&rsaquo;"],
			responsive: (owl_carousel.data("responsive")) ? owl_carousel.data("responsive") : {0: {items: 3}, 768: {items: 4}}
		};
		
		owl_carousel.owlCarousel(owl_options);
	});
	
	/* switch selected thumnail to large image */
	var loaded_imgs = [];
	$(".aps-image-gallery").on("click", "img", function(e) {
		var this_item = $(this),
		img_loader = $(".aps-img-loader"),
		img_zoom = $(".aps-image-zoom"),
		prev_img = img_zoom.attr("src"),
		img_src = this_item.data("src");
		
		/* save the image src in array */
		if ($.inArray(prev_img, loaded_imgs) < 0) {
			loaded_imgs.push(prev_img);
		}
		
		/* make sure the thumbnail is not for large image */
		if (img_zoom.data("src") != img_src) {
			img_zoom.data("src", img_src).attr("src", img_src);
			var new_img_name = img_src.match(/.*\/(.*)$/)[1];
			
			/* make sure the image is not loaded */
			if ($.inArray(img_src, loaded_imgs) < 0) {
				img_loader.fadeIn(50);
				img_zoom.on("load", function() {
					img_loader.fadeOut(300);
				});
			}
			/* add / remove selected class to owl-item */
			$(".aps-thumb-item").removeClass("active-thumb");
			this_item.parent().addClass("active-thumb");
		}
	});
	
	/* nivo lightbox for videos */
	var lb_container = $(".aps-product-videos");
	if (lb_container.data("enable") === 1) {
		var lb_effect = lb_container.data("effect"),
		lb_nav = (lb_container.data("nav") === 1) ? true : false,
		lb_close = (lb_container.data("close") === 1) ? true : false;
		
		$(".aps-lightbox").nivoLightbox({
			effect: lb_effect,
			keyboardNav: lb_nav,
			clickOverlayToClose: lb_close
		});
	}
	
	/* some fixes for range input in webkit */
	var writeTrackStyles = function(el) {
		var id = el.parent().attr("id"),
		value = parseInt(el.val()),
		maxVal = parseInt(el.attr("max")),
		minVal = parseInt(el.data("min")),
		color = $("#apsReviewForm").data("color"),
		grTo = ($("html").attr("dir") == "rtl") ? "left" : "right";
		
		if (value < minVal) {
			el.val(minVal).trigger("change");
			var curVal = (minVal * 100) / maxVal;
		} else{
			var curVal = (value * 100) / maxVal;
		}
		
		var style = "#" +id+ " input::-webkit-slider-runnable-track {background: linear-gradient(to " +grTo+ ", " +color+ " 0%, " +color+ " " + curVal + "%, #e5e6e7 " + curVal + "%, #e5e6e7 100%);}";
		
		if ($("#style-" +id).length > 0) {
			document.getElementById("style-" +id).textContent = style;
		} else {
			var sheet = document.createElement("style");
			sheet.setAttribute("id", "style-" +id);
			document.body.appendChild(sheet);
			sheet.textContent = style;
		}
	};
	
	/* range input slider */
	$(".aps-range-slider-range").each(function() {
		var slider = $(this),
		value = parseInt(slider.val());
		slider.next().html(value);
		writeTrackStyles(slider);
		
		slider.on("input change", function() {
			var range = $(this),
			totalSum = 0, inputs = 0,
			newVal = parseInt(range.val());
			range.next().html(newVal);
			writeTrackStyles(range);
			
			$(".aps-range-slider-range").each(function() {
				totalSum += Number($(this).val());
				inputs++
			});
			
			var totalRating = totalSum / inputs,
			totalScore = totalRating.toFixed(1).replace(/\.0$/, "");
			$(".aps-total-score").text(totalScore);
		});
	});
});

/* hashids */
(function (global, factory) {
	if (typeof define === "function" && define.amd) {
		define(['module', 'exports'], factory);
	} else if (typeof exports !== "undefined") {
		factory(module, exports);
	} else {
		var mod = {
			exports: {}
		};
		factory(mod, mod.exports);
		global.Hashids = mod.exports;
	}
})(this, function (module, exports) {
	'use strict';

	Object.defineProperty(exports, "__esModule", {
		value: true
	});

	function _classCallCheck(instance, Constructor) {
		if (!(instance instanceof Constructor)) {
			throw new TypeError("Cannot call a class as a function");
		}
	}

	var _createClass = function () {
		function defineProperties(target, props) {
			for (var i = 0; i < props.length; i++) {
				var descriptor = props[i];
				descriptor.enumerable = descriptor.enumerable || false;
				descriptor.configurable = true;
				if ("value" in descriptor) descriptor.writable = true;
				Object.defineProperty(target, descriptor.key, descriptor);
			}
		}

		return function (Constructor, protoProps, staticProps) {
			if (protoProps) defineProperties(Constructor.prototype, protoProps);
			if (staticProps) defineProperties(Constructor, staticProps);
			return Constructor;
		};
	}();

	var Hashids = function () {
		function Hashids() {
			var salt = arguments.length <= 0 || arguments[0] === undefined ? '' : arguments[0];
			var minLength = arguments.length <= 1 || arguments[1] === undefined ? 0 : arguments[1];
			var alphabet = arguments.length <= 2 || arguments[2] === undefined ? 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890' : arguments[2];

			_classCallCheck(this, Hashids);

			var minAlphabetLength = 16;
			var sepDiv = 3.5;
			var guardDiv = 12;

			var errorAlphabetLength = 'error: alphabet must contain at least X unique characters';
			var errorAlphabetSpace = 'error: alphabet cannot contain spaces';

			var uniqueAlphabet = '',
			    sepsLength = void 0,
			    diff = void 0;

			/* funcs */

			this.escapeRegExp = function (s) {
				return s.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, '\\$&');
			};
			this.parseInt = function (v, radix) {
				return (/^(\-|\+)?([0-9]+|Infinity)$/.test(v) ? parseInt(v, radix) : NaN
				);
			};

			/* alphabet vars */

			this.seps = 'cfhistuCFHISTU';
			this.minLength = parseInt(minLength, 10) > 0 ? minLength : 0;
			this.salt = typeof salt === 'string' ? salt : '';

			if (typeof alphabet === 'string') {
				this.alphabet = alphabet;
			}

			for (var i = 0; i !== this.alphabet.length; i++) {
				if (uniqueAlphabet.indexOf(this.alphabet.charAt(i)) === -1) {
					uniqueAlphabet += this.alphabet.charAt(i);
				}
			}

			this.alphabet = uniqueAlphabet;

			if (this.alphabet.length < minAlphabetLength) {
				throw errorAlphabetLength.replace('X', minAlphabetLength);
			}

			if (this.alphabet.search(' ') !== -1) {
				throw errorAlphabetSpace;
			}

			/* `this.seps` should contain only characters present in `this.alphabet`
			`this.alphabet` should not contains `this.seps` */

			for (var _i = 0; _i !== this.seps.length; _i++) {

				var j = this.alphabet.indexOf(this.seps.charAt(_i));
				if (j === -1) {
					this.seps = this.seps.substr(0, _i) + ' ' + this.seps.substr(_i + 1);
				} else {
					this.alphabet = this.alphabet.substr(0, j) + ' ' + this.alphabet.substr(j + 1);
				}
			}

			this.alphabet = this.alphabet.replace(/ /g, '');

			this.seps = this.seps.replace(/ /g, '');
			this.seps = this._shuffle(this.seps, this.salt);

			if (!this.seps.length || this.alphabet.length / this.seps.length > sepDiv) {

				sepsLength = Math.ceil(this.alphabet.length / sepDiv);

				if (sepsLength > this.seps.length) {

					diff = sepsLength - this.seps.length;
					this.seps += this.alphabet.substr(0, diff);
					this.alphabet = this.alphabet.substr(diff);
				}
			}

			this.alphabet = this._shuffle(this.alphabet, this.salt);
			var guardCount = Math.ceil(this.alphabet.length / guardDiv);

			if (this.alphabet.length < 3) {
				this.guards = this.seps.substr(0, guardCount);
				this.seps = this.seps.substr(guardCount);
			} else {
				this.guards = this.alphabet.substr(0, guardCount);
				this.alphabet = this.alphabet.substr(guardCount);
			}
		}

		_createClass(Hashids, [{
			key: 'encode',
			value: function encode() {
				for (var _len = arguments.length, numbers = Array(_len), _key = 0; _key < _len; _key++) {
					numbers[_key] = arguments[_key];
				}

				var ret = '';

				if (!numbers.length) {
					return ret;
				}

				if (numbers[0] && numbers[0].constructor === Array) {
					numbers = numbers[0];
					if (!numbers.length) {
						return ret;
					}
				}

				for (var i = 0; i !== numbers.length; i++) {
					numbers[i] = this.parseInt(numbers[i], 10);
					if (numbers[i] >= 0) {
						continue;
					} else {
						return ret;
					}
				}

				return this._encode(numbers);
			}
		}, {
			key: 'decode',
			value: function decode(id) {

				var ret = [];

				if (!id || !id.length || typeof id !== 'string') {
					return ret;
				}

				return this._decode(id, this.alphabet);
			}
		}, {
			key: 'encodeHex',
			value: function encodeHex(hex) {

				hex = hex.toString();
				if (!/^[0-9a-fA-F]+$/.test(hex)) {
					return '';
				}

				var numbers = hex.match(/[\w\W]{1,12}/g);

				for (var i = 0; i !== numbers.length; i++) {
					numbers[i] = parseInt('1' + numbers[i], 16);
				}

				return this.encode.apply(this, numbers);
			}
		}, {
			key: 'decodeHex',
			value: function decodeHex(id) {

				var ret = [];

				var numbers = this.decode(id);

				for (var i = 0; i !== numbers.length; i++) {
					ret += numbers[i].toString(16).substr(1);
				}

				return ret;
			}
		}, {
			key: '_encode',
			value: function _encode(numbers) {

				var ret = void 0,
				    alphabet = this.alphabet,
				    numbersIdInt = 0;

				for (var i = 0; i !== numbers.length; i++) {
					numbersIdInt += numbers[i] % (i + 100);
				}

				ret = alphabet.charAt(numbersIdInt % alphabet.length);
				var lottery = ret;

				for (var _i2 = 0; _i2 !== numbers.length; _i2++) {

					var number = numbers[_i2];
					var buffer = lottery + this.salt + alphabet;

					alphabet = this._shuffle(alphabet, buffer.substr(0, alphabet.length));
					var last = this._toAlphabet(number, alphabet);

					ret += last;

					if (_i2 + 1 < numbers.length) {
						number %= last.charCodeAt(0) + _i2;
						var sepsIndex = number % this.seps.length;
						ret += this.seps.charAt(sepsIndex);
					}
				}

				if (ret.length < this.minLength) {

					var guardIndex = (numbersIdInt + ret[0].charCodeAt(0)) % this.guards.length;
					var guard = this.guards[guardIndex];

					ret = guard + ret;

					if (ret.length < this.minLength) {

						guardIndex = (numbersIdInt + ret[2].charCodeAt(0)) % this.guards.length;
						guard = this.guards[guardIndex];

						ret += guard;
					}
				}

				var halfLength = parseInt(alphabet.length / 2, 10);
				while (ret.length < this.minLength) {

					alphabet = this._shuffle(alphabet, alphabet);
					ret = alphabet.substr(halfLength) + ret + alphabet.substr(0, halfLength);

					var excess = ret.length - this.minLength;
					if (excess > 0) {
						ret = ret.substr(excess / 2, this.minLength);
					}
				}

				return ret;
			}
		}, {
			key: '_decode',
			value: function _decode(id, alphabet) {

				var ret = [],
				    i = 0,
				    r = new RegExp('[' + this.escapeRegExp(this.guards) + ']', 'g'),
				    idBreakdown = id.replace(r, ' '),
				    idArray = idBreakdown.split(' ');

				if (idArray.length === 3 || idArray.length === 2) {
					i = 1;
				}

				idBreakdown = idArray[i];
				if (typeof idBreakdown[0] !== 'undefined') {

					var lottery = idBreakdown[0];
					idBreakdown = idBreakdown.substr(1);

					r = new RegExp('[' + this.escapeRegExp(this.seps) + ']', 'g');
					idBreakdown = idBreakdown.replace(r, ' ');
					idArray = idBreakdown.split(' ');

					for (var j = 0; j !== idArray.length; j++) {

						var subId = idArray[j];
						var buffer = lottery + this.salt + alphabet;

						alphabet = this._shuffle(alphabet, buffer.substr(0, alphabet.length));
						ret.push(this._fromAlphabet(subId, alphabet));
					}

					if (this._encode(ret) !== id) {
						ret = [];
					}
				}

				return ret;
			}
		}, {
			key: '_shuffle',
			value: function _shuffle(alphabet, salt) {

				var integer = void 0;

				if (!salt.length) {
					return alphabet;
				}

				for (var i = alphabet.length - 1, v = 0, p = 0, j = 0; i > 0; i--, v++) {

					v %= salt.length;
					p += integer = salt.charAt(v).charCodeAt(0);
					j = (integer + v + p) % i;

					var tmp = alphabet[j];
					alphabet = alphabet.substr(0, j) + alphabet.charAt(i) + alphabet.substr(j + 1);
					alphabet = alphabet.substr(0, i) + tmp + alphabet.substr(i + 1);
				}

				return alphabet;
			}
		}, {
			key: '_toAlphabet',
			value: function _toAlphabet(input, alphabet) {

				var id = '';

				do {
					id = alphabet.charAt(input % alphabet.length) + id;
					input = parseInt(input / alphabet.length, 10);
				} while (input);

				return id;
			}
		}, {
			key: '_fromAlphabet',
			value: function _fromAlphabet(input, alphabet) {

				var number = 0;

				for (var i = 0; i < input.length; i++) {
					var pos = alphabet.indexOf(input[i]);
					number += pos * Math.pow(alphabet.length, input.length - i - 1);
				}

				return number;
			}
		}]);

		return Hashids;
	}();

	exports.default = Hashids;
	module.exports = exports['default'];
});