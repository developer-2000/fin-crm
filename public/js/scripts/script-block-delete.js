/*delete block with parent li*/
$('.delete-block').editable({
    type: 'none',
    escape: true,
    title: 'Вы действительно хотите удалить блок?',
    tpl: '',
    success: function (response) {
        var parent = $("li[data-id='" + Number(response.pk) + "']");
        if (response.pk) {
            parent.fadeOut(400);
            setTimeout(function () {
                parent.remove();
            }, 400);
            getMessage('success', 'Блок успешно удален');
        } else {
            getMessage('error', 'Ошибка');
        }
    }
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