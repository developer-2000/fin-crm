$('a.print').click(function () {
    var orderId = $(this).attr('data-order-id');
    var link = window.location.origin + '/integrations/russianpost/sticker2/' + orderId;
    window.open(link);
});

