$(function () {
    $('.checkbox_role').on('click', setPermission)
});

function setPermission() {
    var pid = $(this).parents('tr').attr('id');
    var rid = $(this).attr('data-id');
    var status = $(this).prop('checked') ? 1 : 0;
    $.post('/ajax/set-role-to-permission/' + pid, {role: rid, status: status}, function(json) {
        if (json) {
            getMessage("success", "Привелегие добавленно");
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