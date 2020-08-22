$(function() {
    'use strict';

    // Dashboard
    $('.toggle-info').click(function () {
        $(this).toggleClass('selected').parent().next('.panel-body').fadeToggle(100);

        if($(this).hasClass('selected'))
        {
            $(this).html('<i class="fa fa-minus fa-lg"></i>');
        }
        else
            {
                $(this).html('<i class="fa fa-plus fa-lg"></i>');
            }
    });

    $('select').selectBoxIt({
        autoWidth: false
    });

    // Hide placeholder on form focus.
    $('[placeholder]').focus(function (){
        $(this).attr('data-text', $(this).attr('placeholder'));
        $(this).attr('placeholder', '');
    }).blur(function (){
        $(this).attr('placeholder', $(this).attr('data-text'));
    });

    // Add Asterisk On Required Field.
    $('input').each(function() {
        if($(this).attr('required') === 'required')
        {
            $(this).after('<span class="asterisk">*</span>');
        }
    });

    // Convert Password Field To Text Field On Hover
    var passField = $('.password');
    $('.show-pass').hover(function() {
        passField.attr('type', 'text');
    }, function(){
        passField.attr('type','password');
    });
    
    // Confirmation Message On Button.
    $('.confirm').click(function () {
        return confirm('Are you sure ? ');
    });

    // Category View Option
    $('.cate h3').click(function () {
        $(this).next().fadeToggle(200);
    });

    $('.option span').click(function () {
        $(this).addClass('active').siblings('span').removeClass('active');

        if($(this).data('view') === 'full')
        {
            $('.cate .full-view').fadeIn(200);
        }
        else
            {
                $('.cate .full-view').fadeOut(200);
            }
    });

    // Show Delete Button On Child Categories
    $('.child-link').hover(function () {
        $(this).find('.show-delete').fadeIn(200);
    }, function () {
        $(this).find('.show-delete').fadeOut(200);
    })

});