jQuery().ready(function () {


    jQuery('.lang .select-image.drop-down').append('<div class="button"></div>');
    jQuery('.lang .select-image.drop-down').append('<ul class="select-list"></ul>');
    jQuery('.lang .select-image.drop-down select option').each(function () {
        var bg = jQuery(this).css('background-image');
        jQuery('.select-list').append('<li class="clsAnchor"><span value="' + jQuery(this).val() + '" class="' + jQuery(this).attr('class') + '" style=background-image:' + bg + '>' + jQuery(this).text() + '</span></li>');
    });
    jQuery('.lang .select-image.drop-down .button').html('<span style=background-image:' + jQuery('.lang .select-image.drop-down select').find(':selected').css('background-image') + '>' + jQuery('.lang .select-image.drop-down select').find(':selected').text() + '</span>' + '<a href="javascript:void(0);" class="select-list-link"></a>');
    jQuery('.lang .select-image.drop-down ul li').each(function () {
        if (jQuery(this).find('span').text() == jQuery('.drop-down select').find(':selected').text()) {
            jQuery(this).addClass('active');
        }
    });
    jQuery('.lang .select-image.drop-down .select-list span').on('click', function () {
        var dd_text = jQuery(this).text();
        var dd_img = jQuery(this).css('background-image');
        var dd_val = jQuery(this).attr('value');
        jQuery('.lang .select-image.drop-down .button').html('<span style=background-image:' + dd_img + '>' + dd_text + '</span>' + '<a href="javascript:void(0);" class="select-list-link"></a>');
        jQuery('.lang .select-image.drop-down .select-list span').parent().removeClass('active');
        jQuery(this).parent().addClass('active');
        $('.lang .select-image.drop-down select[name=options]').val(dd_val);
        $('.lang .select-image.drop-down .select-list li').slideUp();
    });
    jQuery('.lang .select-image.drop-down .button').on('click', 'a.select-list-link', function () {
        jQuery('.lang .select-image.drop-down ul li').slideToggle();
    });




//---_______-----//
   



});

