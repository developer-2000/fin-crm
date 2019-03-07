$(function () {
    $('.checkbox_permission').on('click', setUserPermission)
});

function setUserPermission() {
    var pid = $(this).attr('data-id');
    var status = $(this).prop('checked') ? 1 : 0;
    $.post('/ajax/set-user-permission/' + pid, {status: status, user_id: $('#current_user_id').attr('data-id')}, function(json) {
        if (json) {
            getMessage("success", "Права доступа изменены");
        } else {
            getMessage("error", "Произошла ошибка");
        }
    }, 'json');
}

function getMessage(type, message) {
    $('.ns-box').remove();
    var notification = new NotificationFx({
        message : '<span class="icon fa fa-bullhorn fa-2x"></span><p>'+ message +'</p>',
        layout : 'bar',
        effect : 'slidetop',
        type : type,
        ttl: 800,
    });
    notification.show();
}