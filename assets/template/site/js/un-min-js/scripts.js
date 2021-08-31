function myFunction() {
    var dots = document.getElementById("dots");
    var moreText = document.getElementById("more");
    var btnText = document.getElementById("myBtn");

    if (dots.style.display === "none") {
        dots.style.display = "inline";
        btnText.innerHTML = "Read more";
        moreText.style.display = "none";
    } else {
        dots.style.display = "none";
        btnText.innerHTML = "Read less";
        moreText.style.display = "inline";
    }
}


/*===================================================================================*/
jQuery(document).ready(function () {

    /*if $(".header-payment input[type=radio]:checked")(function(){
       $(this).parent(".header-payment") .siblings(".main-payment").show();
     });

*/

$(".search-section-menu .icon").click(function () {
    $(".search-area-popup").toggle();
 
});

    /*===================================================================================*/
    $(".show-password .svg-eye").click(function () {
        $(".show-password + input").attr('type', 'text');
        $(".show-password .svg-eye").hide();
        $(".show-password .svg-uneye").show();
    });

    $(".show-password .svg-uneye").click(function () {
        $(".show-password + input").attr('type', 'password');
        $(".show-password .svg-eye").show();
        $(".show-password .svg-uneye").hide();
    });
    /*===================================================================================*/

    $(".collapce").click(function () {
        $(this).parent(".form").parent(".store-name-title").siblings(".shopping-cart-container").toggle();

        $(this).find(".plus").toggleClass("show-icon");
        $(this).find(" .min").toggleClass("show-icon");

    });


    /*===================================================================================*/


    $(".toastsclose").click(function () {
        $(this).parent(".toastscontent").parent(".toastscontainer").hide();
    });

    $(".add-toast").click(function () {
        $(".toastscontainer").addClass("show-toast");
    });


    function markLinks() {
        var $links = $('.show-toast');
        $links.each(function (i) {
            var $this = $(this);
            $this.removeClass("show-toast").dequeue();

        });
        setTimeout(markLinks, 5000);
    }

    markLinks();

    /*===================================================================================*/

    $('.tooltip-button').tooltip({boundary: 'window'})


    /*===================================================================================*/
    $(".larg").addClass("active-class");
    $(".larg").click(function () {
        $(".search-iteams").addClass("grid-class");
        $(".larg").addClass("active-class");
        $(".list").removeClass("active-class");
        $(".search-iteams").removeClass("list-class");
    });

    $(".list").click(function () {
        $(".search-iteams").addClass("list-class");
        $(".list").addClass("active-class");
        $(".larg").removeClass("active-class");
        $(".search-iteams").removeClass("grid-class");
    });
    /*===================================================================================*/
    $(".filter-ocard .toggle-filter").click(function () {
        $(this).parent(".title").siblings(".filter-items").slideToggle();
        $(this).children(".min").toggle();
        $(this).children(".plus").toggle();
        $(this).parent(".title").parent(".filter-ocard").children(".link-show-more").toggle();

    });
    /*===================================================================================*/
    $(".mobile-action").click(function () {
        $(".filter-left-area").addClass("show");

    });

    $(".close-filter").click(function () {
        $(".filter-left-area").removeClass("show");
    });

    $(".search-mobile").click(function () {
        $(".search-area.form-search-mobile").toggle();
    });


//--------//
    $(".user-area .user-after-log").click(function () {
        $(".user-area .content-menu").toggleClass("show");
        $(".user-body-dim").toggleClass("show");
        $(".user-body-dim").css("z-index", "1000412");
        $(".user-area").css("z-index", "1000415");
        $(".form-item").css("z-index", "10");
    });

    $(".user-body-dim").click(function () {
        $(".user-area .content-menu").toggleClass("show");
        $(".user-body-dim").toggleClass("show");
    });

    $(".form-item").click(function () {
        $(".country-body-dim").toggleClass("show");
        $(".country-body-dim").css("z-index", "1000416");
        $(".form-item").css("z-index", "1000417");
        $(".user-body-dim").css("z-index", "10");
        $(".user-area").css("z-index", "10");
    });

    $(".country-body-dim").click(function () {
        $(".country-body-dim").toggleClass("show");
        
    });
//--------//

    $(".user-area").click(function () {
        $(".user-area .dropdown-content").toggle();
        $(".search-area.form-search-mobile").hide();
    });

    $(".click-to-browes").click(function () {
        $(".popup-area").hide();
    });

    $(".link-show-more").click(function () {
        $(this).parent(".filter-ocard").children(".filter-items").toggleClass("show-items");
        $(this).text($(this).text() == 'See More' ? 'See Less' : 'See More');
    });

    /*===================================================================================*/
    $('.category-big-section .loop').owlCarousel({
        center: true,
        items: 3,
        loop: true,
        margin: 10,
        autoplay: true,
        autoplayHoverPause: true,
        stagePadding: 60,
        nav: true,
        dots: false,
        responsive: {
            1000: {
                items: 3
            },
            600: {
                items: 2,
                stagePadding: 0
            },
            320: {
                items: 1,
                stagePadding: 0
            }
        }
    });


    /*===================================================================================*/
    $('.customers-slider .loop,.related-slider .loop').owlCarousel({
        items: 4,
        loop: true,
        margin: 10,
        autoplay: true,
        autoplayHoverPause: true,

        nav: true,
        dots: false,
        responsive: {
            1000: {
                items: 4,

            },
            600: {
                items: 2,

            },
            320: {
                items: 1,

            }
        }
    });

    /*===================================================================================*/
    $('.brands .owl-carousel').owlCarousel({
        loop: true,
        margin: 10,
        nav: true,
        dots: false,
        autoplay: true,
        autoplayHoverPause: true,
        responsive: {
            0: {
                items: 1
            },
            600: {
                items: 3
            },
            1000: {
                items: 5
            }
        }
    })
    /*===================================================================================*/

  //  $("#countries").msDropdown();

    /*===================================================================================*/

    /*$('#slider4').ubislider({
        arrowsToggle: true,
        type: 'ecommerce',
        hideArrows: false,
        autoSlideOnLastClick: true,
        modalOnClick: true,
        onTopImageChange: function () {
            $('#imageSlider4 img').elevateZoom({
                zoomWindowPosition: "zoom-container",
                zoomWindowHeight: 350,
                zoomWindowWidth: 350,
                borderSize: 2,
                gallery : "ubislider-inner",
                galleryActiveClass: "active"
            });
        }
    });*/

    var pageDirIsRtl = $('html').attr('dir') == 'rtl';

    console.log(pageDirIsRtl);
    $('.slider-single').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: true,
        fade: false,
        adaptiveHeight: true,
        infinite: true,
        autoplay: true,
        autoplaySpeed: 4000,
        useTransform: true,
        speed: 400,
        cssEase: 'cubic-bezier(0.77, 0, 0.18, 1)',
        rtl: pageDirIsRtl,
    });
    $('.slider-nav')
        .on('init', function (event, slick) {
            $('.slider-nav .slick-slide.slick-current').addClass('is-active');
        })
        .slick({
            slidesToShow: 7,
            slidesToScroll: 7,
            dots: false,
            focusOnSelect: false,
            infinite: false,
            responsive:
                [
                    {
                        breakpoint: 1024,
                        settings: {
                            slidesToShow: 5,
                            slidesToScroll: 5,
                        }
                    },
                    {
                        breakpoint: 640,
                        settings: {
                            slidesToShow: 4,
                            slidesToScroll: 4,
                        }
                    },
                    {
                        breakpoint: 420,
                        settings: {
                            slidesToShow: 3,
                            slidesToScroll: 3,
                        }
                    }
                ]
        });
    $('.slider-single').on('afterChange', function (event, slick, currentSlide) {
        $.removeData($(this).find('img'), 'elevateZoom');
        $('.zoomWindowContainer').remove();
        $('.zoomContainer').remove();
        $('.slider-nav').slick('slickGoTo', currentSlide);
        var currrentNavSlideElem = '.slider-nav .slick-slide[data-slick-index="' + currentSlide + '"]';
        var currrentImgae = '.slider-single .slick-slide[data-slick-index="' + currentSlide + '"]';
        $('.slider-nav .slick-slide.is-active').removeClass('is-active');
        $(currrentNavSlideElem).addClass('is-active');
        $(currrentImgae).find('img').elevateZoom({
            zoomWindowPosition: "zoom-container",
            zoomWindowHeight: 350,
            zoomWindowWidth: 350,
            borderSize: 2
        });
        console.log($(currrentImgae), 'tests');
    });
    $('.slider-nav').on('click', '.slick-slide', function (event) {
        event.preventDefault();
        var goToSingleSlide = $(this).data('slick-index');

        $('.slider-single').slick('slickGoTo', goToSingleSlide);
    });
    $('.slider-single div[data-slick-index="0"] img').elevateZoom({
        zoomWindowPosition: "zoom-container",
        zoomWindowHeight: 350,
        zoomWindowWidth: 350,
        borderSize: 2,
        gallery: "ubislider-inner",
        galleryActiveClass: "active"
    });
    /*===================================================================================*/

    $("#navigation1").navigation();
    /*===================================================================================*/
    /*	OWL CAROUSEL
    /*===================================================================================*/
    $('#hero .owl-carousel').owlCarousel({
        loop: true,
        margin: 0,
        nav: true,
        autoplay: true,
        autoplayHoverPause: true,
        responsive: {
            0: {
                items: 1
            },
            600: {
                items: 1
            },
            1000: {
                items: 1
            }
        }
    })

    $('.most-buy .owl-carousel').owlCarousel({
        loop: true,
        margin: 0,
        nav: true,
        dots: true,
        responsive: {
            0: {
                items: 1
            },
            600: {
                items: 3
            },
            1000: {
                items: 6
            }
        }
    })

    //-------------//


});


