$('button.create-role-form').on('click', function () {
    var block = $('#form_block');
    var formData = $('form#create-role').serialize();
    console.log(formData);

    $.post('/ajax/users/roles/create', formData, function (json) {
        if (json.success) {
            getMessage('success', 'Роль добавлена');
            block.find('.close').click();
            if (json.tableHtml) {
                $('.table_roles tbody').empty();
                $('.table_roles tbody').append(json.tableHtml);
            }
            $('#create-role')[0].reset();
        } else {
            getMessage('error', 'Произошла ошибка');
            block.find('.close').click();
        }
    })
        .fail(function (json) {
            getErrorMessages(json, block, errorsBlock);
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