$(function () {
    $('#company, #user, #countries, #offer, #project, #sub_project').select2();

    /**
     * получаем кол-во заказов на модерации
     */
    getCountModeration();
    // addEditable();
    setInterval(function () {
        getCountModeration();
    }, 10000);

    /**
     * аннулировка заказа
     */
    $('.cancel_moderation').on('click', cancelOrderModeration);

    /**
     * Календарик
     */
    myDatepicker($('#date_start_moder'));
    myDatepicker($('#date_end_moder'));

    $('a.pop').hover(function () {
        var popup = $(this).siblings('.data_popup');
        var clone = popup.clone();
        popup.parents('.one_order').siblings('.popups').empty().append(clone);
        clone.css({
            top : (popup.offset().top - $(this).parents('.one_order').offset().top) + 'px',
            left : (popup.offset().left - $(this).parents('.one_order').offset().left) + 123 + 'px',
            visibility: "visible",
            height : popup.height() + 'px'
        });
        popup.parents('.one_order').siblings('.popups').fadeToggle(100);

    });
    $('a.pop').on('click', function () {
        return false;
    });

    $('.set_not_calls_callback').on('click', setNotCallsCallback);
    $('.change_phone').on('click', changePhone);
    $('.check_all').on('click', chooseAll)

    /**
     * анулируем как повтор
     */
    $('.annul').on('click', annulOrder);
    $('.annul').tooltip();

    /**
     * модерируем заказы
     */
    $('.moderation').on('click', moderation);
    $('.moderation').tooltip();

    /**
     * кинуть повтор в прозвон
     */
    $('.go_to_pbx').on('click', goToPbx);


    /**
     * Сменить очередь
     */
    $('.change_campaign').on('click', changeCampaign);


    /**
     * подверждаем что плохая связь
     */
    $('.bad_connection').on('click', badConnection);
    $('.cancel').on('click', cancelBadConnection);

    $('#date_template :radio').on('change', function(e) {
        var obj = $(e.currentTarget);
        var dateStartObj = $('#date_start_moder');
        var dateEndObj = $('#date_end_moder');
        var type = obj.val();
        if (type == 11) {
            dateStartObj.removeAttr('disabled');
            dateEndObj.removeAttr('disabled');
            return false;
        }
        if (type == 0) {
            dateStartObj.val('');
            dateEndObj.val('');
            return false;
        } else {
            $.post('/date-filter-template-ajax/', {type: type}, function(json) {
                dateStartObj.val(json.start.split(' ')[0]);
                dateEndObj.val(json.end.split(' ')[0]);
            }, 'json');
        }
    });

    $('.product_type').on('change', changeProductType);

    $('.assigned_operator').select2({
        ajax: {
            method: 'get',
            url: '/user/find',
            dataType:
                'json',
            data:
                function (params) {
                    return {
                        query: $.trim(params)
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
    });

    $('input.assigned_operator').each(function (id, input) {
        var value = $(input).attr('data-content');
        console.log(value);
        if (value) {
            $(input).select2('data', JSON.parse(value));
        }
    })
});

/**
 * Бросам заказ заново звонится
 */
function setNotCallsCallback(e) {
    var id = $(e.currentTarget).attr('data-id');
    $.post('/set-not-calls-callback-ajax/', {id: id}, function(json) {
        showMessage('success', json.message);
    }, 'json').fail(function (json) {
        showMessage('error');
    });
    return false;
}

/**
 * изменяем номер телефона в некоректных
 */
function changePhone() {
    var id = $(this).attr('data-id');
    var one_order = $(this).parents('.one_order');
    var phone = one_order.find('.phone').val();
    var country = one_order.find('.country').val();
    var price = one_order.find('.price_order_incorrect').val();
    $(this).parents('tr').find('has-error').removeClass('has-error');
    $.post('/ajax/moderation-change-phone-and-country/', {id: id, phone: phone, country: country, price: price}, function(json) {
        if (json.errors) {
            if (json.errors.price) {
                one_order.find('.price_order_incorrect').parent().addClass('has-error');
                showMessage('error', json.message);
            } else if (json.errors === true) {
                one_order.find('.country').parent().addClass('has-error');
                one_order.find('.phone').parent().addClass('has-error');
                showMessage('error', json.message);
            } else {
                one_order.find('.phone').parents('tr').addClass('has-error');
                showMessage('error', json.message);
            }
        } else {
            one_order.remove();
            showMessage('success', json.message);
        }
    }, 'json').fail(function (json) {
        showMessage('error');
    });
    return false;
}

function chooseAll() {
    $(this).parents('table').find('.choose_repeat').prop('checked', true);
}

function annulOrder() {
    var parent = $(this).parents('.one_order');
    var ids = [];
    parent.find('input').each(function () {
        if ($(this).prop('checked')) {
            ids.push($(this).attr('data-id'));
        }
    });
    if (!ids.length) {
        ids.push($(this).parents('tr').find('input').attr('data-id'));
    }
    $.post('/cancel-as-repeat-ajax', {ids:ids}, function (json) {
        if (json.success == false) {
            showMessage('error', json.message);
        } else {
            if (parent.find('input').length == ids.length) {
                parent.fadeOut(400);
                setTimeout(function () {
                        parent.remove();
                },600);
            } else {
                $.each(ids, function (index, value) {
                    $('.children_' + value).fadeOut(400);
                });
                setTimeout(function () {
                    $.each(ids, function (index, value) {
                        $('.children_' + value).remove();
                    });
                },600);
            }
            showMessage('success', json.message);
        }
    });
    return false;
}

function moderation() {
    var orderId = $(this).attr('data-id');
    var parent = $(this).parents('.one_order');
    var storage = null;
    var assigned_operator = null;
    if (parent.find('select[name="sub_project_id"]').length) {
        storage = parent.find('select[name="sub_project_id"]').val();
    }
    if (parent.find('input.assigned_operator').length) {
        assigned_operator = parent.find('input.assigned_operator').val();
    }
    $.post('/moderation-order-ajax/' + orderId, {storage : storage, assigned_operator: assigned_operator }, function (json) {
        if (json.success) {
            parent.fadeOut(600);
            setTimeout(function () {
                parent.remove();
            }, 600);
            showMessage('success', json.message);
        } else if (json.error && json.error.length) {
            showMessage('error', json.error);
        } else {
            showMessage('error', json.message);
        }
    }).fail(function (json) {
        showMessage('error');
    });
    return false;
}

function goToPbx() {
    var parent = $(this).parents('.one_order');
    var ids = [];
    parent.find('input').each(function () {
        if ($(this).prop('checked')) {
            ids.push($(this).attr('data-id'));
        }
    });
    if (!ids.length) {
        ids.push($(this).parents('tr').find('input').attr('data-id'));
    }
    $.post('/go-to-pbx-ajax', {ids:ids}, function (json) {
        if (json.success) {
            if (parent.find('input').length == ids.length) {
                parent.fadeOut(400);
                setTimeout(function (parent) {
                    parent.remove();
                },600);
            } else {
                $.each(ids, function (index, value) {
                    $('.children_' + value).fadeOut(400);
                });
                setTimeout(function () {
                    $.each(ids, function (index, value) {
                        $('.children_' + value).remove();
                    });
                },600);
            }
            showMessage('success', json.message);
        } else {
            showMessage('error', json.message);
        }
    }).fail(function (json) {
        showMessage('error');
    });
    return false;
}

function changeCampaign() {
    var orderId = $(this).attr('data-id');
    var parent = $(this).parents('.one_order');
    var campaign = parent.find('.campaign').val();
    $.post('/change-campaign/' + orderId, {orderId: orderId, campaign: campaign}, function (json) {
        if (json.success) {
            parent.fadeOut(600);
            setTimeout(function () {
                parent.remove();
            }, 600);
            showMessage('success', json.message);
        } else {
            showMessage('error', json.message);
        }
    }).fail(function (json) {
        showMessage('error');
    });
    return false;
}

/**
 * Подтверждаем что звонок с плохой связью
 */
function badConnection() {
    var id = $(this).attr('data-id');
    $.post('/confirm-bad-connection-ajax/' + id, [], function (json) {
       if (json.success) {
           $('#order_' + id + ' .actions').empty();
           $('#order_' + id + ' .actions').html(json.html);
           showMessage('success', json.message);
       } else {
           showMessage('error', json.message);
       }
    }).fail(function (json) {
        showMessage('error');
    });
    return false;
}

/**
 * отмена плохой связи
 */
function cancelBadConnection() {
    var id = $(this).attr('data-id');
    $.post('/cancel-bad-connection-ajax/' + id, [], function (json) {
        if (json.success) {
            $('#order_' + id).addClass('danger');
            $('#order_' + id + ' .callback_status').empty();
            $('#order_' + id + ' .callback_status').text(json.text);
            $('#order_' + id + ' .actions').empty();
            $('#order_' + id + ' .actions').html(json.html);
            showMessage('success', json.message);
        } else {
            showMessage('error', json.message);
        }
    }).fail(function (json) {
        showMessage('error');
    });
    return false;
}
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

function getCountModeration() {
    var search = location.search.replace('?', '');
    $.post('/ajax/count-orders-on-moderation', search, function (json) {
        $('span[data-id]').each(function (id, span) {
            var count = $(span).text();
            var status = $(span).attr('data-id');
            if(json[status]) {
                $(span).text(json[status]);
            } else {
                $(span).text(0);
            }
        })
    })
}

function cancelOrderModeration() {
    var block = $(this).parents('.md-content');
    var data = block.find('form').serialize();
    var parent = block.parents('.one_order');
    $.post('/cancel-order-ajax', data, function (json) {
        if (json.orders && json.target) {
            block.find('.close').click();
            parent.fadeOut(600);
            setTimeout(function () {
                parent.remove();
            }, 600);
            showMessage('success', json.message);
        } else {
            showMessage('error', json.message);
        }
    }).fail(function (json) {
        try {
            var response = JSON.parse(json.responseText);
            if (response.errors) {
                var messages = '';
                $.each(response.errors, function (fieldName, value) {
                    var message = '';
                    if (fieldName) {
                        $.each(value, function (key, error) {
                            var obj = document.getElementById(fieldName);
                            var parent = $(obj).parents('.form-group');
                            var label = $(block).find('label[for="' + fieldName + '"]').text();
                            if (!label.length) {
                                label = parent.find('label').text();
                            }
                            parent.find('.help-block').remove();
                            parent.addClass('has-error');

                            message = error.replace(fieldName, '<strong>"' + label + '"</strong>');

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
                    block.find('.error-messages').empty();
                    block.find('.error-messages').append(messages);
                }
            } else {
                showMessage('error');
            }
        } catch (e) {
            showMessage('error');
        }
    });
}

function changeProductType() {
    var checkboxes = $(this).parent('.checkbox-nice').parent('td').parent('tr').find('.product_type'),
        currentCheckbox = $(this),
        id = currentCheckbox.val(),
        type = currentCheckbox.attr('name'),
        val = currentCheckbox.prop('checked') ? 1 : 0;
    $.each(checkboxes, function () {
        if (type != $(this).attr('name')) {
            $(this).prop('checked', false);
        }
    });

    $.post('/ajax/moderation/change-product-type', {id : id, type : type, value : val}, function (json) {
        if (json.success) {
            showMessage('success', json.message);
            currentCheckbox.prop('checked', val);
        } else {
            showMessage('error', json.message);
            currentCheckbox.prop('checked', !val);
        }
    }).fail(function () {
        showMessage('error');
        currentCheckbox.prop('checked', !val);
    });
}