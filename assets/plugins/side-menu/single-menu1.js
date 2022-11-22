(function () {
	"use strict";

	var slideMenu = $('.side-menu');
	$('.app').addClass('sidebar-mini');

	// Toggle Sidebar
	$(document).on('click','[data-toggle="sidebar"]',function(event) {
		event.preventDefault();
		$('.app').toggleClass('sidenav-toggled');
	});

	$(".app-sidebar").hover(function() {
		if ($('body').hasClass('sidenav-toggled')) {
			$('body').addClass('sidenav-toggled-open');
		}
	}, function() {
		if ($('body').hasClass('sidenav-toggled')) {
			$('body').removeClass('sidenav-toggled-open');
		}
	});


	
    //Mobile menu
    jQuery(document).ready(function($) {
      var alterClass = function() {
        var ww = document.body.clientWidth;
        if (ww < 739) {
          $('body').removeClass('sidenav-toggled');
        } else if (ww >= 738) {
          $('body').addClass('sidenav-toggled');
        };
      };
      $(window).resize(function(){
        alterClass();
      });
      //Fire it when the page first loads:
      alterClass();
    });
  

	// Activate sidebar slide toggle
	$("[data-toggle='slide']").click(function(event) {
		event.preventDefault();
		if(!$(this).parent().hasClass('is-expanded')) {
			slideMenu.find("[data-toggle='slide']").parent().removeClass('is-expanded');
		}
		$(this).parent().toggleClass('is-expanded');
	});
	
	$("[data-toggle='sub-slide']").click(function(event) {
		event.preventDefault();
		if(!$(this).parent().hasClass('is-expanded')) {
			slideMenu.find("[data-toggle='sub-slide']").parent().removeClass('is-expanded');
		}
		$(this).parent().toggleClass('is-expanded');
		$('.slide.active').addClass('is-expanded');
	});
	
	// Set initial active toggle
	$("[data-toggle='slide.'].is-expanded").parent().toggleClass('is-expanded');
	$("[data-toggle='sub-slide.'].is-expanded").parent().toggleClass('is-expanded');
	

	//Activate bootstrip tooltips
	$("[data-toggle='tooltip']").tooltip();
	
	
	// ______________Active Class
	$(".app-sidebar a").each(function() {
	  var pageUrl = window.location.href.split(/[?#]/)[0];
		if (this.href == pageUrl) { 
			$(this).addClass("active");
			$(this).parent().addClass("active"); // add active to li of the current link
			$(this).parent().parent().prev().addClass("active"); // add active class to an anchor
			$(this).parent().parent().parent().parent().parent().addClass("active"); 
			$(this).parent().parent().prev().click(); // click the item to make it drop
		}
	});
	
	
	var toggleSidebar = function() {
		var w = $(window);
		if(w.outerWidth() <= 1024) {
			$("body").addClass("sidebar-gone");
			$(document).off("click", "body").on("click", "body", function(e) {
				if($(e.target).hasClass('sidebar-show') || $(e.target).hasClass('search-show')) {
					$("body").removeClass("sidebar-show");
					$("body").addClass("sidebar-gone");
					$("body").removeClass("search-show");
				}
			});
		}else{
			$("body").removeClass("sidebar-gone");
		}
	}
	toggleSidebar();
	$(window).resize(toggleSidebar);
	
	
	//P-scrolling
	const ps = new PerfectScrollbar('.main-sidemenu', {
	  useBothWheelAxes:true,
	  suppressScrollX:true,
	});
	
	
	//sticky-header
		
	
	function updateScroll() {
    if ($(window).scrollTop() >= 70) {
        $(".main-header").addClass('fixed-header');
    } else {
        $(".main-header").removeClass("fixed-header");
    }
}
$(function() {
    $(window).scroll(updateScroll);
    updateScroll();
});
	
})();

