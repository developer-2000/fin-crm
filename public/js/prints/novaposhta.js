$('a.proc_status').click(function () {
    var orderId = $(this).attr('data-id');
    var procStatus = $(this).attr('data-proc-status');
    getMessage('wait', 'Обрабатывется');
    $.post('/ajax/order-change-proc-status', {
            order_id: orderId,
            proc_status: procStatus
        }, function (json) {
            if (json.success) {
                getMessage('success', 'Статус заказа №' + orderId + ' успешно изменен!');
                var tr = $.find("[data-order = " + orderId + "]");
                $(tr).children('.proc_status_name').text(json.procStatusName).css('color', 'green');
            } else {
                getMessage('error', 'Ошибка');
            }
        }
    )
});

