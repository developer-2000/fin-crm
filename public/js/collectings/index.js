$(function () {
    /*
   *Стилизация select
    */
    $('#status, #country, #collectors').select2({
        placeholder: 'Все',
        minimumInputLength: 0,
        allowClear: true,
    });

    $('#choose_all').on('change', chooseAllOrders);
    $('.share').on('click', processSharing);
    $('#date_template :radio').on('change', getDataTemplate);

    $('#project').select2({
        placeholder: "",
        minimumInputLength: 0,
        multiple: true,
        ajax: {
            method: 'get',
            url: '/projects/find',
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
    });

    if ($('#project').attr('data-project')) {
        console.log($('#project').attr('data-project'));
        var arrayForSelect2 = [];
        $.each(JSON.parse($('#project').attr('data-project')), function (element, value) {
            arrayForSelect2.push(value);
        });
        $("#project").select2('data', arrayForSelect2);
    }

    $('#sub_project').select2({
        placeholder: "",
        minimumInputLength: 0,
        multiple: true,
        ajax: {
            method: 'get',
            url: '/sub_projects/find',
            dataType:
                'json',
            data:
                function (params) {
                    return {
                        q: $.trim(params),
                        project_id: $('#project').val().split(",")
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
    if ($('#sub_project').attr('data-sub_project')) {
        var arrayForSelect2 = [];
        $.each(JSON.parse($('#sub_project').attr('data-sub_project')), function (element, value) {
            arrayForSelect2.push(value);
        });
        $("#sub_project").select2('data', arrayForSelect2);
    }
});

function chooseAllOrders() {
    if ($(this).is(':checked')) {
        $('.choose').each(function (e, val) {
            $(val).prop('checked', true);
        });
    } else {
        $('.choose').each(function (e, val) {
            $(val).prop('checked', false);
        })
    }
}

function processSharing() {
    disableButton($('.share'), true);
    var shareAllOrders = 0;
    if ($('#add_all').is(':checked')) {
        shareAllOrders = 1;
    }
    var orders = [];
    var type = $(this).attr('data-type');

    $('.choose').each(function (e, val) {

        if ($(this).is(':checked')) {
            orders.push($(val).val());
        }
    });

    if (!orders.length && !shareAllOrders) {
        getMessage('error', 'Выберите заказ!');
        return false;
    }

    getMessage('wait', 'Обработка!');
    $.post('/ajax/share-collector-orders', $('form#filters').serialize() + '&' + $.param({
        shareAllOrders: shareAllOrders,
        params: window.location.search,
        collectors: $('#collectors').val(), orders: orders,
        type : type
    }), function (json) {
        if (json.error) {
            if (json.message) {
                getMessage('error', json.message);
            } else {
                getMessage('error', 'Произошла ошибка.Данные е добавленные');
            }
        } else if (json.success) {
            window.location.reload();
        } else {
            getMessage('error', 'Данные не добавлены');
        }
    }).fail(function () {
        getMessage('error', 'Произошла ошибка');
        window.location.reload();
    });
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

function getDataTemplate(e) {
    var obj = $(e.currentTarget);
    var dateStartObj = $('#date_start');
    var dateEndObj = $('#date_end');
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
        $.post('/date-filter-template-ajax/', {type: type}, function (json) {
            dateStartObj.val(json.start);
            dateEndObj.val(json.end);
        }, 'json');
    }
}