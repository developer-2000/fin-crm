$(function () {
   $('form').on('submit', addList);
});

function addList() {
    $(this).find('.has-error').removeClass('has-error');
    $.post('/ajax/cold-calls/import-process' , $(this).serialize() , function (json) {
        if (json.errors) {
            for (key in json.errors) {
                $('#' + key).parents('.form-group').addClass('has-error');
            }
        }
        if (json.success) {
            window.location = "/cold-calls/lists";
        }else{
            getMessage('error', "Укажите, пожалуйста, поле номера телефона");
        }
    });

    return false
}

/**
 * Выводим сообщение
 */
function getMessage(type, message) {
    $('.ns-box').remove();
    var notification = new NotificationFx({
        message: '<span class="icon fa fa-bullhorn fa-2x"></span><p>' + message + '</p>',
        layout: 'bar',
        effect: 'slidetop',
        type: type,
        ttl: 3000,
    });

    notification.show();
}
