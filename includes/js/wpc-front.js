
var $ = jQuery;
jQuery(document).ready(function(){
    var $ = jQuery;

    $('.slider-single').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: false,
        fade: false,
        adaptiveHeight: true,
        infinite: true,
        useTransform: true,
        speed: 400,
        cssEase: 'cubic-bezier(0.77, 0, 0.18, 1)',
        responsive: [
            {
                breakpoint: 680,
                settings: {
                    arrows: true,
                }
            },
            {
                breakpoint: 580,
                settings: {
                    arrows: true,
                }
            }
        ]
    });

    $('.slider-nav')
        .on('init', function(event, slick) {
            $('.slider-nav .slick-slide.slick-current').addClass('is-active');
        })
        .slick({
            vertical: true,
            slidesToShow: 3,
            slidesToScroll: 1,
            dots: false,
            arrows: true,
            centerMode: true,
            focusOnSelect: false,
            infinite: true,
            responsive: [{
                breakpoint: 960,
                settings: {
                    vertical: false,
                    slidesToShow: 3,
                    slidesToScroll: 1,
                    // centerMode: true,
                }
            }, {
                breakpoint: 640,
                settings: {
                    vertical: false,
                    slidesToShow: 3,
                    slidesToScroll: 1,
                }
            }, {
                breakpoint: 420,
                settings: {
                    vertical: false,
                    slidesToShow: 2,
                    slidesToScroll: 1,
                }
            }]
        });

    $('.slider-single').on('afterChange', function(event, slick, currentSlide) {
        $('.slider-nav').slick('slickGoTo', currentSlide);
        var currrentNavSlideElem = '.slider-nav .slick-slide[data-slick-index="' + currentSlide + '"]';
        $('.slider-nav .slick-slide.is-active').removeClass('is-active');
        $(currrentNavSlideElem).addClass('is-active');
    });

    $('.slider-nav').on('click', '.slick-slide', function(event) {
        event.preventDefault();
        var goToSingleSlide = $(this).data('slick-index');

        $('.slider-single').slick('slickGoTo', goToSingleSlide);
    });

    // Accordion Nav
    jQuery('.wpc-accordion').navAccordion({
            expandButtonText: '<i class="fa fa-plus"></i>',
            collapseButtonText: '<i class="fa fa-minus"></i>'
        },
        function () {
            console.log('Callback')
        });

});




