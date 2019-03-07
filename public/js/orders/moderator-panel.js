$('#addCall').on('click', function () {
    event.preventDefault();
    if ($('#add_call').is(":checked")) {
        var data = $('#add_call').val();
        var orderId = $('span.order_id').text();
        getMessage('wait', 'Обрабатывется');
        $.ajax({
            type: "POST",
            url: '/ajax/save-moderator-changes',
            data: {action: data, orderId: orderId},
            success: function (data) {

                if (data.call_added) {
                    getMessage('success', 'Заказ добавлен на прозвон');
                    $('div.upload-cancel').empty();
                    $('div.upload-cancel').append(data.html);

                }
                else if (data.callStopped) {
                    getMessage('warning', 'Прозвон по заказу приостановлен');
                    $('div.upload-cancel').empty();
                    $('div.upload-cancel').append(data.html);
                    $('.moderator_block_status').text('Приостановлен');
                }
                else {
                    getMessage('error', 'Ошибка');
                }
            }
        });
    }
});

$('#change_campaign').on('click', function () {
    event.preventDefault();
    var campaign = $('#campaign').val();
    var orderId = $('span.order_id').text();
    getMessage('wait', 'Обрабатывется');
    $.post('/ajax/save-moderator-changes', {campaign: campaign, orderId: orderId}, function (json) {
        if (json.html) {
            $('div.operators_block').empty();
            $('div.operators_block').append(json.html);

        }
        if (json.campaign_changed && !json.status.pbx_status) {
            getMessage('success', 'Очередь успешно изменена');
        }
        else if (json.campaign_changed && json.status.pbx_status.status == 200) {
            getMessage('success', 'Очередь успешно изменена');
        } else {
            getMessage('error', 'Ошибка');
        }
    });
});

$('#campaign').on('change', function () {
    var campaignId = $('#campaign').val();
    $.post('/ajax/change-operators-options', {campaignId: campaignId}, function (json) {
        if (json.html) {
            $('div.operators_block').empty();
            $('div.operators_block').append(json.html);
        }
    });
});


$('#change_priority').on('click', function () {
    event.preventDefault();
    var priority = $('#priority').val();
    var orderId = $('span.order_id').text();
    getMessage('wait', 'Обрабатывется');
    $.post('/ajax/save-moderator-changes', {priority: priority, orderId: orderId}, function (json) {
        if (json.changed_priority) {
            getMessage('success', 'Приоритет успешно изменен');
        }
        else {
            getMessage('error', 'Ошибка');
        }
    });
});

$('#change_stage').on('click', function () {
    event.preventDefault();
    var stage = $('.simple-slider').attr('title');
    var orderId = $('span.order_id').text();
    getMessage('wait', 'Обрабатывется');
    $.post('/ajax/save-moderator-changes', {stage: stage, orderId: orderId}, function (json) {
        if (json.changed_stage) {
            getMessage('success', 'Этап прозвона успешно изменен');
        }
        else {
            getMessage('error', 'Ошибка');
        }
    });
});

$(function () {
    $('.add_call_now').on('change', function () {
        if ($('#add_call_now').is(":checked")) {
            $('#callback_date_moderator').attr('disabled', true);
        }
        else {
            $('#callback_date_moderator').attr('disabled', false);
        }
    });
});


$('#set_call_back_operator').on('click', function () {
    event.preventDefault();
    var operator = $('#operator').val();
    if ($('#add_call_now').is(":checked")) {
        var add_call_now = Date.now();
    }
    var callback_date = $('#callback_date_moderator').val();
    var orderId = $('span.order_id').text();
    getMessage('wait', 'Обрабатывется');
    $.post('/ajax/save-moderator-changes', {
        callback_date: callback_date, add_call_now: add_call_now,
        orderId: orderId, operator: operator
    }, function (json) {
        if (json.status.call_added_now) {
            if (json.status.html) {
                $('div.upload-cancel').empty();
                $('div.upload-cancel').append(json.status.html);
            }
            getMessage('success', 'Заказ успешно добавлен на прозвон');
        }
        if (json.status.call_back_time_changed) {
            getMessage('success', 'Время прозвона успешно изменено');
        }
        if (json.status.operator_changed) {
            getMessage('success', 'Оператор успешно изменен');
        }
        if (json.status.operator_callback_changed) {
            if (json.html) {
                $('div.upload-cancel').empty();
                $('div.upload-cancel').append(json.html);
                $('.moderator_block_status').text('В наборе');
            }
            getMessage('success', 'Заказ успешно загружен в прозвон');
        }
        if (json.status.order_not_in_processing) {
            getMessage('warning', 'Заказ не находится на стадии прозвона');
        }
    });
});
if ($('#order').hasClass('active')) {
    $('li.logs').removeClass('active');
    $('li.order').addClass('active').prop('aria-expended', true);
} else {
    $('li.order').removeClass('active');
    $('li.logs').addClass('active').prop('aria-expended', true);
    ;
}
if ($('#logs').hasClass('active')) {
    $('li.order').removeClass('active');
    $('li.logs').addClass('active').prop('aria-expended', true);
} else {
    $('li.order').addClass('active').prop('aria-expended', true);
    $('li.logs').removeClass('active');
}

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