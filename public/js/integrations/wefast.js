$(function () {
    $('#add_token').on('submit', createToken);
    $('.status').on('change', changeStatus);
    $('.status_conterparty').on('change', changeStatusCounterparty);
    $('#add_offices').on('submit', addOffices);
    $('#add_counterparty').on('submit', createCounterparty);
    $('#edit_counterparty').on('submit', aditCounterparty);

    /**
     * выбор фотки
     */
    $(document).on('change', ':file', function() {
        var input = $(this),
            numFiles = input.get(0).files ? input.get(0).files.length : 1,
            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
        input.trigger('fileselect', [numFiles, label]);
    });
    $(':file').on('fileselect', function (event, numFiles, label) {

        var input = $(this).parents('.input-group').find(':text'),
            log = numFiles > 1 ? numFiles + ' files selected' : label;

        if (input.length) {
            input.val(log);
        } else {
            if (log) alert(log);
        }

    });

    $('#province').select2({
        minimumInputLength: 1,
        ajax: {
            method: 'post',
            delay: 250,
            url: '/ajax/integrations/wefast/find/province',
            dataType:
                'json',
            data:
                function (params) {
                    return {
                        q: $.trim(params)
                    };
                },
            results: function (data) {
                return {
                    results: data,
                    "pagination": {
                        "more": true
                    }
                };
            }
        },
        width : '100%'
    }).on('change', function () {
        $('#district').select2('data', {id : null, text: ''});
        $('#ward').select2('data', {id : null, text: ''});
    });
    $('#district').select2({
        minimumInputLength: 1,
        ajax: {
            method: 'post',
            delay: 250,
            url: '/ajax/integrations/wefast/find/district',
            dataType:
                'json',
            data:
                function (params) {
                    return {
                        province : $('#province').val(),
                        q: $.trim(params)
                    };
                },
            results: function (data) {
                return {
                    results: data,
                    "pagination": {
                        "more": true
                    }
                };
            }
        },
        width : '100%'
    }).on('change', function () {
        $('#ward').select2('data', {id : null, text: ''})
    });
    $('#ward').select2({
        minimumInputLength: 1,
        ajax: {
            method: 'post',
            delay: 250,
            url: '/ajax/integrations/wefast/find/ward',
            dataType:
                'json',
            data:
                function (params) {
                    return {
                        province : $('#province').val(),
                        district : $('#district').val(),
                        q: $.trim(params)
                    };
                },
            results: function (data) {
                return {
                    results: data,
                    "pagination": {
                        "more": true
                    }
                };
            }
        },
        width : '100%'
    });

    if ($('#district').val()) {
        $.get('/ajax/integrations/wefast/find/district', {q : $('#district').val(), province : $('#province').val()}, function (json) {
            if (json.length) {
                $('#district').select2('data', json[0])
            }
        })
    }

    if ($('#province').val()) {
        $.get('/ajax/integrations/wefast/find/province', {q : $('#province').val()}, function (json) {
            if (json.length) {
                $('#province').select2('data', json[0])
            }
        })
    }

    if ($('#ward').val()) {
        $.get('/ajax/integrations/wefast/find/ward', {q : $('#ward').val(), province : $('#province').val(), district : $('#district').val()}, function (json) {
            if (json.length) {
                $('#ward').select2('data', json[0])
            }
        })
    }

    initEditable();
});

