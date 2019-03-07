$(document).ready(function () {
    jQuery('#datetimepicker').datetimepicker({
        format: 'd/m/Y H:m:s',
    });
  //  jQuery.datetimepicker.setLocale('ru');
    var deliveryMode = document.getElementById('approve[delivery_mode]');

    if (deliveryMode.value == 136) {
        $('#block_pvz').removeClass('hidden');
        $('#block_address').addClass('hidden');
        $('#calculate_cost_actual').removeClass('hidden');
    }
    else if (deliveryMode.value == 137) {
        $('#block_address').removeClass('hidden');
        $('#block_pvz').addClass('hidden');
        $('#calculate_cost_actual').removeClass('hidden');
    }

    $(deliveryMode).change(function () {
        if ($(this).val() == 136) {
            $('#block_pvz').removeClass('hidden');
            $('#block_address').addClass('hidden');
            $('#calculate_cost_actual').removeClass('hidden');
        }
        else if ($(this).val() == 137) {
            $('#block_address').removeClass('hidden');
            $('#block_pvz').addClass('hidden');
            $('#calculate_cost_actual').removeClass('hidden');
        }
    });
    $('#calculate_cost_actual_button').click(function () {
        var order_id = $('.order_id').text();

        $.ajax({
          type: 'post',
          url: '/ajax/cdek/calculate-cost-actual',
          dataType: 'json',
          data: $('form#order_data').serialize() + '&' + $.param({order_id: order_id}),
        }).done(function(response) {
          if (response.success) {
              showMessage('success', "Запрос выполнен успешно");
              $('#deliveryCost').val(response.deliveryCost);
              return false;
          }
          showMessage('error', response.message);
        }).fail(function (json) {
            try {
                var response = JSON.parse(json.responseText);
                if (response.errors) {
                    var messages = '';
                    $.each(response.errors, function (name, value) {
                        var message = '';
                        var fieldName = deleteDote(name);
                        if (fieldName) {
                            $.each(value, function (key, error) {
                                var obj = document.getElementsByName(fieldName);
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
                        $('#order_data .error-messages').slideDown();
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

        // $.post('/ajax/cdek/calculate-cost-actual', $('form#order_data').serialize() + '&' + $.param({order_id: order_id}), function (json) {
        //     if (json.deliveryCost) {
        //         document.getElementById('approve[cost_actual]').value = json.deliveryCost;
        //     }
        //     if(json.errors){
        //         getMessage('error', json.errors);
        //     }
        // })
    });


    var region = document.getElementById('approve[region]');
    //$(region).select2({ data: { text: 'Mainstay Electric' } });
    $(region).select2({
        placeholder: "Выберите регион.",
        minimumInputLength: 0,
        ajax: {
            method: 'post',
            delay: 250,
            url: '/ajax/cdek/regions/find',
            dataType:
                'json',
            data:
                function (params) {
                    return {
                        q: $.trim(params),
                        regionCode: document.getElementById('approve[region]') ?
                            document.getElementById('approve[region]').value : ''
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
        width: '100%'
    });



    var city = document.getElementById('approve[city]');
    $(city).select2({
        placeholder: "Выберите город.",
        minimumInputLength: 0,
        ajax: {
            method: 'post',
            delay: 250,
            url: '/ajax/cdek/cities/find',
            dataType:
                'json',
            data:
                function (params) {
                    console.log(document.getElementById('approve[region]') ?
                        document.getElementById('approve[region]').value : '');
                    return {
                        q: $.trim(params),
                        regionCode: document.getElementById('approve[region]') ?
                            document.getElementById('approve[region]').value : ''
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
        width: '100%'
    });
    var warehouse = document.getElementById('approve[warehouse]');
    $(warehouse).select2({
        placeholder: "Выберите ПВЗ.",
        minimumInputLength: 0,
        ajax: {
            method: 'post',
            delay: 250,
            url: '/ajax/cdek/pvz/find',
            dataType:
                'json',
            data:
                function (params) {
                    return {
                        q: $.trim(params),
                        regionCode: document.getElementById('approve[region]') ?
                            document.getElementById('approve[region]').value : '',
                        cityCode: document.getElementById('approve[city]') ?
                            document.getElementById('approve[city]').value : ''
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
        width: '100%'
    });

    if ($('#trackVal').val()) {
        $('#delivery_note_create').addClass('hidden');
    } else {
        $('#delivery_note_edit').addClass('hidden');
        $('#delivery_note_delete').addClass('hidden');
    }

    if ($(warehouse).value) {
        $.get('/ajax/cdek/pvz/find', {}, function (json) {
            if (json.length) {
                $(warehouse).select2('data', json[0])
            }
        })
    }
});

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

function processResponse(response) {
    try {
        if (response.integration.created) {
            $('.sent-to-print').removeClass('hidden');
            $('#delivery_note_create').addClass('hidden');
            $('#delivery_note_delete').removeClass('hidden');
            $('#trackVal').val(response.integration.track);
        }
        if (response.integration.deleted) {
            $('#delivery_note_create').removeClass('hidden');
            $('#delivery_note_delete').addClass('hidden');
            $('.print_note').addClass('hidden')
            $('.sent-to-print').addClass('hidden');
            $('#trackVal').val('');
            $('#deliveryCost').val('');
        }
    } catch (e) {
        console.log(e.message);
    }
}
