$(function() {
	'use strict'
	
	const ps55 = new PerfectScrollbar('#mainContactList', {
		useBothWheelAxes:true,
		suppressScrollX:true,
	});
	const ps56 = new PerfectScrollbar('#mainContactList1', {
		useBothWheelAxes:true,
		suppressScrollX:true,
	});
	
	const ps57 = new PerfectScrollbar('.main-contact-info-body', {
		useBothWheelAxes:true,
		suppressScrollX:true,
	});
	
	if (matchMedia) {
		const mq = window.matchMedia("(max-width: 767px)");
		mq.addListener(WidthChange);
		WidthChange(mq);
	}

	// media query change
	function WidthChange(mq) {
		if (mq.matches) {
		$(document).on('click touch', '.main-contact-item', function() {
				$(this).addClass('selected');
				$(this).siblings().removeClass('selected');
				$('body').addClass('main-content-body-show');
			})
		} else {
		$(document).on('click touch', '.main-contact-item', function() {
				$(this).addClass('selected');
				$(this).siblings().removeClass('selected');
				$('body').removeClass('main-content-body-show');
			})
		}

	}

});

