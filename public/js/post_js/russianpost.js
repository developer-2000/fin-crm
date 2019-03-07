$(function () {
    $('.create_blanks').on('click',createBlank);
    $('#sender').on('change', function () {
        var value = $(this).val();
        $('.blanks').each(function () {
            var link = $(this).attr('href').split('/');
            link[link.length - 1] = value || 0;
            $(this).attr('href', link.join('/'));
        })
    })
});

function processResponse(json) {
    if (json.integration && json.integration.success && json.integration.orderId) {
        var cl = 'alert-success';
        var icon = 'check';
        var link = window.location.origin + '/integrations/russianpost/sticker2/' + json.integration.orderId;
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
        getSucMess(json, btn.text(), btn.attr('data-content'));

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
        getErrorMessageRussianpost(json, btn.text(), btn.attr('data-content'));
        disableButton(btn);
    });
}


function getSucMess(json, blank, url) {
    if (json.integration && json.integration.success && json.integration.orderId) {
        var cl = 'alert-success';
        var icon = 'check';
        var link = window.location.origin + '/integrations/russianpost/' + url + '/' + json.integration.orderId;
        var text = '<a href="' + link+ '" target="_blank">' + blank + '</a>';
        window.open(link);
    } else {
        var cl = 'alert-danger';
        var icon = 'times';
        var text = 'Произошла ошибка, ' + blank + ' не создан';
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
                }else if (block == 'existInPass') {
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


function getErrorMessageRussianpost(json, blankName, blankId) {
    try {
        var response = JSON.parse(json.responseText) ? JSON.parse(json.responseText) : json;
        getSucMess(response, blankName, blankId);
        if (response.errors) {
            var messages = '';
            $.each(response.errors, function (name, value) {
                var message = '';
                var fieldName = deleteDote(name);
                if (fieldName) {
                    $.each(value, function (key, error) {
                        var obj = document.getElementsByName(fieldName);
                        var parent = $(obj).parents('.form-group');
                        var label = $('label[for="' + fieldName + '"]').text();

                        if (!label.length) {
                            label = parent.find('label').text();
                        }
                        if (!label.length) {
                            label = name;
                        }

                        parent.find('.help-block').remove();
                        parent.addClass('has-error');
                        if (name != 'name') {
                            message = error.replace(name, '<strong>"' + label + '"</strong>');
                        } else {
                            message = error.replace('Название', '<strong>"' + label + '"</strong>');
                        }
                        if (message.length) {
                            messages += '<div class="alert alert-danger fade in"> ' +
                                '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> ' +
                                '<i class="fa fa-times-circle fa-fw fa-lg"></i> ' + message +
                                '</div>';
                        }
                    });
                }
            });
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
            $('.ns-close').click();

            $('.alert .close').on('click', setStyleErrorBlock);
        } else {
            getMessage('error', 'Произошла ошибка');
        }
    } catch (e) {
        getMessage('error', 'Произошла ошибка');
    }
}