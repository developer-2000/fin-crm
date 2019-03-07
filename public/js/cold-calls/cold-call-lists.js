$(function () {
    $('body').on('click', '.delete_list', deleteList);
});


function deleteList() {
    var id = $(this).find('span').attr('data-id');
   var parent = $(this).parents('tr');
    $.post('/ajax/delete-cold-call-file', {id: id}, function (json) {
        if (json.success) {
            parent.fadeOut(400);
            setTimeout(function () {
                parent.remove();
            }, 400);
            getMessage('success', 'Лист холодных продаж удален');
        } else {
            getMessage('error', 'Лист холодных продаж не возможно удалить т.к. он находится в обработке, обратитесь, пожалуйста, к модератору');
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