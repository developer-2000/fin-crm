$(function () {
    $('#create_blank').on('click',createBlank);
});

function processResponse(json) {
    if (json.integration && json.integration.success && json.integration.orderId) {
        var cl = 'alert-success';
        var icon = 'check';
        var link = window.location.origin + '/integrations/kazpost/sticker2/' + json.integration.orderId;
        var text = '<a href="' + link+ '" target="_blank">Sticker2</a>'
        window.open(link);
    } else {
        var cl = 'alert-danger';
        var icon = 'times';
        var text = 'Произошла ошибка, Sticker 2 не создан';
        getErrorMessage(json);
    }
    var message = '<div class="alert ' + cl + ' fade in"> ' +
    '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> ' +
    '<i class="fa fa-' + icon + '-circle fa-fw fa-lg"></i> ' + text +
    '</div>';
    if (!window.checkMessage) {
        $('.error-messages').empty();
        $('.error-messages').append(message);
        $('#order_data .error-messages').slideDown();
        window.checkMessage = true;
    } else {
        $('.error-messages').append(message);
    }
}

function createBlank() {
    var btn = $(this);
    var deliveryData = $('#order_data').serialize();
    window.checkMessage = false;
    disableButton(btn, true);
    getMessage('wait', 'Обработка');
    $('#order_data .has-error').removeClass('has-error');

    $.post('/ajax/delivery-note-create/', deliveryData, function (json) {
        getSucMess(json);

        $('.alert .close').on('click', setStyleErrorBlock);
        if (json.integration.success) {
            getMessage('success', 'Экспресс накладная успешно создана!');
            $('.sent-to-print').removeClass('hidden').removeClass('disabled');
        } else if (json.errors) {
            getMessage('error', json.errors);
        } else if (!json.integration.success) {
            getMessage('error', 'Экспресс накладная не создана!');
        }
        disableButton(btn);
    }).fail(function (json) {
        getErrorMessage(json);
        disableButton(btn);
    });
}

function getSucMess(json) {
    if (json.integration && json.integration.success && json.integration.orderId) {
        var cl = 'alert-success';
        var icon = 'check';
        var link = window.location.origin + '/integrations/kazpost/blank/' + json.integration.orderId;
        var text = '<a href="' + link+ '" target="_blank">Blank</a>'
        window.open(link);
    } else {
        var cl = 'alert-danger';
        var icon = 'times';
        var text = 'Произошла ошибка, Blank не создан';
        getErrorMessage(json);
    }
    var message = '<div class="alert ' + cl + ' fade in"> ' +
        '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> ' +
        '<i class="fa fa-' + icon + '-circle fa-fw fa-lg"></i> ' + text +
        '</div>';
    if (!window.checkMessage) {
        $('.error-messages').empty();
        $('.error-messages').append(message);
        $('#order_data .error-messages').slideDown();
        window.checkMessage = true;
    } else {
        $('.error-messages').append(message);
    }

    var messages = '';
    if (json.success) {
        $.each(json.success, function (block, value) {
            var message = '';
            if (value) {
                var cl = 'alert-success';
                var icon = 'check';
                if (block == 'contactData') {
                    message = 'Данные пользователя сохранены успешно';
                } else if (block == 'target') {
                    message = 'Цель сохранена успешно';
                } else if (block == 'suspicious') {
                    message = 'Заказ отмечен как "Подозрительный"';
                } else if (block == 'callback') {
                    message = 'Результат звонка установлен';
                } else if (block == 'products') {
                    message = 'Товары сохранены';
                }
            } else {
                var cl = 'alert-danger';
                var icon = 'times';
                if (block == 'contactData') {
                    message = 'Данные пользователя не сохранены';
                } else if (block == 'target') {
                    message = 'Цель не сохранена';
                } else if (block == 'suspicious') {
                    message = 'Заказ не отмечен как "Подозрительный"';
                } else if (block == 'callback') {
                    message = 'Результат звонка не установлен';
                } else if (block == 'products') {
                    message = 'Товары не сохранены';
                } else if (block == 'existInPass') {
                    message = 'Заказ уже добавлен в проводку или В очередь на печать ';
                }
            }
            if (message.length) {
                messages += '<div class="alert ' + cl + ' fade in"> ' +
                    '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> ' +
                    '<i class="fa fa-' + icon + '-circle fa-fw fa-lg"></i> ' + message +
                    '</div>';
            }
        });
        $('#order_data').find('has-error').removeClass('has-error');
        $('.ns-close').click();
    } else {
        getMessage('error', 'Произошла ошибка')
    }
    if (messages.length) {
        if (!window.checkMessage) {
            $('.error-messages').empty();
            $('.error-messages').append(messages);
            $('#order_data .error-messages').slideDown();
            window.checkMessage = true;
        } else {
            $('.error-messages').append(messages);
        }
    }
}