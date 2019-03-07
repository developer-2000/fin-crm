$(function () {
    let date = document.getElementById('approve[date]');
    let city = document.getElementById('approve[city]');
    let address = document.getElementById('approve[street]');
    let openAddress = false;

    myDatepicker2($(date));
    $('input[name="approve[time_min]"]').mask("99:99");
    $('input[name="approve[time_max]"]').mask("99:99");

    function myDatepicker2(obj) {
        var start = new Date();
        obj.datepicker({
            language: 'ru',
            startDate: start,
            onSelect: function (fd, d, picker) {
                if (!d) {
                    return;
                }
            }
        });
    }

    $(city).select2({
        placeholder: "Выберите город.",
        minimumInputLength: 1,
        width : '100%',
        ajax: {
            method: 'POST',
            delay: 250,
            url: '/ajax/integrations/measoft/find/town',
            dataType:
                'json',
            data:
                function (params) {
                    return {
                        query: $.trim(params)
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
    $(address).typeahead( {
        ajax: {
            url: "/ajax/integrations/measoft/find/street",
            method: 'get',
            riggerLength: 1,
            preDispatch: function(query) {
                return {
                    query : query,
                    code  : $(city).val()
                }
            }
        },
        display: 'value'
    });
    $(address).on('keyup', function () {
        if (!openAddress && $(city).val()) {
            openAddress = true;
            $(this).parent().addClass('open');
        }
    });
    try {
        //получение города по коду
        if ($(city).val()) {
            $.get('/ajax/integrations/measoft/find/town', {query: 'town', code: $(city).val()}, function (json) {
                $(city).select2('data', json[0]);
            })
        }
    } catch (e) {
        console.log(e.message);
    }
});

function processResponse(json) {
    var messages = '';
    if (json.integration && json.integration.messages && json.integration.messages.length) {
        $.each(json.integration.messages, function (index, values) {
            messages += '<div class="alert alert-danger fade in"> ' +
            '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> ' +
            '<i class="fa fa-times-circle fa-fw fa-lg"></i> ' + values +
            '</div>';
        })
    } else if (json.integration && json.integration.success && json.integration.track) {
        $(document.getElementById('approve[track]')).val(json.integration.track);
        $('#delivery_note_create').addClass('hidden');
        $('#delivery_note_delete').removeClass('hidden');
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

function deleveryNoteDeleteProcessed(json) {
    var messages = '';
    if (json.integration && json.integration.errors && json.integration.errors.length) {
        $.each(json.integration.errors, function (index, values) {
            messages += '<div class="alert alert-danger fade in"> ' +
                '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> ' +
                '<i class="fa fa-times-circle fa-fw fa-lg"></i> ' + values +
                '</div>';
        })
    } else if (json.integration && json.integration.success) {
        getMessage('success', 'Экспресс накладная успешно удалена!');
        $(document.getElementById('approve[track]')).val('');
        $('#delivery_note_delete').addClass('hidden');
        $('#delivery_note_create').removeClass('hidden');
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


