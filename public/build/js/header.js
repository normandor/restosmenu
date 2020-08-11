$('body').on('click', '.show-sidebar', function (e) {
    e.preventDefault();
    $('div#main').toggleClass('sidebar-show');
    setTimeout(MessagesMenuWidth, 250);
});

$('.main-menu').on('click', 'a', function (e) {
    var parents = $(this).parents('li');
    var li = $(this).closest('li.dropdown');
    var another_items = $('.main-menu li').not(parents);
    another_items.find('a').removeClass('active');
    another_items.find('a').removeClass('active-parent');
    if ($(this).hasClass('dropdown-toggle') || $(this).closest('li').find('ul').length == 0) {
        $(this).addClass('active-parent');
        var current = $(this).next();
        if (current.is(':visible')) {
            li.find("ul.dropdown-menu").slideUp('fast');
            li.find("ul.dropdown-menu a").removeClass('active');
        } else {
            another_items.find("ul.dropdown-menu").slideUp('fast');
            current.slideDown('fast');
        }
    } else {
        if (li.find('a.dropdown-toggle').hasClass('active-parent')) {
            var pre = $(this).closest('ul.dropdown-menu');
            pre.find("li.dropdown").not($(this).closest('li')).find('ul.dropdown-menu').slideUp('fast');
        }
    }
    if ($(this).hasClass('active') == false) {
        $(this).parents("ul.dropdown-menu").find('a').removeClass('active');
        $(this).addClass('active');
    }
    if ($(this).attr('href') == '#') {
        e.preventDefault();
    }
});

$('.dropdown:has(li:has(a.active)) > a').addClass('active-parent active');
$('.dropdown:has(li:has(a.active)) > ul').css("display", "block");

function toggleCollapseHeader() {
    $('.collapse').collapse('toggle');
}

function MessagesMenuWidth() {
    var W = window.innerWidth;
    var W_menu = $('#sidebar-left').outerWidth();
    var w_messages = (W - W_menu) * 16.666666666666664 / 100;
    $('#messages-menu').width(w_messages);
}