function createToken() {
    disableButton($('#form_block').find('[type="submit"]'), true);
    var block = $('#form_block');
    var errorsBlock = block.find('.error-messages');
    $.post('/ajax/integrations/wefast/create-token', $(this).serialize(), function (json) {
        if (json.success) {
            getMessage('success', 'Ранг добавлен');
            block.find('.close').click();
            if (json.html) {
                $('#block_keys tbody').empty();
                $('#block_keys tbody').append(json.html);

                $('.status').on('change', changeStatus);
                initEditable();
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

function changeStatus() {
    $.post('/ajax/integrations/wefast/change-status-key', { status : +$(this).prop('checked'), id : +$(this).attr('data-id')}, function (json) {
        if (json.success) {
            getMessage('success', 'Статус именен');
        } else {
            getMessage('error', 'Статус не именен');
        }
    })
}

function addOffices() {
    var form_data = new FormData(),
        block = $('#form_block'),
        errorsBlock = block.find('.error-messages');
    form_data.append('file', $('input[type=file]').prop('files')[0]);
    $.ajax({
        type : 'post',
        url : '/ajax/integrations/wefast/import-offices',
        data : form_data,
        cache : false,
        contentType : false,
        processData : false,
        beforeSend : function () {
            getMessage("wait", 'Обработка');
        },
        success:  function (json) {
            if (!json.errors && !json.errors.length) {
                window.location.href = window.location.href;
            } else {
                errorsBlock.empty();
                var mes = '';
                $.each(json.errors, function (id, message) {
                    mes += '<div class="alert alert-danger fade in"> ' +
                        '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> ' +
                        '<i class="fa fa-times-circle fa-fw fa-lg"></i> Ошибка в строке [' + id + ']' + message +
                        '</div>';
                });
                errorsBlock.append(mes);
            }
        }
    }).fail(function (response) {
        if (response.status == 504) {
            getMessage("success", 'Данные обрабатываются');
        } else {
            getMessage("error", 'Произошла ошибка на сервере');
        }
        $('#form_block .close').click();
    });

    return false;
}

function createCounterparty() {
    disableButton($('#form_block').find('[type="submit"]'), true);
    var block = $('#form_block');
    var errorsBlock = block.find('.error-messages');
    getMessage('wait', 'Обработка');
    $.post('/ajax/integrations/wefast/create-counterparty', $(this).serialize(), function (json) {
        if (json.success) {
            getMessage('success', 'Контрагент добавлен');
            block.find('.close').click();
            if (json.html) {
                $('#block_counterparties tbody').empty();
                $('#block_counterparties tbody').append(json.html);

                $('.status_conterparty').on('change', changeStatusCounterparty);
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

function changeStatusCounterparty() {
    $.post('/ajax/integrations/wefast/change-status-counterparty', { status : +$(this).prop('checked'), id : +$(this).attr('data-id')}, function (json) {
        if (json.success) {
            getMessage('success', 'Статус именен');
        } else {
            getMessage('error', 'Статус не именен');
        }
    })
}

function initEditable() {
    $('.key_name, .key_token').editable({
        url : '/ajax/integrations/wefast/edit-key',
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
    $('.key_sub_project').editable({
        url : '/ajax/integrations/wefast/edit-key',
        ajaxOptions : {
            type : 'post',
            dataType : 'json'
        },
        select2: {
            placeholder: 'Select User',
            allowClear: true,
            minimumInputLength: 1,
            width : '170px',
            ajax: {
                url: '/sub_projects/find',
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
                getMessage('error', "Произошла ошибка!");
            }

        },
        error : function (errors) {
            getMessage('error', "Произошла ошибка на сервере!");
        }
    });
    $('.delete_key').editable({
        tpl : '',
        emptytext : '<span class="fa-stack "> ' +
        '<i class="fa fa-square fa-stack-2x"></i> ' +
        '<i class="fa fa-trash-o fa-stack-1x fa-inverse"></i> ' +
        '</span>',
        url : '/ajax/integrations/wefast/delete-key',
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
            var response = JSON.parse(errors.responseText);
            if (response.counterparties) {
                getMessage('error', "Ключ принадлежит контрагенту!");
            } else {
                getMessage('error', "Произошла ошибка на сервере!");
            }
        }
    });
    $('.delete_counterparty').editable({
        tpl : '',
        emptytext : '<span class="fa-stack "> ' +
        '<i class="fa fa-square fa-stack-2x"></i> ' +
        '<i class="fa fa-trash-o fa-stack-1x fa-inverse"></i> ' +
        '</span>',
        url : '/ajax/integrations/wefast/counterparties/delete',
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
    $('#key').select2({
        minimumInputLength: 1,
        ajax: {
            url: '/ajax/integrations/wefast/find/key',
            dataType: 'json',
            data: function (params) {
                return {
                    query: $.trim(params),
                    sub_project_id : $('[name="sub_project_id"]').val(),
                };
            },
            results: function (data) {
                return {
                    results: data,
                };
            }
        },
        width : '100%',
    });
    if ($('#project_id').attr('data-content')) {
        $('#project_id').select2('data', JSON.parse($('#project_id').attr('data-content')));
    }
    if ($('#sub_project_id').attr('data-content')) {
        $('#sub_project_id').select2('data', JSON.parse($('#sub_project_id').attr('data-content')));
    }
    if ($('#key').attr('data-content')) {
        $('#key').select2('data', JSON.parse($('#key').attr('data-content')));
    }
}

function aditCounterparty() {
    var form = $(this),
        btn = $(this).find('[type="submit"]'),
        errorsBlock = form.find('.error-messages');
    disableButton(btn, true);
    $.post('/ajax/integrations/wefast/counterparties/edit', $(this).serialize(), function (json) {
        if (json.success) {
            getMessage('success', 'Изменения сохранены');
        } else {
            getErrorMessages(json, form, errorsBlock);
        }
        disableButton(btn);
    }).fail(function (json) {
        getErrorMessages(json, form, errorsBlock);
        disableButton(btn);
    });

    return false;
}
