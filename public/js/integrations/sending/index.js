/*if($('#track2').val()){
    $('#delivery_note_create').prop('disabled', true);todo перенести
    $('#delivery_note_delete').prop('disabled', false);
    $('#delivery_note_edit').prop('disabled', false);
}else {
    $('#delivery_note_create').prop('disabled', false);
    $('#delivery_note_delete').prop('disabled', true);
    $('#delivery_note_edit').prop('disabled', true);
}*/

$('button#delivery_note_edit').on('click', function () {
    event.preventDefault();
    var formData = $('form#order_data').serialize();


    $.post('/ajax/orders/' + window.orderId + '/save-order-sending-data', formData  + '&' + $.param({
        target_approve: $('#integration').val()
    }), function (json) {
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
            $('.error-messages').empty();
            $('.error-messages').append(messages);
            $('#order_data .error-messages').slideDown();

        }

        $('.alert .close').on('click', setStyleErrorBlock);

    }).fail(function (json) {
        try {
            var response = JSON.parse(json.responseText);
            if (response.errors) {
                var messages = '';
                $.each(response.errors, function (name, value) {
                    var message = '';
                    var fieldName = deleteDote(name);
                    if (fieldName) {
                        $.each(value, function (key, error) {
                            var obj = document.getElementById(fieldName);
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
                    $('.error-messages').empty();
                    $('.error-messages').append(messages);
                    $('#order_data .error-messages').slideDown();
                }
                $('.ns-close').click();

                $('.alert .close').on('click', setStyleErrorBlock);
            } else {
                getMessage('error', 'Произошла ошибка');
            }
        } catch (e) {
            getMessage('error', 'Произошла ошибка');
        }
    });



    var delivery_note_ref = $('#delivery_note_ref').val();
    $.post('/ajax/integrations/novaposhta/delivery-note/update', formData + '&' + $.param({
        delivery_note_ref: delivery_note_ref
    }), function (json) {
        if (json.success) {
            getMessage('success', 'Экспрес накладная успешно отредактирована!');
            if (json.tableHtml) {
                $('.integrations_table tbody').empty();
                $('.integrations_table tbody').append(json.tableHtml);
            }
        } else if (json.errors) {
            getMessage('error', json.errors);
        }
    })
        .fail(function (json) {
            var errors = [];
            $.each(JSON.parse(json.responseText).errors, function (e, obj) {
                errors.push(obj + '<br>');
            });
            getMessage('error', errors);
        });
});

$('button#delivery_note_delete').on('click', function () {
    event.preventDefault();
    var formData = $('form#order_data').serialize();
    $.post('/ajax/integrations/novaposhta/delivery-note/delete', formData, function (json) {
        if (json.success) {
            getMessage('success', 'Экспрес накладная успешно удалена!');
            $(' button#delivery_note_create').prop('disabled', false);
            $(' button#delivery_note_edit').prop('disabled', true);
            $(' button#delivery_note_delete').prop('disabled', true);
        } else if (json.errors) {
            getMessage('error', json.errors);
        }
    })
        .fail(function (json) {
        });
});


function getMessage(type, message) {
    $('.ns-box').remove();
    var notification = new NotificationFx({
        message: '<span class="icon fa fa-bullhorn fa-2x"></span><p>' + message + '</p>',
        layout: 'bar',
        effect: 'slidetop',
        type: type,
        ttl: 3000,
    });
    notification.show();
}

function getErrorMessages(json, block, errorsBlock) {
    try {
        var response = JSON.parse(json.responseText);
        if (response.errors) {
            var messages = '';
            $.each(response.errors, function (fieldName, value) {
                var message = '';
                if (fieldName) {
                    $.each(value, function (key, error) {
                        var obj = block.find('[name="' + fieldName + '"]');
                        var parent = $(obj).parents('.form-group');
                        var label = $(block).find('label[for="' + fieldName + '"]').text();
                        if (!label.length) {
                            label = parent.find('label').text();
                        }
                        parent.find('.help-block').remove();
                        parent.addClass('has-error');
                        if (fieldName != 'name') {
                            message = error.replace(fieldName, '<strong>"' + label + '"</strong>');
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
                errorsBlock.empty();
                errorsBlock.append(messages);
            }
        } else {
            getMessage('error', 'Произошла ошибка');
            block.find('.close').click();
        }
    } catch (e) {
        getMessage('error', 'На сервере произошла ошибка');
        block.find('.close').click();
    }
}