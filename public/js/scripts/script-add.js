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

$(function () {
    $('form').on('submit', processResult);
});

if ($('#offersJson').val()) {
    $('.offers').select2('data', JSON.parse($('#offersJson').val()));
}

function processResult() {
    event.preventDefault();

    $.post('/ajax/scripts/create', $(this).serialize(), function (json) {
        if (json.scriptId) {
            window.location = '/scripts/' + json.scriptId + '/blocks/create';
        }
        else {
            getMessage('error', 'Ошибка');
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