//  * Поиск по офферам
$('.offers').select2({
    placeholder: "Выберите оффер.",
    minimumInputLength: 1,
    multiple: true,
    ajax: {
        url: '/offer/find',
        dataType: 'json',
        data: function (params) {

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

$('#rate-add').on('submit',  rateAdd);

function rateAdd() {
    event.preventDefault();
    $.post('/ajax/plan-rate-add', $(this).serialize(), function (json) {
        if(Number.isInteger(json.planRateId)) {
            window.location = '/plans/rates/edit/' + json.planRateId;
        }
    });
    return false;
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