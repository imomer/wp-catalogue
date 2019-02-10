jQuery(document).ready(function () {


    jQuery(".new-prdct-img img").click(function () {
        var cID = jQuery(this).attr('id');
        jQuery('.product-img-view img').hide();
        jQuery('.product-img-view img#' + cID).fadeIn(500);

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




