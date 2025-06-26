$(function() {
	'use strict'
	$('#chatActiveContacts').lightSlider({
		autoWidth: true,
		controls: false,
		pager: false,
		slideMargin: 12
	});
	if (window.matchMedia('(min-width: 992px)').matches) {
		new PerfectScrollbar('#ChatList', {
			suppressScrollX: true
		});
		new PerfectScrollbar('#ChatCalls', {
			suppressScrollX: true
		});
		new PerfectScrollbar('#ChatContacts', {
			suppressScrollX: true
		});
		var ChatBody = new PerfectScrollbar('#ChatBody', {
			suppressScrollX: true
		});
		$('#ChatBody').scrollTop($('#ChatBody').prop('scrollHeight'));
	}
	$(document).on('click touch', '#ChatList .media', function() {
		$(this).addClass('selected').removeClass('new');
		$(this).siblings().removeClass('selected');
		if (window.matchMedia('(max-width: 991px)').matches) {
			$('body').addClass('main-content-body-show');
			$('html body').scrollTop($('html body').prop('scrollHeight'));
		}
	});
	$('[data-toggle="tooltip"]').tooltip();
	$(document).on('click touch', '#ChatBodyHide', function(e) {
		e.preventDefault();
		$('body').removeClass('main-content-body-show');
	})
});