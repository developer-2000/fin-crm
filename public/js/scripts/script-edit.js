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

    $('#form2').on('submit', processResult);
    $('.activate_script_row').on('change', changeStatus);
    $('#form1').on('submit', changeOffer);

});

function changeOffer() {

    $.post('/ajax/scripts/change-offer', $(this).serialize(), function (json) {
        if (json.success) {
            getMessage('success', 'Скрипт успешно изменен!');
        }
        else {
            getMessage('error', 'Ошибка');
        }
    });
    return false;
}

$('document').ready(function () {
    $('.note-editable').text($('#text').val());
});

function changeStatus() {
    var scriptDetailId = $(this).val();
    if ($(this).prop('checked')) {
        var status = 'active';

        $.getJSON('/ajax/scriptDetail/' + scriptDetailId + '/' + status, function (data) {
        })
    }
    else {
        status = 'inactive';
        $.getJSON('/ajax/scriptDetail/' + scriptDetailId + '/' + status, function (data) {
        })
    }
}


$(document).ready(function () {
    var updateOutput = function (e) {
        var list = e.length ? e : $(e.target),
            output = list.data('output');
        if (window.JSON) {
            output.val(window.JSON.stringify(list.nestable('serialize'), null));//, null, 2));
            var data = window.JSON.stringify(list.nestable('serialize'));
            $.ajax({
                type: "POST",
                data: {data: data},
                url: '/ajax/scripts/details/update',
                success: function (data) {
                    if (data) {
                        $(data.positions).each(function (e, val) {
                            var id = val["id"];
                            if ($('[data-id = ' + id + ' ]')) {
                                $('[data-id = ' + id + ' ]').find('div .position').text(e + 1);
                            }
                        });
                    }
                }
            });
        }
        else {
            output.val('JSON browser support required for this demo.');
        }
    };

    $('.dd').each(function () {
        var nestableId = $(this).attr('id');
        $('#' + nestableId).nestable({
            group: 0,
            maxDepth: 1
        })
            .on('change', updateOutput);
        updateOutput($('#' + nestableId).data('output', $('#nestable-output')));
    });
});

function processResult() {
    var offerId = $('#offerId').val();

    var scriptId = $('#scriptId').val();
    var text = CKEDITOR.instances.ckeditor.getData();

    $.post('/script/' + scriptId + '/edit-ajax', $(this).serialize() + '&' + $.param({text: text}), function (json) {
        if (json.success) {
            getMessage('success', 'Скрипт успешно изменен');
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

if ($('#offersJson').val()) {
    $('.offers').select2('data', JSON.parse($('#offersJson').val()));
}