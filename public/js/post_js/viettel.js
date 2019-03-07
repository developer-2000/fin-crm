$(document).ready(function () {
    jQuery('#datetimepicker').datetimepicker({
        format: 'd/m/Y H:m:s',
    });
    jQuery.datetimepicker.setLocale('vi');

    $(document).on('change', '#sender',
        function () {
            var keyId = $(this).val();
            $.post('/ajax/viettel/sender-by-key', {key: keyId}, function (data) {
                if (data.success) {
                    $('.sender-block').empty();
                    $('.sender-block').append(data.html);
                    if ($('#sender_warehouse').val() && data.notes) {
                        $('#order_note').text(data.notes[$('#sender_warehouse').val()]);
                    }
                }
            })
        });

    $('#province').select2({
        placeholder: "Выберите провинцию.",
        minimumInputLength: 1,
        ajax: {
            method: 'post',
            delay: 250,
            url: '/ajax/viettel/province/find',
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
        },
        width: '100%'
    });

    $('#district').select2({
        placeholder: "Выберите district.",
        minimumInputLength: 1,
        ajax: {
            method: 'post',
            delay: 250,
            url: '/ajax/viettel/district/find',
            dataType:
                'json',
            data:
                function (params) {
                    return {
                        province_id: $('#province').val(),
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
        },
        width: '100%'
    });

    $('#ward').select2({
        placeholder: "Выберите ward.",
        minimumInputLength: 1,
        ajax: {
            method: 'post',
            delay: 250,
            url: '/ajax/viettel/ward/find',
            dataType:
                'json',
            data:
                function (params) {
                    return {
                        district: $('#district').val(),
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
        },
        width: '100%'
    });


    if ($('#trackVal').val()) {
        $('#delivery_note_create').addClass('hidden');
    } else {
        $('#delivery_note_edit').addClass('hidden');
        $('#delivery_note_delete').addClass('hidden');
    }

    if ($('#district').val()) {
        $.get('/ajax/viettel/district/find', {
            district: $('#district').val(),
            province: $('#province').val()
        }, function (json) {
            if (json.length) {
                $('#district').select2('data', json[0])
            }
        })
    }

    if ($('#province').val()) {
        $.get('/ajax/viettel/province/find', {province: $('#province').val()}, function (json) {
            if (json.length) {
                $('#province').select2('data', json[0])
            }
        })
    }

    if ($('#ward').val()) {
        $.get('/ajax/viettel/ward/find', {ward: $('#ward').val()}, function (json) {
            if (json.length) {
                $('#ward').select2('data', json[0])
            }
        })
    }

});


$(document).on('click', '.sign_in', function () {
    $.post('/ajax/viettel/sign-in', {
        account_email: $('#account_email').val(),
        account_password: $('#account_password').val()
    }, function (json) {
        if (json.keyUpdated) {
            $('div#sender_add').removeClass('md-show');
            $('button#delivery_note_create').trigger("click");
        }
    })
});


function processResponse(response) {
    try {
        // if (response.integration.oldToken) {
        //     $('div#sender_add').addClass('md-show');
        // }
        if (response.integration.created) {
            console.log(response.integration)
            $('.sent-to-print').removeClass('hidden');
            $('#delivery_note_create').addClass('hidden');
            $('#delivery_note_delete').removeClass('hidden');
            $('#trackVal').val(response.integration.track);
            if (response.integration.deliveryCost) {
                $('#deliveryCost').val(response.integration.deliveryCost);
            }
        }
        if (response.integration.deleted) {
            $('#delivery_note_create').removeClass('hidden');
            $('#delivery_note_delete').addClass('hidden');
            $('.print_note').addClass('hidden')
            $('.sent-to-print').addClass('hidden');
            $('#trackVal').val('');
            $('#deliveryCost').val('');
        }
    } catch (e) {
        console.log(e.message);
    }
}
