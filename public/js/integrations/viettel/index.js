$(document).ready(function () {

    $('#district').select2({
        initSelection: function (element, callback) {
            return callback({id: 1, text: 'default selection with id 1'});
        },
        placeholder: "Выберите город.",
        minimumInputLength: 1,
        ajax: {
            method: 'get',
            delay: 250,
            url: '/ajax/viettel/district/find',
            dataType:
                'json',
            data:
                function (params) {
                    return {
                        q: $.trim(params)
                    };
                },
            results: function (data) {
                return {
                    results: data,
                    "pagination": {
                        "more": true
                    }
                };
            }
        }
    });


    $(' #ward').select2({
        placeholder: "Выберите админ округ.",
        minimumInputLength: 1,
        ajax: {
            method: 'get',
            url: '/ajax/viettel/ward/find',
            dataType:
                'json',
            data:
                function (params) {
                    return {
                        settlementRef: window.settlementRef,
                        q: $.trim(params)
                    };
                },
            results: function (data) {
                return {
                    results: data,
                    "pagination": {
                        "more": true
                    }
                };
            }
        }
    });

    $('.account_register').click(function () {
        var form = $('#account-create').serialize();
        $.post('/ajax/viettel/account/create', form, function (json) {
            if (json.success) {
                $('div#form_block').removeClass('md-show');
                getMessage('success', 'Аккаунт Viettel был успешно создан!');
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

    $('.sender-add').click(function () {
        var form = $('#sender-add').serialize();
        $.post('/ajax/viettel/sender/add', form, function (json) {
            if (json.success) {
                $('div#sender_add').removeClass('md-show');
                $('tbody').empty();
                $('tbody').append(json.html);

                getMessage('success', 'Отправитель Viettel был успешно создан!');
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

    $(document).on('change', '.activate_account', function () {
        if ($(this).is(':checked')) {
            var status = 1;
        } else {
            status = 0;
        }
        $.post('/ajax/viettel/sender/activate', {key_id: $(this).attr('id'), status: status},
            function (json) {
                if (json.success) {
                    getMessage('success', 'Отправитель Viettel был успешно изменен!');
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
