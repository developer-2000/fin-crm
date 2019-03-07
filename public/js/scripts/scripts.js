$(function () {
    $('.activate_script_row').on('change', changeStatus);
});

function changeStatus() {
    var script = $(this).val();
    if ($(this).prop('checked')) {
        var status = 'active';

        $.getJSON('/script/' + script + '/set-status/' + status, function (json) {
            if (json.success) {
                getMessage('success', 'Скрипт успешно актирован')
            }
             else if (json.isActiveScript) {
                getMessage('warning', 'Активным может быть только один скрипт!');
            }
            else {
                getMessage('error', 'Ошибка');
            }
        });
    }
    else {
        status = 'inactive';
        $.getJSON('/script/' + script + '/set-status/' + status, function (json) {
            if (json.success) {
                getMessage('warning', 'Скрипт успешно деактирован');
            }
            else {
                getMessage('error', 'Ошибка');
            }
        })
    }
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
