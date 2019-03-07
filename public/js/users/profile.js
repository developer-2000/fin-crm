$(function () {
    /**
     * Календарик
     */
    myDatepicker($('#date_start'));
    myDatepicker($('#date_end'));

    $('#time_zone').select2({
        width : '100%'
    });

    $('#user_settings').on('submit', submitSettings);
});

/**
 * Календарик
 */
function myDatepicker(obj) {
    var start = new Date();
    obj.datepicker({
        language: 'ru',
        startDate: start,
        onSelect: function (fd, d, picker) {
            if (!d) {
                return;
            }
        }
    });
}

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

function submitSettings() {
    getMessage('wait', 'Обработка');
    var form = $(this);
    var errorsBlock = form.find('.error_messages');
    disableButton(form.find('[type="submit"]'), true);

    $.post('/ajax/user/settings', form.serialize(), function (json) {
        if (json.success) {
            getMessage('success', 'Данные сохранены');
        } else {
            getMessage('error', 'Произошла ошибка');
        }
        disableButton(form.find('[type="submit"]'));
    }).fail(function (json) {
        getErrorMessages(json, form, errorsBlock);
        disableButton(form.find('[type="submit"]'));
        $('.ns-close').click();
    });

    return false;
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
                        fieldName = fieldName.replace('_', ' ');
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
        }
    } catch (e) {
        getMessage('error', 'На сервере произошла ошибка');
    }
}