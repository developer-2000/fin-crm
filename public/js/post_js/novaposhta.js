/*
 * Поиск по городам НП
 */

$(document).ready(function () {
    jQuery('#datetimepicker').datetimepicker({
    format:'d.m.Y',
});

    if ($('#track').val()) {
        $('.print_note').removeClass('hidden');
        $('.sent-to-print').removeClass('hidden');
        // $('.sent-to-print').prop('disabled','disabled' );
    }
    if ($('#procStatus').attr('alias') == 'to_print') {
        $('.sent-to-print').removeClass('hidden').prop('disabled', true);
    }

    $('#approve-city').select2({
        placeholder: "Выберите город.",
        minimumInputLength: 1,
        ajax: {
            method: 'get',
            delay: 250,
            url: '/ajax/novaposhta/settlements/find',
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

    $(' #approve-warehouse').select2({
        placeholder: "Выберите отделение.",
        minimumInputLength: 0,
        ajax: {
            method: 'get',
            url: '/ajax/novaposhta/warehouses/find',
            dataType:
                'json',
            data:
                function (params) {
                    return {
                        settlementRef: $('#approve-city').val(),
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
    if ($('#city').val()) {
        $.post('/ajax/novaposhta/settlements/find', {SettlementRef: $('#city').val(),  orderId: $('.order_id').text()}, function (json) {
            if (json.length) {
                $('#approve-city').select2('data', json[0]);
            }
        });
    }
    $(document).on('change', '#approve-city', function () {
        window.settlementRef = $('#approve-city').val();
        $('.approve-warehouse').select2('data', null);
    });

    if ($('#warehouse').val()) {
        $.post('/ajax/novaposhta/warehouses/find', {
            SettlementRef: $('#city').val(),
            warehouseRef: $('#warehouse').val(),
            orderId: $('.order_id').text()
        }, function (json) {
            if (json.length) {
                $('#approve-warehouse').select2('data', json[0]);
            }
        });
    }
    if ($('#track').val()) {
        $('#delivery_note_create').addClass('hidden');
    } else {
        $('#delivery_note_edit').addClass('hidden');
        $('#delivery_note_delete').addClass('hidden');
    }

});

function processResponse(response) {

    try {
        if (response.integration && response.integration.created) {
            $.post('/ajax/integrations/novaposhta/delivery-note/html/' + response.integration.track, function (data) {
                $('.print_note').removeClass('hidden');
                $('.print_note').empty();
                $('.print_note').append(data.html);
                $('.sent-to-print').removeClass('hidden');
            });

            $('#delivery_note_create').addClass('hidden');
            $('#delivery_note_edit').removeClass('hidden');
            $('#delivery_note_delete').removeClass('hidden');
            $('#track2').val(response.integration.track2);
            $('#trackVal').val(response.integration.track);
        }

        if (response.integration && response.integration.updated) {
            $('#track2').val(response.integration.track2);
        }
        if (response.integration && response.integration.deleted) {
            $('#delivery_note_create').removeClass('hidden');
            $('#delivery_note_edit').addClass('hidden');
            $('#delivery_note_delete').addClass('hidden');
            $('.print_note').addClass('hidden')
            $('.sent-to-print').addClass('hidden');
            $('#trackVal').val('');
        }
    } catch (e) {
        console.log(e.message);
    }
    // if (response.errors) {
    //     var errors = response.errors.description;
    //     getMessage('error', errors);
    // }
}