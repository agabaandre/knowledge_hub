"use strict";
$(document).ready(function() {
    togglemenu();
    if ($('body').hasClass('layout-6') || $('body').hasClass('layout-7')) {
        togglemenulayout();
    }
    menuhrres();
    var vw = $(window)[0].innerWidth;
    $('[data-toggle="tooltip"]').tooltip();
    $('[data-toggle="popover"]').popover();
    $('.to-do-list input[type=checkbox]').on('click', function() {
        if ($(this).prop('checked'))
            $(this).parent().addClass('done-task');
        else
            $(this).parent().removeClass('done-task');
    });
    $(".mobile-menu").on('click', function() {
        var $this = $(this);
        $this.toggleClass('on');
    });
    $("#mobile-collapse").on('click', function() {
        $(".pcoded-navbar:not(.theme-horizontal)").toggleClass("navbar-collapsed");
    });
    $(".search-btn").on('click', function() {
        var $this = $(this);
        $(".main-search").addClass('open');
        if (vw <= 991) {
            $(".main-search .form-control").css({
                'width': '90px'
            });
        } else {
            $(".main-search .form-control").css({
                'width': '150px'
            });
        }
    });
    $(".search-close").on('click', function() {
        var $this = $(this);
        $(".main-search").removeClass('open');
        $(".main-search .form-control").css({
            'width': '0'
        });
    });
    // search
    $("#search-friends").on("keyup", function() {
        var g = $(this).val().toLowerCase();
        $(".header-user-list .userlist-box .media-body .chat-header").each(function() {
            var s = $(this).text().toLowerCase();
            $(this).closest('.userlist-box')[s.indexOf(g) !== -1 ? 'show' : 'hide']();
        });
    });
    $("#m-search").on("keyup", function() {
        var g = $(this).val().toLowerCase();
        var ln = $(this).val().length;

        $(".pcoded-inner-navbar > li").each(function() {
            var t = $(this).attr('data-username');
            if (t) {
                var s = t.toLowerCase();
            }
            if (s) {
                var n = s.indexOf(g);
                if (n !== -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
                if (ln > 0) {
                    $('.pcoded-menu-caption').hide();
                } else {
                    $('.pcoded-menu-caption').show();
                }
            }
        });
    });
    // open user list
    $('.displayChatbox').on('click', function() {
        $('.header-user-list').toggleClass('open');
    });
    // open messages
    $('.header-user-list .userlist-box').on('click', function() {
        $('.header-chat').addClass('open');
        $('.header-user-list').toggleClass('msg-open');
    });
    // close user list
    $('.h-back-user-list').on('click', function() {
        $('.header-chat').removeClass('open');
        $('.header-user-list').removeClass('msg-open');
    });

    //  full chat
    $('.h-close-text').on('click', function() {
        $('.header-chat').removeClass('open');
        $('.header-user-list').removeClass('open');
        $('.header-user-list').removeClass('msg-open');
    });
    $('.btn-attach').click(function() {
        $('.chat-attach').trigger('click');
    });
    $('.h-send-chat').on('keyup', function(e) {
        fc(e);
    });
    $('.btn-send').on('click', function(e) {
        cfc(e);
    });
    if (vw <= 991) {
        $(".main-search").addClass('open');
        $(".main-search .form-control").css({
            // 'width': '60px'
            'width': '120px'
        });
    }
    // Friend scroll
    if ($('.main-friend-cont')[0]) {
        $(".main-friend-cont").each(function() {
            var px = new PerfectScrollbar(this, {
                wheelSpeed: .5,
                swipeEasing: 0,
                suppressScrollX: !0,
                wheelPropagation: 1,
                minScrollbarLength: 40,
            });
        });
    }
    if ($('.main-chat-cont')[0]) {
        var px = new PerfectScrollbar('.main-chat-cont', {
            wheelSpeed: .5,
            swipeEasing: 0,
            suppressScrollX: !0,
            wheelPropagation: 1,
            minScrollbarLength: 40,
        });
    }
    if ($('.noti-body')[0]) {
        var px = new PerfectScrollbar('.notification  .noti-body', {
            wheelSpeed: .5,
            swipeEasing: 0,
            suppressScrollX: !0,
            wheelPropagation: 1,
            minScrollbarLength: 40,
        });
    }
    // Menu scroll
    if (!$('.pcoded-navbar').hasClass('theme-horizontal')) {
        var vw = $(window)[0].innerWidth;
        if ($('.navbar-content')[0]) {
            if (vw < 992 || $('.pcoded-navbar').hasClass('menupos-static')) {
                var px = new PerfectScrollbar('.navbar-content', {
                    wheelSpeed: .5,
                    swipeEasing: 0,
                    suppressScrollX: !0,
                    wheelPropagation: 1,
                    minScrollbarLength: 40,
                });
            } else {
                var px = new PerfectScrollbar('.navbar-content', {
                    wheelSpeed: .5,
                    swipeEasing: 0,
                    suppressScrollX: !0,
                    wheelPropagation: 1,
                    minScrollbarLength: 40,
                });
            }
        }
    }

    function fc(e) {
        if (e.which == 13) {
            cfc(e)
        }
    };

    function cfc(e) {
        $('.header-chat .main-friend-chat').append('' +
            '<div class="media chat-messages">' +
            '<div class="media-body chat-menu-reply">' +
            '<div class="">' +
            '<p class="chat-cont">' + $('.h-send-chat').val() + '</p>' +
            '</div>' +
            '<p class="chat-time">now</p>' +
            '</div>' +
            '<a class="media-right photo-table" href="#!"><img class="media-object img-radius img-radius m-t-5" src="../assets/images/user/avatar-1.jpg" alt="Generic placeholder image"></a>' +
            '</div>' +
            '');
        frc($('.h-send-chat').val());
        fsc();
        $('.h-send-chat').val(null);
    };

    function frc(wrmsg) {
        setTimeout(function() {
            $('.header-chat .main-friend-chat').append('' +
                '<div class="media chat-messages typing">' +
                '<a class="media-left photo-table" href="#!"><img class="media-object img-radius img-radius m-t-5" src="../assets/images/user/avatar-2.jpg" alt="Generic placeholder image"></a>' +
                '<div class="media-body chat-menu-content">' +
                '<div class="rem-msg">' +
                '<p class="chat-cont">Typing . . .</p>' +
                '</div>' +
                '<p class="chat-time">now</p>' +
                '</div>' +
                '</div>' +
                '');
            fsc();
        }, 1500);
        setTimeout(function() {
            document.getElementsByClassName("rem-msg")[0].innerHTML = "<p class='chat-cont'>hello dear you write</p> <p class='chat-cont'>" + wrmsg + "</p>";
            $('.rem-msg').removeClass("rem-msg");
            $('.typing').removeClass("typing");
            fsc();
        }, 3000);
    };

    function fsc() {
        var tmph = $('.header-chat .main-friend-chat');
        $('.main-chat-cont.scroll-div').scrollTop(tmph.outerHeight());
    }
    // close card
    $(".card-option .close-card").on('click', function() {
        var $this = $(this);
        $this.parents('.card').addClass('anim-close-card');
        $this.parents('.card').animate({
            'margin-bottom': '0',
        });
        setTimeout(function() {
            $this.parents('.card').children('.card-block').slideToggle();
            $this.parents('.card').children('.card-body').slideToggle();
            $this.parents('.card').children('.card-header').slideToggle();
            $this.parents('.card').children('.card-footer').slideToggle();
        }, 600);
        setTimeout(function() {
            $this.parents('.card').remove();
        }, 1500);
    });
    // reload card
    $(".card-option .reload-card").on('click', function() {
        var $this = $(this);
        $this.parents('.card').addClass("card-load");
        $this.parents('.card').append('<div class="card-loader"><i class="pct-loader1 anim-rotate"></div>');
        setTimeout(function() {
            $this.parents('.card').children(".card-loader").remove();
            $this.parents('.card').removeClass("card-load");
        }, 3000);
    });
    // collpased and expaded card
    $(".card-option .minimize-card").on('click', function() {
        var $this = $(this);
        var port = $($this.parents('.card'));
        var card = $(port).children('.card-block').slideToggle();
        var card = $(port).children('.card-body').slideToggle();
        if (!port.hasClass('full-card')) {
            $(port).css("height", "auto");
        }
        $(this).children('a').children('span').toggle();
    });
    // full card
    $(".card-option .full-card").on('click', function() {
        var $this = $(this);
        var port = $($this.parents('.card'));
        port.toggleClass("full-card");
        $(this).children('a').children('span').toggle();
        if (port.hasClass('full-card')) {
            $('body').css('overflow', 'hidden');
            $('html,body').animate({
                scrollTop: 0
            }, 1000);
            var elm = $(port, this);
            var off = elm.offset();
            var l = off.left;
            var t = off.top;
            var docH = $(window).height();
            var docW = $(window).width();
            port.animate({
                'marginLeft': l - (l * 2),
                'marginTop': t - (t * 2),
                'width': docW,
                'height': docH,
            });
        } else {
            $('body').css('overflow', '');
            port.removeAttr('style');
            setTimeout(function() {
                $('html,body').animate({
                    scrollTop: ($(port).offset().top) - 70
                }, 500);
            }, 400);
        }
    });
    // apply matchHeight to each item container's items
    // remove pre-loader start
    setTimeout(function() {
        $('.loader-bg').fadeOut('slow', function() {
            $(this).remove();
        });
    }, 400);
    // remove pre-loader end
});

// ===============
$(window).resize(function() {
    togglemenu();
    menuhrres();
    if ($('body').hasClass('layout-6') || $('body').hasClass('layout-7')) {
        togglemenulayout();
    }
});
// menu [ horizontal configure ]
function menuhrres() {
    var vw = $(window)[0].innerWidth;
    if (vw < 992) {
        setTimeout(function() {
            $(".sidenav-horizontal-wrapper").addClass("sidenav-horizontal-wrapper-dis").removeClass("sidenav-horizontal-wrapper");
            $(".theme-horizontal").addClass("theme-horizontal-dis").removeClass("theme-horizontal");
        }, 400);
    } else {
        setTimeout(function() {
            $(".sidenav-horizontal-wrapper-dis").addClass("sidenav-horizontal-wrapper").removeClass("sidenav-horizontal-wrapper-dis");
            $(".theme-horizontal-dis").addClass("theme-horizontal").removeClass("theme-horizontal-dis");
        }, 400);
    }
    // Menu scroll
    setTimeout(function() {
        if ($('.pcoded-navbar').hasClass('theme-horizontal-dis')) {
            $(".sidenav-horizontal-wrapper-dis").css({
                'height': '100%',
                'position': 'relative'
            });
            if ($('.sidenav-horizontal-wrapper-dis')[0]) {
                var px = new PerfectScrollbar('.sidenav-horizontal-wrapper-dis', {
                    wheelSpeed: .5,
                    swipeEasing: 0,
                    suppressScrollX: !0,
                    wheelPropagation: 1,
                    minScrollbarLength: 40,
                });
            }
        }
    }, 1000);
}
var ost = 0;
$(window).scroll(function() {
    var vw = $(window)[0].innerWidth;
    if (vw >= 768) {
        var cOst = $(this).scrollTop();
        if (cOst == 400) {
            $('.theme-horizontal').addClass('top-nav-collapse');
        } else if (cOst > ost && 400 < ost) {
            $('.theme-horizontal').addClass('top-nav-collapse').removeClass('default');
        } else {
            $('.theme-horizontal').addClass('default').removeClass('top-nav-collapse');
        }
        ost = cOst;
    }
});

// menu [ compact ]
function togglemenu() {
    var vw = $(window)[0].innerWidth;
    if ($(".pcoded-navbar").hasClass('theme-horizontal') == false) {
        if (vw <= 1200 && vw >= 992) {
            $(".pcoded-navbar:not(.theme-horizontal)").addClass("navbar-collapsed");
        }
        if (vw < 992) {
            $(".pcoded-navbar:not(.theme-horizontal)").removeClass("navbar-collapsed");
        }
    }
}
// ===============

// toggle full screen
function toggleFullScreen() {
    var a = $(window).height() - 10;

    if (!document.fullscreenElement && // alternative standard method
        !document.mozFullScreenElement && !document.webkitFullscreenElement) { // current working methods
        if (document.documentElement.requestFullscreen) {
            document.documentElement.requestFullscreen();
        } else if (document.documentElement.mozRequestFullScreen) {
            document.documentElement.mozRequestFullScreen();
        } else if (document.documentElement.webkitRequestFullscreen) {
            document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
        }
    } else {
        if (document.cancelFullScreen) {
            document.cancelFullScreen();
        } else if (document.mozCancelFullScreen) {
            document.mozCancelFullScreen();
        } else if (document.webkitCancelFullScreen) {
            document.webkitCancelFullScreen();
        }
    }
    $('.full-screen > i').toggleClass('icon-maximize');
    $('.full-screen > i').toggleClass('icon-minimize');
}
// =============   layout builder   =============
$.fn.pcodedmenu = function(settings) {
    var oid = this.attr("id");
    var defaults = {
        themelayout: 'vertical',
        MenuTrigger: 'click',
        SubMenuTrigger: 'click',
    };
    var settings = $.extend({}, defaults, settings);
    var PcodedMenu = {
        PcodedMenuInit: function() {
            PcodedMenu.HandleMenuTrigger();
            PcodedMenu.HandleSubMenuTrigger();
            PcodedMenu.HandleOffset();
        },
        HandleSubMenuTrigger: function() {
            var $window = $(window);
            var newSize = $window.width();
            if ($('.pcoded-navbar').hasClass('theme-horizontal') == true || $('.pcoded-navbar').hasClass('theme-horizontal-dis') == true) {
                if ((newSize >= 992 && $('body').hasClass('layout-6') || (newSize >= 992 && $('body').hasClass('layout-7')))) {
                    var $dropdown = $("body[class*='layout-6'] .theme-horizontal .pcoded-inner-navbar .pcoded-submenu > li.pcoded-hasmenu, body[class*='layout-7'] .theme-horizontal .pcoded-inner-navbar .pcoded-submenu > li.pcoded-hasmenu");
                    $dropdown.off('click').off('mouseenter mouseleave').hover(
                        function() {
                            $(this).addClass('pcoded-trigger').addClass('active');
                        },
                        function() {
                            $(this).removeClass('pcoded-trigger').removeClass('active');
                        }
                    );
                } else {
                    if ($('body').hasClass('layout-6') || $('body').hasClass('layout-7')) {
                        if ($('.pcoded-navbar').hasClass('theme-horizontal-dis')) {
                            var $dropdown = $(".pcoded-navbar.theme-horizontal-dis .pcoded-inner-navbar .pcoded-submenu > li.pcoded-hasmenu");
                            $dropdown.off('click').off('mouseenter mouseleave').hover(
                                function() {
                                    $(this).addClass('pcoded-trigger').addClass('active');
                                },
                                function() {
                                    $(this).removeClass('pcoded-trigger').removeClass('active');
                                }
                            );
                        }
                        if (!$('.pcoded-navbar').hasClass('theme-horizontal-dis')) {
                            var $dropdown = $(".pcoded-navbar:not(.theme-horizontal-dis) .pcoded-inner-navbar .pcoded-submenu > li > .pcoded-submenu > li");
                            $dropdown.off('mouseenter mouseleave').off('click').on('click',
                                function() {
                                    var str = $(this).closest('.pcoded-submenu').length;
                                    if (str === 0) {
                                        if ($(this).hasClass('pcoded-trigger')) {
                                            $(this).removeClass('pcoded-trigger');
                                            $(this).children('.pcoded-submenu').slideUp();
                                        } else {
                                            $('.pcoded-submenu > li > .pcoded-submenu > li.pcoded-trigger').children('.pcoded-submenu').slideUp();
                                            $(this).closest('.pcoded-inner-navbar').find('li.pcoded-trigger').removeClass('pcoded-trigger');
                                            $(this).addClass('pcoded-trigger');
                                            $(this).children('.pcoded-submenu').slideDown();
                                        }
                                    } else {
                                        if ($(this).hasClass('pcoded-trigger')) {
                                            $(this).removeClass('pcoded-trigger');
                                            $(this).children('.pcoded-submenu').slideUp();
                                        } else {
                                            $('.pcoded-submenu > li > .pcoded-submenu > li.pcoded-trigger').children('.pcoded-submenu').slideUp();
                                            $(this).closest('.pcoded-submenu').find('li.pcoded-trigger').removeClass('pcoded-trigger');
                                            $(this).addClass('pcoded-trigger');
                                            $(this).children('.pcoded-submenu').slideDown();
                                        }
                                    }
                                });
                            $(".pcoded-inner-navbar .pcoded-submenu > li > .pcoded-submenu > li").on('click', function(e) {
                                e.stopPropagation();
                                var str = $(this).closest('.pcoded-submenu').length;
                                if (str === 0) {
                                    if ($(this).hasClass('pcoded-trigger')) {
                                        $(this).removeClass('pcoded-trigger');
                                        $(this).children('.pcoded-submenu').slideUp();
                                    } else {
                                        $('.pcoded-hasmenu li.pcoded-trigger').children('.pcoded-submenu').slideUp();
                                        $(this).closest('.pcoded-inner-navbar').find('li.pcoded-trigger').removeClass('pcoded-trigger');
                                        $(this).addClass('pcoded-trigger');
                                        $(this).children('.pcoded-submenu').slideDown();
                                    }
                                } else {
                                    if ($(this).hasClass('pcoded-trigger')) {
                                        $(this).removeClass('pcoded-trigger');
                                        $(this).children('.pcoded-submenu').slideUp();
                                    } else {
                                        $('.pcoded-hasmenu li.pcoded-trigger').children('.pcoded-submenu').slideUp();
                                        $(this).closest('.pcoded-submenu').find('li.pcoded-trigger').removeClass('pcoded-trigger');
                                        $(this).addClass('pcoded-trigger');
                                        $(this).children('.pcoded-submenu').slideDown();
                                    }
                                }
                            });
                        }
                    } else {
                        if (newSize >= 992) {
                            var $dropdown = $(".pcoded-navbar.theme-horizontal .pcoded-inner-navbar .pcoded-submenu > li.pcoded-hasmenu");
                            $dropdown.off('click').off('mouseenter mouseleave').hover(
                                function() {
                                    $(this).addClass('pcoded-trigger').addClass('active');
                                },
                                function() {
                                    $(this).removeClass('pcoded-trigger').removeClass('active');
                                }
                            );
                        } else {
                            var $dropdown = $(".pcoded-navbar.theme-horizontal-dis .pcoded-inner-navbar .pcoded-submenu > li > .pcoded-submenu > li");
                            $dropdown.off('mouseenter mouseleave').off('click').on('click',
                                function() {
                                    var str = $(this).closest('.pcoded-submenu').length;
                                    if (str === 0) {
                                        if ($(this).hasClass('pcoded-trigger')) {
                                            $(this).removeClass('pcoded-trigger');
                                            $(this).children('.pcoded-submenu').slideUp();
                                        } else {
                                            $('.pcoded-submenu > li > .pcoded-submenu > li.pcoded-trigger').children('.pcoded-submenu').slideUp();
                                            $(this).closest('.pcoded-inner-navbar').find('li.pcoded-trigger').removeClass('pcoded-trigger');
                                            $(this).addClass('pcoded-trigger');
                                            $(this).children('.pcoded-submenu').slideDown();
                                        }
                                    } else {
                                        if ($(this).hasClass('pcoded-trigger')) {
                                            $(this).removeClass('pcoded-trigger');
                                            $(this).children('.pcoded-submenu').slideUp();
                                        } else {
                                            $('.pcoded-submenu > li > .pcoded-submenu > li.pcoded-trigger').children('.pcoded-submenu').slideUp();
                                            $(this).closest('.pcoded-submenu').find('li.pcoded-trigger').removeClass('pcoded-trigger');
                                            $(this).addClass('pcoded-trigger');
                                            $(this).children('.pcoded-submenu').slideDown();
                                        }
                                    }
                                });
                        }
                    }
                }
            }
            switch (settings.SubMenuTrigger) {
                case 'click':
                    $('.pcoded-navbar .pcoded-hasmenu').removeClass('is-hover');
                    $(".pcoded-inner-navbar .pcoded-submenu > li > .pcoded-submenu > li").on('click', function(e) {
                        e.stopPropagation();
                        var str = $(this).closest('.pcoded-submenu').length;
                        if (str === 0) {
                            if ($(this).hasClass('pcoded-trigger')) {
                                $(this).removeClass('pcoded-trigger');
                                $(this).children('.pcoded-submenu').slideUp();
                            } else {
                                $('.pcoded-submenu > li > .pcoded-submenu > li.pcoded-trigger').children('.pcoded-submenu').slideUp();
                                $(this).closest('.pcoded-inner-navbar').find('li.pcoded-trigger').removeClass('pcoded-trigger');
                                $(this).addClass('pcoded-trigger');
                                $(this).children('.pcoded-submenu').slideDown();
                            }
                        } else {
                            if ($(this).hasClass('pcoded-trigger')) {
                                $(this).removeClass('pcoded-trigger');
                                $(this).children('.pcoded-submenu').slideUp();
                            } else {
                                $('.pcoded-submenu > li > .pcoded-submenu > li.pcoded-trigger').children('.pcoded-submenu').slideUp();
                                $(this).closest('.pcoded-submenu').find('li.pcoded-trigger').removeClass('pcoded-trigger');
                                $(this).addClass('pcoded-trigger');
                                $(this).children('.pcoded-submenu').slideDown();
                            }
                        }
                    });
                    $(".pcoded-submenu > li").on('click', function(e) {
                        e.stopPropagation();
                        var str = $(this).closest('.pcoded-submenu').length;
                        if (str === 0) {
                            if ($(this).hasClass('pcoded-trigger')) {
                                $(this).removeClass('pcoded-trigger');
                                $(this).children('.pcoded-submenu').slideUp();
                            } else {
                                $('.pcoded-hasmenu li.pcoded-trigger').children('.pcoded-submenu').slideUp();
                                $(this).closest('.pcoded-inner-navbar').find('li.pcoded-trigger').removeClass('pcoded-trigger');
                                $(this).addClass('pcoded-trigger');
                                $(this).children('.pcoded-submenu').slideDown();
                            }
                        } else {
                            if ($(this).hasClass('pcoded-trigger')) {
                                $(this).removeClass('pcoded-trigger');
                                $(this).children('.pcoded-submenu').slideUp();
                            } else {
                                $('.pcoded-hasmenu li.pcoded-trigger').children('.pcoded-submenu').slideUp();
                                $(this).closest('.pcoded-submenu').find('li.pcoded-trigger').removeClass('pcoded-trigger');
                                $(this).addClass('pcoded-trigger');
                                $(this).children('.pcoded-submenu').slideDown();
                            }
                        }
                    });
                    break;
            }
        },
        HandleMenuTrigger: function() {
            var $window = $(window);
            var newSize = $window.width();
            if (newSize >= 992) {
                if ($('.pcoded-navbar').hasClass('theme-horizontal') == true) {
                    if ((newSize >= 768 && $('body').hasClass('layout-6') || (newSize >= 768 && $('body').hasClass('layout-7')))) {
                        var $dropdown = $("body[class*='layout-6'] .theme-horizontal .pcoded-inner-navbar > li,body[class*='layout-7'] .theme-horizontal .pcoded-inner-navbar > li ");
                        $dropdown.off('click').off('mouseenter mouseleave').hover(
                            function() {
                                $(this).addClass('pcoded-trigger').addClass('active');
                                if ($('.pcoded-submenu', this).length) {
                                    var elm = $('.pcoded-submenu:first', this);
                                    var off = elm.offset();
                                    var l = off.left;
                                    var w = elm.width();
                                    var docH = $(window).height();
                                    var docW = $(window).width();

                                    var isEntirelyVisible = (l + w <= docW);
                                    if (!isEntirelyVisible) {
                                        var temp = $('.sidenav-inner').attr('style');
                                        $('.sidenav-inner').css({
                                            'margin-left': (parseInt(temp.slice(12, temp.length - 3)) - 80)
                                        });
                                        $('.sidenav-horizontal-prev').removeClass('disabled');
                                    } else {
                                        $(this).removeClass('edge');
                                    }
                                }
                            },
                            function() {
                                $(this).removeClass('pcoded-trigger').removeClass('active');
                            }
                        );
                    } else {
                        if ($('body').hasClass('layout-6') || $('body').hasClass('layout-7')) {
                            if ($('.pcoded-navbar').hasClass('theme-horizontal-dis')) {
                                var $dropdown = $(".pcoded-navbar.theme-horizontal-dis .pcoded-inner-navbar > li");
                                $dropdown.off('click').off('mouseenter mouseleave').hover(
                                    function() {
                                        $(this).addClass('pcoded-trigger').addClass('active');
                                        if ($('.pcoded-submenu', this).length) {
                                            var elm = $('.pcoded-submenu:first', this);
                                            var off = elm.offset();
                                            var l = off.left;
                                            var w = elm.width();
                                            var docH = $(window).height();
                                            var docW = $(window).width();

                                            var isEntirelyVisible = (l + w <= docW);
                                            if (!isEntirelyVisible) {
                                                var temp = $('.sidenav-inner').attr('style');
                                                $('.sidenav-inner').css({
                                                    'margin-left': (parseInt(temp.slice(12, temp.length - 3)) - 80)
                                                });
                                                $('.sidenav-horizontal-prev').removeClass('disabled');
                                            } else {
                                                $(this).removeClass('edge');
                                            }
                                        }
                                    },
                                    function() {
                                        $(this).removeClass('pcoded-trigger').removeClass('active');
                                    }
                                );
                            }
                            if (!$('.pcoded-navbar').hasClass('theme-horizontal-dis')) {
                                var $dropdown = $(".pcoded-navbar:not(.theme-horizontal-dis) .pcoded-inner-navbar > li");
                                $dropdown.off('mouseenter mouseleave').off('click').on('click',
                                    function() {
                                        if ($(this).hasClass('pcoded-trigger')) {
                                            $(this).removeClass('pcoded-trigger');
                                            $(this).children('.pcoded-submenu').slideUp();
                                        } else {
                                            $('li.pcoded-trigger').children('.pcoded-submenu').slideUp();
                                            $(this).closest('.pcoded-inner-navbar').find('li.pcoded-trigger').removeClass('pcoded-trigger');
                                            $(this).addClass('pcoded-trigger');
                                            $(this).children('.pcoded-submenu').slideDown();
                                        }
                                    }
                                );
                            }
                        } else {
                            var $dropdown = $(".theme-horizontal .pcoded-inner-navbar > li");
                            $dropdown.off('click').off('mouseenter mouseleave').hover(
                                function() {
                                    $(this).addClass('pcoded-trigger').addClass('active');
                                    if ($('.pcoded-submenu', this).length) {
                                        var elm = $('.pcoded-submenu:first', this);
                                        var off = elm.offset();
                                        var l = off.left;
                                        var w = elm.width();
                                        var docH = $(window).height();
                                        var docW = $(window).width();

                                        var isEntirelyVisible = (l + w <= docW);
                                        if (!isEntirelyVisible) {
                                            var temp = $('.sidenav-inner').attr('style');
                                            $('.sidenav-inner').css({
                                                'margin-left': (parseInt(temp.slice(12, temp.length - 3)) - 80)
                                            });
                                            $('.sidenav-horizontal-prev').removeClass('disabled');
                                        } else {
                                            $(this).removeClass('edge');
                                        }
                                    }
                                },
                                function() {
                                    $(this).removeClass('pcoded-trigger').removeClass('active');
                                }
                            );
                        }
                    }
                }
            } else {
                var $dropdown = $(".pcoded-navbar.theme-horizontal-dis .pcoded-inner-navbar > li");
                $dropdown.off('mouseenter mouseleave').off('click').on('click',
                    function() {
                        if ($(this).hasClass('pcoded-trigger')) {
                            $(this).removeClass('pcoded-trigger');
                            $(this).children('.pcoded-submenu').slideUp();
                        } else {
                            $('li.pcoded-trigger').children('.pcoded-submenu').slideUp();
                            $(this).closest('.pcoded-inner-navbar').find('li.pcoded-trigger').removeClass('pcoded-trigger');
                            $(this).addClass('pcoded-trigger');
                            $(this).children('.pcoded-submenu').slideDown();
                        }
                    });
            }
            switch (settings.MenuTrigger) {
                case 'click':
                    $('.pcoded-navbar').removeClass('is-hover');
                    $(".pcoded-inner-navbar > li:not(.pcoded-menu-caption) ").on('click', function() {
                        if ($(this).hasClass('pcoded-trigger')) {
                            $(this).removeClass('pcoded-trigger');
                            $(this).children('.pcoded-submenu').slideUp();
                        } else {
                            $('li.pcoded-trigger').children('.pcoded-submenu').slideUp();
                            $(this).closest('.pcoded-inner-navbar').find('li.pcoded-trigger').removeClass('pcoded-trigger');
                            $(this).addClass('pcoded-trigger');
                            $(this).children('.pcoded-submenu').slideDown();
                        }
                    });
                    break;
            }
        },
        HandleOffset: function() {
            switch (settings.themelayout) {
                case 'horizontal':
                    var trigger = settings.SubMenuTrigger;
                    if (trigger === "hover") {
                        $("li.pcoded-hasmenu").on('mouseenter mouseleave', function(e) {
                            if ($('.pcoded-submenu', this).length) {
                                var elm = $('.pcoded-submenu:first', this);
                                var off = elm.offset();
                                var l = off.left;
                                var w = elm.width();
                                var docH = $(window).height();
                                var docW = $(window).width();

                                var isEntirelyVisible = (l + w <= docW);
                                if (!isEntirelyVisible) {
                                    $(this).addClass('edge');
                                } else {
                                    $(this).removeClass('edge');
                                }
                            }
                        });
                    } else {
                        $("li.pcoded-hasmenu").on('click', function(e) {
                            e.preventDefault();
                            if ($('.pcoded-submenu', this).length) {
                                var elm = $('.pcoded-submenu:first', this);
                                var off = elm.offset();
                                var l = off.left;
                                var w = elm.width();
                                var docH = $(window).height();
                                var docW = $(window).width();

                                var isEntirelyVisible = (l + w <= docW);
                                if (!isEntirelyVisible) {
                                    $(this).toggleClass('edge');
                                }
                            }
                        });
                    }
                    break;
                default:
            }
        },
    };
    PcodedMenu.PcodedMenuInit();
};
$("#pcoded").pcodedmenu({
    MenuTrigger: 'click',
    SubMenuTrigger: 'click',
});

// menu [ Mobile ]
$("#mobile-collapse,#mobile-collapse1").click(function(e) {
    var vw = $(window)[0].innerWidth;
    if (vw < 992) {
        $(".pcoded-navbar").toggleClass('mob-open');
        e.stopPropagation();
    }
});
$(window).ready(function() {
    var vw = $(window)[0].innerWidth;
    $(".pcoded-navbar").on('click tap', function(e) {
        e.stopPropagation();
    });
    $('.pcoded-main-container,.pcoded-header').on("click", function() {
        if (vw < 992) {
            if ($(".pcoded-navbar").hasClass("mob-open") == true) {
                $(".pcoded-navbar").removeClass('mob-open');
                $("#mobile-collapse,#mobile-collapse1").removeClass('on');
            }
        }
    });
    // mobile header
    $("#mobile-header").on('click', function() {
        $(".navbar-collapse,.m-header").slideToggle();
    });
});
$('.pcoded-navbar .close').on('click', function() {
    var port = $(this);
    port.parents('.card').fadeOut('slow').remove();
});
// Layout 1 navbar start
$('.layout-1 .sidemenu a').on('click', function() {
    var port = $(this);
    port.parents('li').siblings().removeClass('active');
    port.parents('li').addClass('active');
    $('.side-content .sidelink').slideUp();
    $('.side-content .sidelink.' + port.attr('data-cont')).slideDown();
});
$('.layout-1 .toggle-sidemenu').on('click', function() {
    var port = $(this);
    $('.pcoded-navbar').toggleClass('hide-sidemenu');
});
// Layout 1 navbar End

// Layout 6,7 navbar start
$("#mobile-collapse1").click(function(e) {
    var vw = $(window)[0].innerWidth;
    if ($('body').hasClass('layout-6') || $('body').hasClass('layout-7')) {
        setTimeout(function() {
            if (vw <= 992) {
                $('.pcoded-navbar:not(.theme-horizontal-dis)').removeClass('mob-open');
            }
            if (vw > 992) {
                $(".pcoded-navbar:not(.theme-horizontal)").toggleClass('mob-open');
                e.stopPropagation();
            }
        }, 100);
    }
});
$('#show-toggle').on('click', function() {
    $('.pcoded-navbar:not(.theme-horizontal-dis)').toggleClass('mob-open-h');
});
if ($('body').hasClass('layout-6') || $('body').hasClass('layout-7')) {
    if ($('.navbar-content')[0]) {
        var px = new PerfectScrollbar('.navbar-content', {
            wheelSpeed: .5,
            swipeEasing: 0,
            suppressScrollX: !0,
            wheelPropagation: 1,
            minScrollbarLength: 40,
        });
    }
}
$('.pcoded-main-container,.pcoded-header,.pcoded-navbar.theme-horizontal').on("click", function() {
    var vw = $(window)[0].innerWidth;
    if ($('body').hasClass('layout-6') || $('body').hasClass('layout-7')) {
        if (vw > 992) {
            if ($(".pcoded-navbar").hasClass("mob-open") == true) {
                $(".pcoded-navbar").removeClass('mob-open');
                $("#mobile-collapse,#mobile-collapse1").removeClass('on');
            }
        }
    }
});

function togglemenulayout() {
    var vw = $(window)[0].innerWidth;
    if ($('body').hasClass('layout-6') || $('body').hasClass('layout-7')) {
        if (vw <= 1200 && vw >= 992) {
            $(".pcoded-navbar:not(.theme-horizontal)").addClass("navbar-collapsed");
        }
        if (vw < 992) {
            $(".pcoded-navbar:not(.theme-horizontal)").removeClass("navbar-collapsed");
        }
    }
}
// Layout 8 horizontal navbar start

// .navbar-nav
$('body.layout-8 .pcoded-navbar .dropdown-toggle').on('click', function() {
    $(this).siblings('.dropdown-menu').addClass('show');
});
$(document).mouseup(function(e) {
    var container = $("body.layout-8 .pcoded-navbar .dropdown-menu");
    if (!container.is(e.target) && container.has(e.target).length === 0) {
        container.removeClass('show');
    }
});

// Layout 9 navbar start
$('.layout-9 .sidemenu a').on('click', function() {
    var port = $(this);
    port.parents('li').siblings().removeClass('active');
    port.parents('li').addClass('active');
    $('.side-content .sidelink').slideUp(1);
    $('.side-content .sidelink.' + port.attr('data-cont')).slideDown(1);
});
// Layout 9 navbar End

// [ Page Breadcumb ] start
var vw = $(window)[0].innerWidth;
if (vw < 992) {
    if ($('.page-wrapper>.page-header')[0]) {
        $('.pcoded-header > .collapse:not(.show)>.mr-auto').css('display', "none");
        $('.pcoded-header > .collapse:not(.show)>.ml-auto').css('display', "block");
        $('.pcoded-header > .container> .collapse:not(.show)>.mr-auto').css('display', "none");
        $('.pcoded-header > .container> .collapse:not(.show)>.ml-auto').css('display', "block");
        if ($('.pcoded-header > .container> .collapse')[0]) {
            console.log("hii");
            $('.pcoded-header > .container> .collapse:not(.show)').append('<a href="#!" class="mob-toggler"></a>');
        }
    } else {
        $('.pcoded-header > .collapse:not(.show)').append('<a href="#!" class="mob-toggler"></a>');
    }
}
if ($('.pcoded-header .page-header')[0]) {
    var dt = $('.pcoded-main-container .page-header').clone();
    $('.pcoded-header .page-header').html(dt);
    $('.pcoded-main-container .page-header').remove();
}
if ($('.page-header-title h5')[0]) {
    var page = $('.page-header-title h5').html();
    $(document).attr("title", "Flash Able - " + page);
}
// responsive header click
$('.pcoded-header > .collapse:not(.show) .mob-toggler').click(function() {
    $('.pcoded-header > .collapse:not(.show) > ul').slideToggle(1);
});
$('.pcoded-header > .container>.collapse:not(.show) .mob-toggler').click(function() {
    $('.pcoded-header > .container> .collapse:not(.show) > ul').slideToggle(1);
});
// [ Page Breadcumb ] end

// [ Layout 14 ] start
if (vw >= 992) {
    $('.layout-14 .pcoded-inner-navbar > li.pcoded-hasmenu').unbind("click");
}
$('.layout-14 .pcoded-inner-navbar > li.pcoded-hasmenu').on('mouseenter', function() {
    if (vw >= 992) {
        clrpop()
        $('.navbar-wrapper').append('<div class="navbar-popup"></div>')
        var port = $(this);
        var stage = $('.navbar-popup');
        var dt = port.children('.pcoded-submenu').clone();
        stage.addClass('pcoded-inner-navbar');
        stage.html(dt);
        var elm = $(port, this);
        var off = elm.offset();
        var l = off.left;
        var t = off.top;
        var w = elm.width();
        var docH = $(window).height();
        var docW = $(window).width();
        var srl = $(window).scrollTop();
        var sth = stage.innerHeight()
        stage.show();
        console.log(srl);
        if (docH <= t + sth - srl) {
            stage.addClass('vedge');
            stage.animate({
                'top': t - 240 - srl
            }, 1);
        } else {
            stage.removeClass('vedge');
            stage.animate({
                'top': t - 8 - srl
            }, 1);
        }
        $('.navbar-popup > .pcoded-submenu').css({
            'max-height': '300px',
            'position': 'relative'
        });
        var px = new PerfectScrollbar('.navbar-popup > .pcoded-submenu', {
            wheelSpeed: .5,
            swipeEasing: 0,
            suppressScrollX: !0,
            wheelPropagation: 1,
            minScrollbarLength: 40,
        });
        $(".navbar-popup .pcoded-submenu > li").on('click', function(e) {
            e.stopPropagation();
            var str = $(this).closest('.pcoded-submenu').length;
            if (str === 0) {
                if ($(this).hasClass('pcoded-trigger')) {
                    $(this).removeClass('pcoded-trigger');
                    $(this).children('.pcoded-submenu').slideUp();
                } else {
                    $('.pcoded-hasmenu li.pcoded-trigger').children('.pcoded-submenu').slideUp();
                    $(this).closest('.pcoded-inner-navbar').find('li.pcoded-trigger').removeClass('pcoded-trigger');
                    $(this).addClass('pcoded-trigger');
                    $(this).children('.pcoded-submenu').slideDown();
                }
            } else {
                if ($(this).hasClass('pcoded-trigger')) {
                    $(this).removeClass('pcoded-trigger');
                    $(this).children('.pcoded-submenu').slideUp();
                } else {
                    $('.pcoded-hasmenu li.pcoded-trigger').children('.pcoded-submenu').slideUp();
                    $(this).closest('.pcoded-submenu').find('li.pcoded-trigger').removeClass('pcoded-trigger');
                    $(this).addClass('pcoded-trigger');
                    $(this).children('.pcoded-submenu').slideDown();
                }
            }
        });
    }
});

function clrpop() {
    $('.navbar-popup').remove();
}
$('.layout-14 .pcoded-navbar').on('mouseleave', function() {
    clrpop()
});
$('.layout-14 .pcoded-inner-navbar > li:not(.pcoded-hasmenu)').on('mouseenter', function() {
    clrpop()
});

// [ Layout 14 ] end


// active menu item list start
$(".pcoded-navbar .pcoded-inner-navbar a").each(function() {
    var pageUrl = window.location.href.split(/[?#]/)[0];
    if (!$('body').hasClass('layout-14')) {
        if (this.href == pageUrl && $(this).attr('href') != "") {
            $(this).parent('li').addClass("active");
            if (!$('.pcoded-navbar').hasClass('theme-horizontal')) {
                $(this).parent('li').parent().parent('.pcoded-hasmenu').addClass("active").addClass("pcoded-trigger");
                $(this).parent('li').parent().parent('.pcoded-hasmenu').parent().parent('.pcoded-hasmenu').addClass("active").addClass("pcoded-trigger");
            }
            if ($('body').hasClass('layout-7') || $('body').hasClass('layout-6')) {
                $(this).parent('li').parent().parent('.pcoded-hasmenu').addClass("active").addClass("pcoded-trigger");
                $(this).parent('li').parent().parent('.pcoded-hasmenu').parent().parent('.pcoded-hasmenu').addClass("active").addClass("pcoded-trigger");
                $('.theme-horizontal .pcoded-inner-navbar').find('li.pcoded-trigger').removeClass('pcoded-trigger');
            }
            $(this).parent('li').parent().parent('.sidelink').addClass("active");
            $(this).parent('li').parent().parent().parent().parent('.sidelink').addClass("active");
            $(this).parent('li').parent().parent().parent().parent().parent().parent('.sidelink').addClass("active");
            if ($('body').hasClass('layout-1') || $('body').hasClass('layout-9')) {
                var temp = $('.sidelink.active').attr('class');
                temp = temp.replace("sidelink", "");
                temp = temp.replace("active", "");
                $('.sidemenu  .nav-link[data-cont=' + temp.trim() + ']').parent().addClass('active');
            }
        }
    }
});
// active menu item list end
