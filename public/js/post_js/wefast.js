;(function () {
    $('#province').select2({
        placeholder: "Выберите город.",
        minimumInputLength: 1,
        ajax: {
            method: 'post',
            delay: 250,
            url: '/ajax/integrations/wefast/find/province',
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
        width : '100%'
    }).on('change', function () {
        $('#district').select2('data', {id : null, text: ''})
    });
    $('#district').select2({
        placeholder: "Выберите город.",
        minimumInputLength: 1,
        ajax: {
            method: 'post',
            delay: 250,
            url: '/ajax/integrations/wefast/find/district',
            dataType:
                'json',
            data:
                function (params) {
                    return {
                        province : $('#province').val(),
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
        width : '100%'
    });

    if ($('#district').val()) {
        $.get('/ajax/integrations/wefast/find/district', {q : $('#district').val(), province : $('#province').val()}, function (json) {
            if (json.length) {
                $('#district').select2('data', json[0])
            }
        })
    }

    if ($('#province').val()) {
        $.get('/ajax/integrations/wefast/find/province', {q : $('#province').val()}, function (json) {
            if (json.length) {
                $('#province').select2('data', json[0])
            }
        })
    }

}());
function processResponse(response) {
    try {
        if (response.integration) {
            var messages = '';
            if (response.integration.errorCode && response.integration.errorMessage) {
                messages += '<div class="alert alert-danger fade in"> ' +
                    '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> ' +
                    '<i class="fa fa-times-circle fa-fw fa-lg"></i> ' + response.integration.errorMessage +
                    '</div>';
            } else if (response.integration.success && response.integration.data.orderNumber) {
                messages += '<div class="alert alert-success fade in"> ' +
                    '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> ' +
                    '<i class="fa fa-check-circle fa-fw fa-lg"></i>Track <strong>' + response.integration.data.orderNumber +
                    '</strong></div>';
                document.getElementById('approve[cost_actual]').value = response.integration.data.totalMoneyCollectedAmount;
                document.getElementById('approve[track]').value = response.integration.data.orderNumber;
            }
            if (messages.length) {
                if (!window.checkMessage) {
                    $('.error-messages').empty();
                    $('.error-messages').append(messages);
                    $('#order_data .error-messages').slideDown();
                    window.checkMessage = true;
                } else {
                    $('.error-messages').append(messages);
                }
            }
        }
    } catch (e) {
        console.log(e.message);
    }
}
