$(document).ready(function () {

    if ($('#trackVal').val()) {
        $('.print_note').removeClass('hidden');
    }

    if ($('#trackVal').val()) {
        $('#delivery_note_create').addClass('hidden');
        $('#delivery_note_delete').removeClass('hidden');
    } else {
        $('#delivery_note_delete').addClass('hidden');
    }

    //Datetimepicker block
    $('#delivery_date').datetimepicker({
        i18n: {
            de: {
                months: [
                    'Januar', 'Februar', 'März', 'April',
                    'Mai', 'Juni', 'Juli', 'August',
                    'September', 'Oktober', 'November', 'Dezember'
                ],
                dayOfWeek: [
                    "So.", "Mo", "Di", "Mi",
                    "Do", "Fr", "Sa."
                ]
            }
        },
        timepicker: false,
        format: 'Y-m-d',
        defaultDate: new Date()
    });

    $('#delivery_time_min').datetimepicker({
        datepicker: false,
        format: 'H:m',
        defaultTime:'09:00'
    });

    $('#delivery_time_max').datetimepicker({
        datepicker: false,
        format: 'H:m',
        defaultTime:'22:00'
    });


    $.datetimepicker.setLocale('id');

    $('#pickup_date').datetimepicker({
        i18n: {
            de: {
                months: [
                    'Januar', 'Februar', 'März', 'April',
                    'Mai', 'Juni', 'Juli', 'August',
                    'September', 'Oktober', 'November', 'Dezember',
                ],
                dayOfWeek: [
                    "So.", "Mo", "Di", "Mi",
                    "Do", "Fr", "Sa.",
                ]
            }
        },
        timepicker: false,
        format: 'Y-m-d',
        defaultDate: new Date()
    });

    $('#pickup_time_min').datetimepicker({
        datepicker: false,
        format: 'H:m',
        defaultTime:'09:00'
    });

    $('#pickup_time_max').datetimepicker({
        datepicker: false,
        format: 'H:m',
        defaultTime:'22:00'
    });

    // $(document).on('change', '#sender',
    //     function () {
    //         var keyId = $(this).val();
    //         $.post('/ajax/viettel/sender-by-key', {key: keyId}, function (data) {
    //             if (data.success) {
    //                 $('.sender-block').empty();
    //                 $('.sender-block').append(data.html);
    //                 if ($('#sender_warehouse').val() && data.notes) {
    //                     $('#order_note').text(data.notes[$('#sender_warehouse').val()]);
    //                 }
    //             }
    //         })
    //     });

});


function processResponse(response) {
    try {
        // if (response.integration.oldToken) {
        //     $('div#sender_add').addClass('md-show');
        // }
        if (response.integration.created) {
            $.post('/ajax/integrations/ninjaxpress/delivery-note/html/' + response.integration.sender + '/' +response.integration.track, function (data) {
                $('.print_note').removeClass('hidden');
                $('.print_note').empty();
                $('.print_note').append(data.html);
            });

            $('#delivery_note_create').addClass('hidden');
            $('#delivery_note_edit').removeClass('hidden');
            $('#delivery_note_delete').removeClass('hidden');
            $('#track2').val(response.integration.track2);
            $('#trackVal').val(response.integration.track);
        }

        if (response.integration.created) {
            $('#delivery_note_create').addClass('hidden');
           // $('#delivery_note_delete').removeClass('hidden');
            $('#trackVal').val(response.integration.track);
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
