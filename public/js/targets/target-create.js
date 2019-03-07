$(function () {
    $('#offer').select2();

    /**
     * показываем шаблон
     */
    $('#template').on('change', showTemplate);

    /**
     * показываем option для полей
     */
    $('.typeFiled').on('change', showOptions);

    /**
     * добавление нового поля для своего шаблона
     */
    $('.add_field').on('click', addNewCustomField);

    /**
     * Удаление поля
     */
    $('.del_field').on('click', deleteField);

    /**
     * сохранение цели
     */
    $('#create_target').on('submit', addTarget);

    /**
     * Изменяем цель
     */
    $('#change_target').on('submit', updateTarget);
});

function addNewCustomField() {
    // $('.custom_template .add_field').remove();
    var clone = $('.custom_template_hidden>div').clone();
    $('.custom_template').append(clone);
    /**
     * добавление нового поля для своего шаблона
     */
    $(clone).find('.add_field').on('click', addNewCustomField);
    /**
     * показываем option для полей
     */
    $(clone).find('.typeFiled').on('change', showOptions);
    /**
     * Удаление поля
     */
    $(clone).find('.del_field').on('click', deleteField);
    renameFields();
    return false;
}

function addOption() {
    var clone = $('.options_hidden .row').clone();
    $(this).parents('.form-group').find('.options').append(clone);
    renameOptions($(this).parents('.form-group').find('.options'));
    /**
     * добавить option
     */
    $(clone).find('.add_option').on('click', addOption);
    /**
     * удаление option
     */
    $(clone).find('.del_option').on('click', delOption);
    return false;
}

function addFirstOption(optionBlock) {
    var clone = $('.options_hidden .row').clone();
    optionBlock.parents('.form-group').find('.options').append(clone);
    /**
     * добавить option
     */
    $('.option .add_option').on('click', addOption);
    /**
     * удаление option
     */
    $('.option .del_option').on('click', delOption);
    renameOptions(optionBlock.parents('.form-group').find('.options'));
    return false;
}

function showTemplate() {
    $('.templates .active').removeClass('active');
    var value = $(this).val();
    var template = value + '_template';
    $('.' + template).addClass('active');
    if (value == 'custom' && !$('.' + template).text().length) {
        addNewCustomField();
    }
}

function showOptions() {
    var typeField = $(this).find('option:selected').attr('data-options');
    if (typeField) {
        $(this).parents('.form-group').find('.options').removeClass('hidden');
        if (!$(this).parents('.form-group').find('.options').text().length) {
            addFirstOption($(this));
        }
    } else {
        $(this).parents('.form-group').find('.options').addClass('hidden');
    }
}

function  deleteField() {
    $(this).parents('.custom_field').remove();
    $(this).parents('.product_field').remove();
}

function delOption() {
    $(this).parents('.option').parent('.row').remove();
}

function renameOptions(optionBlock) {
    var countField = 0;
    var select = $(optionBlock).parent().find('select');
    var fieldId = select.attr('id').replace(/\D+/g, '');
    $(optionBlock).find('.option').each(function () {
        var inputs = $(this).find(':input[name |="option"]');
        $(inputs).each(function () {
            var id = $(this).attr('id').split('[');
            var parameters = id[0].split('-');
            var template = $('#template').val() + '_template';
            var newId = template + '[' + fieldId + "][" + parameters[0] + '][' + countField + '][' + parameters[1] + "]";
            $(this).attr('id', newId);
            $(this).attr('name', newId);
            $('label[for="' + id + '"]').attr('for', newId);
        });
        countField++;
    });
}

function renameFields() {
    var countField = 0;
    $('.custom_template .custom_field').each(function () {
        var inputs = $(this).find(':input[name |="custom_template[field"]');
        $(inputs).each(function () {
            var id = $(this).attr('id').split('[');
            var newId = id[0] + '[' + countField + '][' + id[1];
            $(this).attr('id', newId);
            $(this).attr('name', newId);
            $('.custom_template label[for="' + id[0] + '[' + id[1] + '"]').attr('for', newId);
        });
        var options = $(this).find('.options');
        renameOptions(options);
        countField++;
    });
}

