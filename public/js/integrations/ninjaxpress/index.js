$(document).ready(function () {


    $('.key-add').click(function () {
        var form = $('#key-add').serialize();
        $.post('/ajax/ninjaxpress/key/add', form, function (json) {
            if (json.success) {
                $('div#key_add').removeClass('md-show');
                $('tbody').empty();
                $('tbody').append(json.html);

                getMessage('success', 'Отправитель Ninjaxpress был успешно создан!');
            }
            if (json.error) {
                getMessage('error', json.error);
            }

        }).fail(function (json) {
            var errors = [];
            $.each(JSON.parse(json.responseText).errors, function (e, obj) {
                errors.push(obj + '<br>');
            });
            getMessage('error', errors);
        });
    });

    $('.generate-access-token').click(function () {
        var key_id = $('#ninjaxpress_key_id').val();
        $.post('/ajax/ninjaxpress/generate-access-token', {key_id: key_id}, function (json) {
            if (json.success) {
                $('tbody').empty();
                $('tbody').append(json.html);
                getMessage('success', 'Токен был успешно сгенерирован!');
            }
            if (json.errors) {
                getMessage('error', json.errors);
            }

        }).fail(function (json) {
            var errors = [];
            $.each(JSON.parse(json.responseText).errors, function (e, obj) {
                errors.push(obj + '<br>');
            });
            getMessage('error', errors);
        });
    });

    $('.generate-hmac').click(function () {
        var key_id = $('#ninjaxpress_key_id').val();
        // $.post('/api/ninjaxpress/webhooks', {key_id: key_id}, function (json) {
        //
        // });
        $.post('/ajax/ninjaxpress/generate-hmac', {key_id: key_id}, function (json) {
            if (json.success) {
                // $('tbody').empty();
                // $('tbody').append(json.html);
                getMessage('success', 'Hmac был успешно сгенерирован!');
            }
            if (json.errors) {
                getMessage('error', json.errors);
            }

        }).fail(function (json) {
            var errors = [];
            $.each(JSON.parse(json.responseText).errors, function (e, obj) {
                errors.push(obj + '<br>');
            });
            getMessage('error', errors);
        });
    });

    $(document).on('change', '.activate_key', function () {
        if ($(this).is(':checked')) {
            var status = 1;
        } else {
            status = 0;
        }
        $.post('/ajax/ninjaxpress/key/activate', {key_id: $(this).attr('id'), status: status},
            function (json) {
                if (json.success) {
                    getMessage('success', 'Ключ Ninjaxpress  был успешно изменен!');
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

});
