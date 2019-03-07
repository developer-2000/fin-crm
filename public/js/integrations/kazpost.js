$(function () {
    $('#add_senders').on('submit', addKazSenders);
    $('#edit_counterparty').on('submit', editKazSenders);

    $('#project_id').select2({
        minimumInputLength: 1,
        ajax: {
            url: '/projects/find',
            dataType: 'json',
            data: function (params) {
                return {
                    query: $.trim(params),
                };
            },
            results: function (data) {
                return {
                    results: data,
                };
            }
        },
        width : '100%'
    }).on('change', function () {
        $('#sub_project_id').select2('data', {id : null, text: ''})
    });
    $('#sub_project_id').select2({
        minimumInputLength: 1,
        ajax: {
            url: '/sub_projects/find',
            dataType: 'json',
            data: function (params) {
                return {
                    query: $.trim(params),
                    project_id : $('#project_id').val(),
                };
            },
            results: function (data) {
                return {
                    results: data,
                };
            }
        },
        width : '100%'
    });
    if ($('#project_id').attr('data-content')) {
        $('#project_id').select2('data', JSON.parse($('#project_id').attr('data-content')));
    }
    if ($('#sub_project_id').attr('data-content')) {
        $('#sub_project_id').select2('data', JSON.parse($('#sub_project_id').attr('data-content')));
    }
});

function addKazSenders() {
    disableButton($('#form_block').find('[type="submit"]'), true);
    var block = $('#form_block');
    var errorsBlock = block.find('.error-messages');
    $.post('/ajax/integrations/kazpost/add-sender', $(this).serialize(), function (json) {
        if (json.success) {
            getMessage('success', 'Отправитель добавлен');
            block.find('.close').click();
            if (json.html) {
                $('#block_senders tbody').empty();
                $('#block_senders tbody').append(json.html);
            }
            $('#form_block form')[0].reset();
        } else {
            getMessage('error', 'Произошла ошибка');
            block.find('.close').click();
        }
        disableButton($('#form_block').find('[type="submit"]'));
    }).fail(function (json) {
        getErrorMessages(json, block, errorsBlock);
        disableButton($('#form_block').find('[type="submit"]'));
    });
    return false;
}

function editKazSenders() {
    var form = $(this);
    var errorsBlock = form.find('.error-messages');
    disableButton(form.find('[type="submit"]'), true);
    $.post('/ajax/integrations/kazpost/edit-sender/' + $('#sender_id').val(), form.serialize(), function (json) {
        if (json.success) {
            getMessage('success', 'Изменения добавлены');
        } else {
            getMessage('error', 'Произошла ошибка');
        }
        disableButton(form.find('[type="submit"]'));
    }).fail(function (json) {
        getErrorMessages(json, form, errorsBlock);
        disableButton(form.find('[type="submit"]'));
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