//---------------------------------//
$(function () {

    $(".numbers-row").append('<div class="inc  button ">+</div><div class="dec  button">-</div>');

    $(".button").on("click", function () {

        var $button = $(this);
        var oldValue = $button.parent().find("input").val();

        if ($button.text() == "+") {
            var newVal = parseFloat(oldValue) + 1;
        } else {
            // Don't allow decrementing below zero
            if (oldValue > 0) {
                var newVal = parseFloat(oldValue) - 1;
            } else {
                newVal = 0;
            }
        }

        $button.parent().find("input").val(newVal);

    });

});


function increaseValue() {
    var value = parseInt(document.getElementById('number').value, 10);
    value = isNaN(value) ? 0 : value;
    value++;
    document.getElementById('number').value = value;
  }
  
  function decreaseValue() {
    var value = parseInt(document.getElementById('number').value, 10);
    value = isNaN(value) ? 0 : value;
    value < 1 ? value = 1 : '';
    value--;
    document.getElementById('number').value = value;
  }


//------------------------------------------------------------------------------------------//

  var input = document.querySelector("#phone");
  window.intlTelInput(input, {
    // allowDropdown: false,
     autoHideDialCode: true,
    // autoPlaceholder: "off",
    // dropdownContainer: document.body,
    // excludeCountries: ["us"],
     formatOnDisplay: true,
    // geoIpLookup: function(callback) {
    //   $.get("http://ipinfo.io", function() {}, "jsonp").always(function(resp) {
    //     var countryCode = (resp && resp.country) ? resp.country : "";
    //     callback(countryCode);
    //   });
    // },
    // hiddenInput: "full_number",
    // initialCountry: "auto",
    // localizedCountries: { 'de': 'Deutschland' },
    // nationalMode: false,
    // onlyCountries: ['us', 'gb', 'ch', 'ca', 'do'],
    // placeholderNumberType: "MOBILE",
    // preferredCountries: ['cn', 'jp'],
    // separateDialCode: true,
    utilsScript: "un-min-js/utils.js",
  });


  
  function showvalue(arg) {
    alert(arg);
    //arg.visible(false);
  }
  