$(function () {
    $('form').on('submit', registration);
    $('#type').on('change', getForm);
    $('.rank-type').on('change', getRankForm);
    $('#def').on('change', getDef);
    $('#billing').on('change', billing);
    $('.add_new_rank').on('click', addNewRank);
    $('.delete_new_rank').on('click', deleteNewRank);
    $('.select_billing').on('change', getFormBilling);
});

function registration() {
    $(this).find('.has-error').removeClass('has-error');
    $.post(location.pathname , $(this).serialize() , function (json) {
        if (json.errors) {
            for (key in json.errors) {
                $('#' + key).parents('.form-group').addClass('has-error');
            }
        }
        if (json.success) {
            if (!json.update) {
                getMessage('success', "Компания добавлена");
                $('form')[0].reset();
                getForm();
                $('.billing_block').empty();
            } else {
                getMessage('success', "Компания изменена");
            }
        }
    }).fail(function (json) {
        try {
            var response = JSON.parse(json.responseText);
            if (response.errors) {
                var messages = '';
                $.each(response.errors, function (name, value) {
                    var message = '';
                    var fieldName = deleteDoted(name);
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
                    $('error-messages').slideDown();
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

function getForm() {
    var type = $(this).val();
    var inputs = $('#' + type).clone().html();
    var wrapper = $(this).parents('.wrapper');
    wrapper.find('.type').empty();
    wrapper.find('.type').append(inputs);
    renameInput(wrapper.find('.type').find('input'), 'global');
    $('#def').attr('checked', false);
}

function renameInput(inputs, prefix) {
    $(inputs).each(function () {
        var name = $(this).attr('name');
        if (name.split('][').length > 1) {
            name = name.split('][')[1].replace(']', '');
        }
        var newName = prefix + '[' + name + ']';
        $(this).attr('name', newName).attr('id', newName);

        var label = $(this).parents('.form-group').find('label[for="' + name + '"]');
        label.attr('for', newName);
    })
}


function getRankForm() {
    var type = $(this).val();
    var inputs = $('#' + type).clone().html();
    var wrapper = $(this).parents('.wrapper');
    wrapper.find('.type').empty();
    wrapper.find('.type').append(inputs);
    renameInputRanks($(this));
    $('#def').attr('checked', false);
}


function renameInputRanks(currentBlock) {
    var wrappers = currentBlock.parents('.ranks_block').find('.wrapper');
    var i = 0;
    wrappers.each(function () {
        var inputs = $(this).find('[name]'),
            prefix;

        if ($(this).parents('.billing_block').length > 0) {
            prefix = 'ranks_billing[' + i + ']';
        } else {
            prefix = 'ranks[' + i + ']';
        }
        renameInput(inputs, prefix);
        i++;
    })
}


function getDef() {
    if ($(this).prop('checked')) {
        $.post('/get-default-prices-ajax', {}, function (json) {
            for(prop in json.prices) {
                $('form #' + prop).val(json.prices[prop]);
            }
        })
    }
}

function billing() {
    if ($(this).prop('checked')) {
        $('.billing_block').empty();
        $('.billing_block').append($('#billing_block').clone().html()).fadeIn(400);

        $('.billing_block .add_new_rank').on('click', addNewRank);
        $('.select_billing').on('change', getFormBilling);
    } else {
        $('.billing_block').empty();
        $('.billing_block').fadeOut(400);
    }
}

function getFormBilling() {
    var type = $(this).val();
    var inputs = $('#' + type).clone().html();
    $('.type_billing').empty();
    $('.type_billing').append(inputs);
    renameInput($('.type_billing').find('input'), 'global');
    $('#def').attr('checked', false);
}

/**
 * Выводим сообщение
 */
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

function addNewRank() {
    var block = $('.hidden>.wrapper').clone();
    var parent = $(this).parents('.form-horizontal').find('.ranks_block');
    parent.append(block);
    renameInputRanks($('.ranks_block .wrapper'));
    $('.rank-type').on('change', getRankForm);
    $('.delete_new_rank').on('click', deleteNewRank);
    return false;
}

function deleteNewRank() {
    var wrapper = $(this).parents('.wrapper');
    wrapper.fadeOut(300);
    setTimeout(function () {
        wrapper.remove();
        renameInputRanks($('.ranks_block .wrapper'));
    },300);
    return false;
}