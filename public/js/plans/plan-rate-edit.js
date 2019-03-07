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

$('#rate-add').on('submit', rateAdd);
$('#offer-add').on('submit', offerAdd);
$(function () {
    $('body').on('click', '#delete_offer', deleteRow);
    $('body').on('click', '#delete_rate', deleteRate);
});

function deleteRow() {
    var offerRow = (JSON.parse($(this).find('span').attr('data-id')));
    var parent = $(this).parents('tr');
    $.post('/ajax/delete-plan-offer-ajax', {offerRow: offerRow}, function (json) {
        if (json.success) {
            parent.fadeOut(400);
            setTimeout(function () {
                parent.remove();
            }, 400);
            getMessage('success', 'Оффер удален');
        } else {
            getMessage('error', 'Ошибка');
        }
    });
    return false;
}

function deleteRate() {
    var geo = $(this).find('span').attr('data-id');
    event.preventDefault();
    var url = $(location).attr('href');
    var segments = url.split('/');
    var planRateId = parseInt(segments[5]);
    var parent = $(this).parents('tr');
    $.post('/ajax/delete-plan-rate', {geo: geo, planRateId: planRateId}, function (json) {
        if (json.success) {
            parent.fadeOut(400);
            setTimeout(function () {
                parent.remove();
            }, 400);
            getMessage('success', 'Норма успешно удалена');
        } else {
            getMessage('error', 'Ошибка');
        }
    });
    return false;
}

function rateAdd() {
    event.preventDefault();
    var url = $(location).attr('href');
    var segments = url.split('/');
    var planRateId = parseInt(segments[5]);

    $.post('/ajax/plan-rate-add-with-link/' + planRateId, $(this).serialize(), function (json) {
        if (json.error) {
            getMessage('error', json.error);
        }
        if (json.html) {
            $('.all-plan-rates').empty();
            $('.all-plan-rates').append(json.html);
            getMessage('success', 'Норма апрува добавлена');
        }
    });
    return false;
}

function offerAdd() {
    event.preventDefault();
    var url = $(location).attr('href');
    var segments = url.split('/');
    var planRateId = parseInt(segments[5]);
    $.post('/ajax/plan-rate-add-offer/' + planRateId, $(this).serialize(), function (json) {
        if (json.error) {
            getMessage('error', json.error);
        }
        if (json.html) {
            $('.all-plan-rates').empty();
            $('.all-plan-rates').append(json.html);
            getMessage('success', 'Оффер успешно добавлен');
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

if ($('#planRateOffers').val()) {
    $('.offers').select2('data', JSON.parse($('#planRateOffers').val()));
}