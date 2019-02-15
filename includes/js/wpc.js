var $ = jQuery;
jQuery(document).ready(function() {


    // Uploading field
    jQuery('body').on('click', '.wpc-add-image-button', function (e) {
        e.preventDefault();

        var image = wp.media({
            title: 'Select Product Images',
            // mutiple: true if you want to upload multiple files at once
            multiple: false
        }).open()
            .on('select', function(e){
                var uploaded_image = image.state().get('selection').first();

                var image_url = uploaded_image.toJSON().url;
                var html = "<div class=\"wpc-product\">\n" +
                    "  \t\t\t\t\t<img src=\""+image_url+"\"  alt=\"Preview\">\n" +
                    "  \t\t\t\t\t<input type=\"hidden\" name=\"wpc_product_imgs[]\" value=\""+image_url+"\">\n" +
                    "  \t\t\t\t\t<a class=\"remove-image remove-product-img\" href=\"#\" style=\"display:" +
                    " inline;\">&#215;</a></div>";
                $('.wpc-add-image-button').before(html);
            });

    });

    //Remove Product Image
    $('body').on('click', '.remove-product-img', function (e) {
        e.preventDefault();
        console.log($(this).parent().remove());
    });

    // Create dynamic field
    jQuery('body').on('click', '.base-add-fld-btn', function (e) {
        e.preventDefault();

        var parent = jQuery(this).parents('tr');
        var clone = parent.find('.cloner').clone();
        var fldID = jQuery(this).data('fldid');

        clone.removeClass('cloner');

        clone.find('.base-x-small-text').attr('name', fldID + '[]');

        parent.find('.dynamic-field-wrapp').append(clone);
    });


    // Del dynamic field
    jQuery('body').on('click', '.del-dynamic-field', function (e) {
        e.preventDefault();
        jQuery(this).parent().remove();
    });

});