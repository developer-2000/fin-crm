$(function () {
    /**
     * удаление не закрытой сессии и логинимся
     */
    $('#new_session').on('click', function () {
        $.post('/login', $('form').serialize() + '&' + $.param({
            newSession: true
        }), function (json) {
            if (json.new_session) {
                location.href = '/';
            }
        }, 'json');
        return false;
    })
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