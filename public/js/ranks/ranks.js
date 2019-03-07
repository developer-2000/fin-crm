$(function () {
    $('.create_rank').on('click', createRank);
    addEditable();

});

function createRank() {
    var block = $('#form_block');
    var formData = block.find('form').serialize();
    var errorsBlock = block.find('.error-messages');
    $.post('/ajax/users/ranks/create', formData, function (json) {
        if (json.success) {
            getMessage('success', 'Ранг добавлен');
            block.find('.close').click();
            if (json.tableHtml) {
                $('.table_ranks tbody').empty();
                $('.table_ranks tbody').append(json.tableHtml);
                addEditable();
            }
            $('#form_block form')[0].reset();
        } else {
            getMessage('error', 'Произошла ошибка');
            block.find('.close').click();
        }
    }).fail(function (json) {
        getErrorMessages(json, block, errorsBlock);
    });
}

function getMessage(type, message) {
    $('.ns-box').remove();
    var notification = new NotificationFx({
        message : '<span class="icon fa fa-bullhorn fa-2x"></span><p>'+ message +'</p>',
        layout : 'bar',
        effect : 'slidetop',
        type : type,
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

function addEditable() {
    $('.delete_rank').editable({
        tpl : '',
        emptytext : '<span class="fa-stack "> ' +
        '<i class="fa fa-square fa-stack-2x"></i> ' +
        '<i class="fa fa-trash-o fa-stack-1x fa-inverse"></i> ' +
        '</span>',
        url : '/ajax/users/ranks/delete',
        ajaxOptions : {
            type : 'post',
            dataType : 'json'
        },
        success : function (data, config) {
            if (data.success) {
                var parent = $('a[data-pk="' + data.id + '"]').parents('tr');
                parent.fadeOut(600);
                setTimeout(function () {
                    parent.remove();
                }, 600);
                getMessage('success', "Успешно удален")
            } else {
                getMessage('error', "Произошла ошибка!");
            }

        },
        error : function (errors) {
            getMessage('error', "Произошла ошибка на сервере!");
        }
    });
    $('.rank_name').editable({
        emptytext : 'Не назван',
        url : '/ajax/users/ranks/update',
        ajaxOptions : {
            type : 'post',
            dataType : 'json'
        },
        validate: function (value) {
            if ($.trim(value) == '') {
                return 'Заполните поле!';
            }
        },
        success : function (data, config) {
            if (data.success) {
                getMessage('success', "Успешно изменен")
            } else {
                getMessage('error', "Произошла ошибка!");
            }

        },
        error : function (errors) {
            getMessage('error', "Произошла ошибка на сервере!");
        }
    });
    $('.rank_role').editable({
        type : 'select',
        source : '/roles/all',
        emptytext : 'Нет роли',
        url : '/ajax/users/ranks/update',
        ajaxOptions : {
            type : 'post',
            dataType : 'json'
        },
        validate: function (value) {
            if ($.trim(value) == '') {
                return 'Заполните поле!';
            }
        },
        success : function (data, config) {
            if (data.success) {
                getMessage('success', "Успешно изменен")
            } else {
                getMessage('error', "Произошла ошибка!");
            }

        },
        error : function (errors) {
            getMessage('error', "Произошла ошибка на сервере!");
        }
    });
}