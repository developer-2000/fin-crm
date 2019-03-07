$(function () {
    $('form').on('submit', processResult);
});

function processResult() {
    event.preventDefault();
    var scriptId = $('#scriptId').val();
    var text = CKEDITOR.instances.ckeditor.getData();
    $.post('/script/' + scriptId + '/block-add-ajax', $(this).serialize() + '&' + $.param({text: text}), function (json) {
        if (json.success) {
            getMessage('success', 'Блок успешно добавлен');
            $("form").trigger('reset');
        }
        else {
            getMessage('error', 'Ошибка');
        }
    }).fail(function (json) {
        console.log(json);
        var errors = [];
        $.each(JSON.parse(json.responseText).errors, function (e, obj) {
            errors.push(obj + '<br>');
        });
        getMessage('error', errors);
    });
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