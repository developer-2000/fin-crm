$('a.print').click(function () {
    var orderId = $(this).attr('data-order-id');
    console.log(orderId);
  //  if (json.integration && json.integration.success && json.integration.orderId) {
        var cl = 'alert-success';
        var icon = 'check';
        var link = window.location.origin + '/integrations/kazpost/sticker2/' + orderId;
        var text = '<a href="' + link+ '" target="_blank">Sticker2</a>'
        window.open(link);
  //  }
    // else {
    //     var cl = 'alert-danger';
    //     var icon = 'times';
    //     var text = 'Произошла ошибка, Sticker 2 не создан';
    //     getErrorMessage(json);
    // }
});

