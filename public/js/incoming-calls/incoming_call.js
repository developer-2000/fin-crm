$(function () {
    $('#country').select2();
    $('#create_order').on('submit' , createOrder);
    $('.search_product').on('keydown', searchProduct);
    $('#phone_search').on('keydown', searchPhone);
});

function createOrder() {
    if ($('#create_order').hasClass('create')) {
        var required_fields = {
            'name': 'required',
            'surname': 'required',
            'phone': 'required',
            'country': 'required',
            'middle' : ''
        };
        var error = false;
        $.each(required_fields, function (fieldName, required) {
            var obj = $('#' + fieldName);
            var value = deleteWhiteSpace(obj.val());
            var parent = obj.parents('.form-group');
            var message = '';
            var pattern = /^[0-9]+$/;
            parent.removeClass('has-error');
            parent.find('.help-block').remove();
            if ((value.length > 100 || value.length < 2) && (required || value.length > 0)) {
                if (!value.length) {
                    message += '<span class="help-block"><i class="icon-remove-sign"></i>Заполните поле</span>';
                } else {
                    message += '<span class="help-block"><i class="icon-remove-sign"></i> Некорректная длина строки</span>';
                }
                parent.addClass('has-error');
            }
            if (fieldName == 'phone' && !pattern.test(value)) {
                message += '<span class="help-block"><i class="icon-remove-sign"></i> Номер должен быть числовым</span>';
                parent.addClass('has-error');
            }

            if (message) {
                error = true;
                parent.find('.error_messages').append(message);
            }
        });
        if (!error) {
            getMessage('wait', "Обрабатывается");
            $.post('/ajax/incoming-call/create-order', $('#create_order').serialize(), function (json) {
                if (!json.status && json.errors) {
                    getMessage('error', "Некорректные данные!");
                    $.each(json.errors, function (fieldName, value) {
                        var obj = $('#' + fieldName);
                        var parent = obj.parents('.form-group');
                        var label = parent.find('label').text().replace(' *', '');
                        var message = '';
                        parent.find('.help-block').remove();
                        parent.addClass('has-error');
                        if (fieldName != 'name') {
                            message = value[0].replace(fieldName, '"' + label + '"');
                        } else {
                            message = value[0].replace('Название', '"' + label + '"');
                        }
                        if (message) {
                            parent.find('.error_messages').append('<span class="help-block"><i class="icon-remove-sign"></i>' + message + '</span>');
                        }
                    });
                } else if (json.status) {
                    getMessage('success', "Данные сохранены");
                    window.orderId = json.status;
                    $('.my_disable').removeClass('my_disable');
                    $('#def').after(json.html);
                    $('#create_order').removeClass('create');

                    /**
                     * сохраняем контактные данные
                     */
                    $('#create_order').on('submit', changeInfoClient);
                    $('#change_targets').on('change', changeTargetForCreateOrder);
                    $('.order_confirm').on('click', confirmOrder);
                    $('.failure_order').on('click', failureOrder);
                    $('.cancel_order').on('click', cancelOrder);
                    $('.call_back').on('click', otherTarget);

                }
            });
        }
    }
}

/**
 * удаление пробелов с начала строки
 * @param string
 * @returns {*}
 */
function deleteWhiteSpace(string) {
    var whiteSpace = 0;
    for(var i = 0; i < string.length; i++) {
        if (string[i] == ' ') {
            whiteSpace++;
        } else {
            break;
        }
    }
    if (whiteSpace) {
        string = string.substr(whiteSpace);
    }
    return string;
}

/**
 * Поиск по всем товарам
 */
function searchProduct(e) {
    if ((e.which >= 48 && e.which <= 90) || (e.which >= 186 && e.which <= 220) || (e.which >= 96 && e.which <= 111) || e.which == 8 || e.which == 46) {
        setTimeout(function() {
            var search = $(e.currentTarget).val();
            if (!search) {
                $('.search_block').empty();
                $('.search_block').css('display', 'none');
                return false;
            }
            $.post('/ajax/incoming-call/create-order/search-product', {orderId: orderId,search: search, orderId: window.orderId}, function(json) {
                $('.search_block').empty();
                $('.search_block').append(json.html);
                $('.search_block').css('display', 'block');
                /**
                 * добовляем товар из поиска
                 */
                $('.search_block .add_product').on('click', addNewProduct);
            }, 'json');
        }, 200);
    }
}

function searchPhone(e) {
    if ((e.which >= 48 && e.which <= 90) || (e.which >= 186 && e.which <= 220) || (e.which >= 96 && e.which <= 111) || e.which == 8 || e.which == 46) {
        setTimeout(function() {
            var search = $(e.currentTarget).val();
            var phone = $('#incoming_phone').text();

            $.post('/incoming-call/search-orders-by-phone-ajax', {phone: search, incoming_phone: phone}, function(json) {
                $('#orders tbody').empty();
                $('#orders tbody').append(json.html);
                $('a.pop').hover(function () {
                    $(this).siblings('.data_popup').fadeToggle(100);

                });
                $('a.pop').on('click', function () {
                    return false;
                });
            }, 'json');
        }, 200);
    }
}

function changeTargetForCreateOrder(e) {
    var targetId = $(e.currentTarget).val();
    getMessage('wait', "Обрабатывается");
    $.post('/incoming-call/create-order/get-target-ajax', {orderId: window.orderId, targetId: targetId}, function (json) {
        if (json.status) {
            var tabsContent = ['approve', 'failure', 'fake'];
            $.each(tabsContent, function (key, divId) {
                $('#' + divId).remove();
            });
            $('.nav-tabs .active').removeClass('active');
            $('#def').addClass('in active');
            $('#def').after(json.html);
            $('.order_confirm').on('click', confirmOrder);
            $('.failure_order').on('click', failureOrder);
            $('.cancel_order').on('click', cancelOrder);
            $('.call_back').on('click', otherTarget);
            getMessage('success', "Цель успешна изменена");
            $('#change_targets').on('change', changeTargetForCreateOrder);
        } else {
            getMessage('error', "Произошла ошибка");
        }

    });
}