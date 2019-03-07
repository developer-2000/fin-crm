$(document).on("click", 'button.editable-submit', function (e){
    event.preventDefault();

    var id =  $(e.currentTarget).parents('td').find('a.rate-delete').attr('data-id');
    var parent = $(this).parents('tr');
    $.post('/plans/rates/' + id + '/delete', function (json) {
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
            ttl: 6000,
        });
    } else {
        var notification = new NotificationFx({
            message: '<span class="icon fa fa-bullhorn fa-2x"></span><p>' + message + '</p>',
            layout: 'bar',
            effect: 'slidetop',
            type: type,
            ttl: 6000,
        });
    }
    notification.show();
}