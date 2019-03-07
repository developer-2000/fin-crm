$(function () {
    $('.checkbox_status').on('click', setStatus)
});

function setStatus() {
    var offerId = $(this).parents('tr').attr('id');
    var status = $(this).prop('checked') ? 'active' : 'inactive';
    $.post('/ajax/set-offer-status/' + offerId, {status: status}, function(json) {
        if (json.status == 'active') {
            getMessage("success", "Оффер активирован");
        }
        else if( json.status == 'inactive'){
            getMessage("warning", "Оффер деактивирован");
        }
        else {
            getMessage("error", "Произошла ошибка");
        }
    }, 'json');
}

function getMessage(type, message) {
    $('.ns-box').remove();
    var notification = new NotificationFx({
        message : '<span class="icon fa fa-bullhorn fa-2x"></span><p>'+ message +'</p>',
        layout : 'bar',
        effect : 'slidetop',
        type : type,
        ttl: 800,
    });
    notification.show();
}

$('.company-select2').select2({
    placeholder: "Выберите компанию",
    minimumInputLength: 1,
    multiple: true,
    ajax: {
        url: '/company/find/',
        dataType: 'json',
        data: function (params) {
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
    }
});
