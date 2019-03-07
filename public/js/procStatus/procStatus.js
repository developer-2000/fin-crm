$(function () {
    $('#create_status').on('click', createStatus);
    addEditable();
    $('#add_sub_status').on('click', addSubStatus);
    $('#statuses').on('change', '.change_color', changeColor);
    $('button[data-modal="rewrite_status"]').on('click', getOrderByStatus);
    $('#rewrite_status').on('click', '#rewrite_btn', rewriteStatus);
});

function createStatus() {
    var block = $('#form_block');
    var formData = block.find('form').serialize();
    var errorsBlock = block.find('.error-messages');
    $.post('/ajax/statuses/create', formData, function (json) {
        if (json.success) {
            getMessage('success', 'Статус добавлен');
            block.find('.close').click();
            if (json.html) {
                $('#statuses tbody').empty();
                $('#statuses tbody').append(json.html);
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

function changeColor() {
    var data = {
        pk : $(this).attr('data-pk'),
        name : $(this).attr('name'),
        value : $(this).val(),
    };

    $.post('/ajax/statuses/edit', data, function (json) {
        if (json.success) {
            getMessage('success', "Успешно изменен")
        } else {
            var text = json.message ? json.message : '';
            getMessage('error', "Произошла ошибка!" + text);
        }
    }).fail(function (errors) {
        getMessage('error', "Произошла ошибка на сервере!");
    });
}

function addEditable() {
    $('.edit_status').editable({
        emptytext : 'Не назван',
        url : '/ajax/statuses/edit',
        ajaxOptions : {
            type : 'post',
            dataType : 'json'
        },
        source: '/projects/find',
        select2: {
            width: 200,
            placeholder: 'Select country',
            allowClear: true,
            ajax: {
                url: '/projects/find',
                dataType: "json",
                type: 'GET',
            }
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
                var text = data.message ? data.message : '';
                getMessage('error', "Произошла ошибка!" + text);
            }

        },
        error : function (errors) {
            getMessage('error', "Произошла ошибка на сервере!");
        }
    });

    $('.delete_status').editable({
        tpl : '',
        emptytext : '<span class="fa-stack "> ' +
        '<i class="fa fa-square fa-stack-2x"></i> ' +
        '<i class="fa fa-trash-o fa-stack-1x fa-inverse"></i> ' +
        '</span>',
        url : '/ajax/statuses/delete',
        ajaxOptions : {
            type : 'post',
            dataType : 'json'
        },
        success : function (data, config) {
            if (data.success) {
                var parent = $(this).parents('tr'),
                    subStatuses = parent.find('.sub-statuses div'),
                    obj = parent;

                if (subStatuses.length && $(this).hasClass('del_sub_status')) {
                    obj = $(this).parent('div');
                }

                removeObject(obj);

                getMessage('success', "Успешно изменен")
            } else {
                getMessage('error', data.message);
            }

        },
        error : function (errors) {
            getMessage('error', "Произошла ошибка на сервере!");
        }
    });

    $('.add_sub_status').editable({
        emptytext : '<span class="fa-stack "> ' +
        '<i class="fa fa-square fa-stack-2x"></i> ' +
        '<i class="fa fa-plus fa-stack-1x fa-inverse"></i> ' +
        '</span>',
        url : '/ajax/statuses/add-sub-status',
        ajaxOptions : {
            type : 'post',
            dataType : 'json'
        },
        success : function (data, config) {
            if (data.success) {
                if (data.html) {
                    $('#statuses tbody').empty();
                    $('#statuses tbody').append(data.html);
                    addEditable();
                }

                getMessage('success', "Успешно изменен")
            } else {
                getMessage('error', data.message);
            }

        },
        error : function (errors) {
            getMessage('error', "Произошла ошибка на сервере!");
        }
    });
}

function addSubStatus() {
    var newInput = $('.hidden .sub-status').clone();

    $('#sub-statuses').append(newInput.fadeIn(500));

    $('.delete_sub_status').on('click', deleteSubStatus);

    return false;
}

function deleteSubStatus(e) {
    var parent = $(this).parents('.sub-status');

    removeObject(parent);

    return false;
}

function removeObject(obj) {
    obj.fadeOut(500);
    setTimeout(function () {
        obj.remove();
    }, 500);
}

function getOrderByStatus() {
    var block = $('#rewrite_form');
    $('#spinner').fadeIn(0);
    $('#rewrite_status .error-messages').empty();
    block.empty();
    $.post('/ajax/statuses/get-order-by-status', function (json) {
        block.empty();
        $('#spinner').fadeOut(0);
        if (json.view && json.view.length) {
            block.append(json.view);
        } else {
            block.append('Произошла ошибка');
        }
    });
}

function rewriteStatus() {
    var block = $('#rewrite_status');
    var formData = block.find('form').serialize();
    var errorsBlock = block.find('.error-messages');
    $.post('/ajax/rewrite-statuses', formData, function (json) {
        $('.ns-close').click();
        if (json) {
            var messages = '';
            $.each(json, function (id, value) {
                var message = value.name || '';
                var cl = 'alert-danger';
                var icon = 'times';
                if (value.result == true) {
                    cl = 'alert-success';
                    icon = 'check';
                }

                messages += '<div class="alert ' + cl + ' fade in"> ' +
                    '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> ' +
                    '<i class="fa fa-' + icon + '-circle fa-fw fa-lg"></i> ' + message +
                    '</div>';
            });
            if (messages.length) {
                $('#rewrite_status .error-messages').empty();
                $('#rewrite_status .error-messages').append(messages);
                $('#rewrite_status .error-messages').slideDown();

            }
        }
    }).fail(function (json) {
        getErrorMessages(json, block, errorsBlock);
    });

    return false;
}