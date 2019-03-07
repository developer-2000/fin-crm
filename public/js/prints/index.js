$('button.sent').click(function () {
    var currentButton = $(this);
    var tbody = $(this).parent().parent().parent().closest('tbody');
    var orders = [];
    tbody.children(' tr.order').each(function (e, value) {
        orders.push($(value).attr('data-order'));
    });
    $.post('/ajax/run-action', {
        action: $(this).attr('id'),
        orders: orders,
        status: Number($(this).attr('data-proc-status')),
    }, function (data) {
        if (data.success) {
            var successOrders = [];
            var failedOrders = [];

            $.each(data.orders, function (e, val) {
                if (val.success) {
                    var tr = $.find('[data-order = "' + e + '"]');
                    $(tr).fadeOut(400);
                    successOrders.push(e);
                } else {
                    failedOrders.push(e);
                }
            });

            setTimeout(function () {

                $.each(successOrders, function (e, val) {
                    var tr = $.find('[data-order = "' + val + '"]');
                    $(tr).remove();
                });
                if (failedOrders.length > 0) {
                    var warnMessage = "<br>" + "Статус заказов " + failedOrders + "  измененить не удалось!"
                }

                if (!failedOrders.length) {
                    if ($(currentButton).parents('tbody').children('tr.order').length == 0) {
                        var block = $(currentButton).closest('div.post-block');
                        $(block).fadeOut(400);
                        setTimeout(function () {
                            $(block).remove();
                        }, 400);
                    }
                    getMessage('success', "Статус заказов " + successOrders + " успешно изменен!");
                } else {
                    getMessage('error', warnMessage);
                }
            }, 400);

        } else {
            getMessage('error', "Произошла ошибка, обратитесь в службу поддержки!");
        }
    }).fail(function (data) {
        var errors = [];
        $.each(JSON.parse(data.responseText), function (e, obj) {
            errors.push(obj + '<br>');
        });
        getMessage('error', errors);
    });
});
$('a.sent').each(function (e, val) {
    $(val).click(function () {
        event.preventDefault();
        var order = [$(this).attr('data-order-id')];
        $.post('/ajax/run-action', {
            action: $(this).attr('id'),
            orders: order,
            status: Number($(this).attr('data-proc-status')),
        }, function (data) {
            if (data.success) {
                var successOrders = [];
                var failedOrders = [];

                $.each(data.orders, function (e, val) {
                    if (val.success) {
                        var tr = $.find('[data-order = "' + e + '"]');
                        $(tr).fadeOut(400);
                        setTimeout(function () {
                            $(tr).remove();
                        }, 400);
                        successOrders.push(e);
                    } else {
                        failedOrders.push(e);
                    }
                });
                if (failedOrders.length > 0) {
                    var warnMessage = "<br>" + "Статус заказа " + failedOrders + "  измененить не удалось!"
                }
                if (!failedOrders.length) {
                    getMessage('success', "Статус заказа " + successOrders + " успешно изменен!");
                } else {
                    getMessage('error', warnMessage);
                }
            } else {
                getMessage('error', "Произошла ошибка, обратитесь в службу поддержки!");
            }
        }).fail(function (data) {
            var errors = [];
            $.each(JSON.parse(data.responseText), function (e, obj) {
                errors.push(obj + '<br>');
            });
            getMessage('error', errors);
        });

    });
});


$('a.change_all_orders_statuses').click(function () {
    event.preventDefault();
    var ordersIds = $('#ordersIds').val();
    var procStatus = $(this).attr('data-proc-status');
    getMessage('wait', 'Обрабатывется');
    $.post('/ajax/order-change-proc-status', {
            orders_ids: ordersIds,
            proc_status: procStatus
        }, function (json) {
            if (json.success) {
                getMessage('success', 'Статус заказов успешно изменен!');
                $.each(JSON.parse(ordersIds), function (e, val) {
                    var tr = $.find("[data-order = " + val + "]");
                    $(tr).children('.proc_status_name').text(json.procStatusName).css('color', 'green');
                })

            } else if (json.orders_updated) {
                getMessage('warning', 'Статус заказов уже изменен!');
            } else {
                getMessage('error', 'Ошибка');
            }
        }
    )
});


/**
 * Выводим сообщение
 */
function getMessage(type, message) {
    $('.ns-box').remove();
    if (type === 'wait') {
        var notification = new NotificationFx({
            message: '<span class="fa fa-spinner fa-2x alert_spinner"></span><p>' + message + '</p>',
            layout: 'bar',
            effect: 'slidetop',
            type: 'notice',
            ttl: 60000,
        });
    } else {
        var notification = new NotificationFx({
            message: '<span class="icon fa fa-bullhorn fa-2x"></span><p>' + message + '</p>',
            layout: 'bar',
            effect: 'slidetop',
            type: type,
            ttl: 3000,
        });
    }
    notification.show();
}