function addTarget() {
    $(this).find('.help-block').empty();
    $(this).find('.has-error').removeClass('has-error');
    getMessage('wait', 'Обработка');

    $.ajax({
        url : '/target/create-ajax',
        type : "POST",
        dataType : "json",
        data : $(this).serialize()
    }).done(function (json) {
        if (json.success) {
            getMessage('success', 'Цель добавлена');
        } else {
            getMessage('error', 'Ошибка!Данные не сохранены.');
        }
    }).fail(function (json) {
        var response = JSON.parse(json.responseText);
        if (response.errors) {
            $.each(response.errors,function (name, messages) {
                var fieldName = deleteDoted(name);
                $.each(messages, function (id, message) {
                    var element = document.getElementById(fieldName);
                    //todo доделать вывод ошибок
                    var formGroup = $(element).parents('.form-group');
                    var errorSpan = formGroup.find('.help-block');
                    var label = formGroup.find('label').text();
                    var resultMessage = '';
                    formGroup.addClass('has-error');
                    if (!errorSpan.length) {
                        errorSpan = '<div class="col-sm-offset-4 help-block"></div>';
                        formGroup.append(errorSpan);
                    }
                    resultMessage += '<div>';
                    if (fieldName == 'name') {
                        resultMessage += message.replace('Название', '<strong>"' + label + '"</strong>');
                    } else {
                        resultMessage += message.replace(name, '<strong>"' + label + '"</strong>');
                    }
                    resultMessage += '</div>';
                    formGroup.find('.help-block').append(resultMessage);
                })
            });
            getMessage('error', 'Ошибка вылидации');
        } else {
            getMessage('error', 'Произошла ошибка на сервере');
        }
    });
    return false
}

function updateTarget() {
    $(this).find('.help-block').empty();
    $(this).find('.has-error').removeClass('has-error');
    getMessage('wait', 'Обработка');

    $.ajax({
        url : '/ajax/target/update',
        type : "POST",
        dataType : "json",
        data : $(this).serialize()
    }).done(function (json) {
        if (json.success) {
            getMessage('success', 'Цель отредактирована');
        } else {
            getMessage('error', 'Ошибка!Данные не сохранены.');
        }
    }).fail(function (json) {
        var response = JSON.parse(json.responseText);
        if (response.errors) {
            $.each(response.errors,function (name, messages) {
                var fieldName = deleteDoted(name);
                $.each(messages, function (id, message) {
                    var element = document.getElementById(fieldName);
                    //todo доделать вывод ошибок
                    var formGroup = $(element).parents('.form-group');
                    var errorSpan = formGroup.find('.help-block');
                    var label = formGroup.find('label').text();
                    var resultMessage = '';
                    formGroup.addClass('has-error');
                    if (!errorSpan.length) {
                        errorSpan = '<div class="col-sm-offset-4 help-block"></div>';
                        formGroup.append(errorSpan);
                    }
                    resultMessage += '<div>';
                    if (fieldName == 'name') {
                        resultMessage += message.replace('Название', '<strong>"' + label + '"</strong>');
                    } else {
                        resultMessage += message.replace(name, '<strong>"' + label + '"</strong>');
                    }
                    resultMessage += '</div>';
                    formGroup.find('.help-block').append(resultMessage);
                })
            });
            getMessage('error', 'Ошибка вылидации');
        } else {
            getMessage('error', 'Произошла ошибка на сервере');
        }
    });
    return false
}

function deleteDoted(fieldName) {
    var params = fieldName.split('.');
    var result = fieldName;
    if (params.length > 1) {
        result = '';
        for (var i = 0; i < params.length; i++) {
            if (i == 0) {
                result += params[i] + '[';
            } else if (i + 1 != params.length) {
                result += params[i] + '][';
            } else if (i + 1 == params.length) {
                result += params[i] + ']';
            }
        }
    }
    return result;
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