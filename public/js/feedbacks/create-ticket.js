$('#company_id').change(function () {
    var data = $(this).val();
    $.ajax({
        type: "POST",
        url: '/operator-mistakes/users/get-by-company-id/' + data,
        success: function (data) {
            if (data.html) {
                $('.operators').empty();
                $('.operators').append(data.html);
            }
        }
    });
});
$('#send_all').change(function () {
    if ($(this).is(':checked')) {
    } else {

    }
    var data = $(this).val();
    $.ajax({
        type: "POST",
        url: '/operator-mistakes/users/get-by-company-id/' + data,
        success: function (data) {
            if (data.html) {
                $('.operators').empty();
                $('.operators').append(data.html);
            }
        }
    });
});

$('.send-failed-ticket').submit(function (e) {
    event.preventDefault();
    $.ajax({
        type: "POST",
        data: $(this).serialize(),
        url: '/ajax/send-ticket',
        success: function (data) {
            if (data) {
                $('form').trigger('reset');
                $('.md-modal').removeClass('md-show');
                showMessage('success', data.message);
                $('#feedback_order_' + data.orderId).empty();
                $('#feedback_order_' + data.orderId).append(data.html);
            }
        }
    }).fail(function (data) {
        var errors = [];
        $.each(JSON.parse(data.responseText), function (e, obj) {
            errors.push(obj + '<br>');
        });

        showMessage('error', errors);
    });
});
$('.send-success-ticket').submit(function () {
    event.preventDefault();
    $.ajax({
        type: "POST",
        data: $(this).serialize(),
        url: '/ajax/send-ticket',
        success: function (data) {
            if (data) {
                $('form').trigger('reset');
                $('.md-effect-15').removeClass('md-show');
                showMessage('success', data.message);
                $('#feedback_order_' + data.orderId).empty();
                $('#feedback_order_' + data.orderId).append(data.html);
            }
        }
    }).fail(function (data) {
        var errors = [];
        $.each(JSON.parse(data.responseText), function (e, obj) {
            errors.push(obj + '<br>');
        });

        showMessage('error', errors);
    });
});

$('#send-info-fault-ticket').submit(function () {
    event.preventDefault();
    $.ajax({
        type: "POST",
        data: $(this).serialize(),
        url: '/ajax/send-info-fault-ticket',
        success: function (data) {
            if (data.success) {
                $('form').trigger('reset');
                $('.md-effect-15').removeClass('md-show');
                showMessage('success', data.message);
            } else {
                showMessage('error', data.message);
            }
            if (data.html) {
                $('#feedbacks tbody tr:first').before(data.html);
            }
        }
    }).fail(function (data) {
        var errors = [];
        $.each(JSON.parse(data.responseText), function (e, obj) {
            errors.push(obj + '<br>');
        });

        showMessage('error', errors);
    });
});