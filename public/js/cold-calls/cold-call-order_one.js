$(function() {
    $('#offer').on('change', function() {
        var offerId = $('#offer').val();
        $.post('/ajax/change-recommended-products/', { offerId: offerId, orderId:orderId}, function(json) {
            if (json) {
            $('.recommended-product').empty();
            $('.recommended-product').append(json.html);
            $(' .add_product').on('click', addNewProduct);
            }
        }, 'json');
    });
});


