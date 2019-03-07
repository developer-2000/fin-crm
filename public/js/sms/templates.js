$('.alias-link')
    .on('click', function () {
        var cursorPos = $('.input-large').prop('selectionStart');
        var v = $('.input-large').val();
        var textBefore = v.substring(0, cursorPos);
        var textAfter = v.substring(cursorPos, v.length);
        $('.input-large').val(textBefore + $(this).attr('data-value') + textAfter);
    });


$('form#template-create').submit(function () {
    event.preventDefault();
    $.post('/ajax/sms/templates/create', $(this).serialize(), function (json) {
        if (json.success) {
            getMessage('success', 'Шаблон успешно добавлен!');
            $('#template-create').removeClass('md-show');
            $('.templates').empty();
            $('.templates').append(json.html);
            $(document).ready(function () {
                $.fn.editable.defaults.mode = 'popup';
                $.fn.editable.defaults.params = function (params) {
                    params.id = $(".template").data("data-id");
                    return params;
                };
                //toggle `popup` / `inline` mode
                $.fn.editable.defaults.mode = 'popup';
                $.fn.editable.defaults.params = function (params) {
                    params.id = $(".category").data("data-id");
                    return params;
                };

                //make username editable
                $('.destroy-template').editable({
                    type: 'none',
                    escape: true,
                    title: 'Вы действительно хотите удалить шаблон?',
                    tpl: '',
                    success: function (response) {
                        if (response.pk) {
                            var parent = $("a[data-pk='" + Number(response.pk) + "']").parents('tr');
                            parent.fadeOut(400);
                            setTimeout(function () {
                                parent.remove();
                            }, 400);
                        }
                    }
                });

                $('.template-name').editable({
                    escape: true,
                    title: 'Редактировать название',
                });

                $('.template').editable({

                    escape: true,
                    title: 'Редактировать шаблон',
                    onblur: "ignore"
                });
            });
        }
    }).fail(function (json) {
        var errors = [];
        $.each(JSON.parse(json.responseText).errors, function (e, obj) {
            errors.push(obj + '<br>');
        });
        getMessage('error', errors);
    });
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