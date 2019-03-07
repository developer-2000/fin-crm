$('#city').select2({
    placeholder: "Выберите город.",
    minimumInputLength: 1,
    ajax: {
        method: 'get',
        delay: 250,
        url: '/ajax/novaposhta/settlements/find',
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
    }
});


$(' #warehouse').select2({
    placeholder: "Выберите отделение.",
    minimumInputLength: 1,
    ajax: {
        method: 'get',
        url: '/ajax/novaposhta/warehouses/find',
        dataType:
            'json',
        data:
            function (params) {
                return {
                    settlementRef: $('#city').val(),
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
    }
});


if ($('#city-name').val()) {
    var cityData = JSON.parse($('#city-name').val());
    $('#city').select2('data', {"id": cityData.id, "text": cityData.text});

}
$(document).on('change', '#sender-city', function () {
    window.settlementRef = $('#sender-city').val();
});


if ($('#warehouse-name').val()) {
    var warehouseData = JSON.parse($('#warehouse-name').val());
    $('#warehouse').select2('data', {"id": warehouseData.id, "text": warehouseData.text});
}

$('#save-sender').submit(function () {
    event.preventDefault();
    $.post('/ajax/sender-save', $(this).serialize(), function (json) {
        if (json.success) {
            getMessage('success', 'Контрагент успешно обновлен!');
        }
    }).fail(function (json) {
        var errors = [];
        $.each(JSON.parse(json.responseText), function (e, obj) {
            errors.push(obj + '<br>');
        });
        getMessage('error', errors);
    });
});

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
