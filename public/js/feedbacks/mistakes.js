$('#send_message').on('submit', sendNewMessage);

function sendNewMessage() {
    event.preventDefault();
    showMessage('processing');
    $.post('/ajax/feedback/new-message', $(this).serialize(), function (json) {
        if (json.html && json.success) {
            $('.conversation-wrapper').empty();
            $('.conversation-wrapper').append(json.html);
            showMessage('success', json.message);
        }
        else {
            showMessage('error', json.message);
        }
    });
    return false;
}


$('#form1').on('submit', closeFeedback);

function closeFeedback() {
    event.preventDefault();

    var feedbackId = $('#feedback_id').val();
    if ($('#close_feedback').prop('checked')) {
        var status = 'closed';
        $('button.send_message').addClass('hidden');
        $('#comment').addClass('hidden');
        $('#conversation').removeClass('hidden');
        showMessage('processing');
        $.post('/ajax/feedback-change-status', $(this).serialize() + '&' + $.param({
            status: status,
            feedbackId: feedbackId
        }), function (json) {
            if (json.success) {
                showMessage('success', json.message);
            }
            else {
                showMessage('error', json.message);
            }
        })
    }
    else {
        status = 'opened';
        $.post('/ajax/feedback-change-status', $(this).serialize() + '&' + $.param({
            status: status,
            feedbackId: feedbackId
        }), function (json) {
            $('button.send_message').removeClass('hidden');

            $('#comment').removeClass('hidden');
            $('#conversation').addClass('hidden');
            if (json.success) {
                showMessage('success', json.message);
            }
            else {
                showMessage('error', json.message);
            }
        })
    }
}

/*
 *Стилизация select
  */
$('#status').select2({
    // placeholder: 'Все',
    allowClear: true,
});

$('#country').select2({
    // placeholder: 'Все',
    allowClear: true
});

$('#company').select2({
    // placeholder: 'Все',
    allowClear: true
});

$('#mistake_type').select2({
    // placeholder: 'Все',
    allowClear: true
});
$('#moderator').select2({
    // placeholder: 'Все',
    allowClear: true
});


$('#user').select2({
    // placeholder: 'Все',
    allowClear: true
});

$('#target').select2({
    // placeholder: 'Все',
    allowClear: true
});

$('.set-order').select2({
    // placeholder: "Выберите заказ.",
    minimumInputLength: 1,
    multiple: false,
    ajax: {
        url: '/order/find',
        dataType: 'json',
        data: function (params) {

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
$('.offers-select2').select2('enable');

/**
 * Календарик
 */
myDatepicker($('#date_start'));
myDatepicker($('#date_end'));

/**
 * Календарик
 */
function myDatepicker(obj) {
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

$('#date_template :radio').on('change', function(e) {
    var obj = $(e.currentTarget);
    var dateStartObj = $('#date_start');
    var dateEndObj = $('#date_end');
    var type = obj.val();
    if (type == 11) {
        dateStartObj.removeAttr('disabled');
        dateEndObj.removeAttr('disabled');
        return false;
    }
    if (type == 0) {
        dateStartObj.val('');
        dateEndObj.val('');
        return false;
    } else {
        $.post('/date-filter-template-ajax/', {type: type}, function(json) {
            dateStartObj.val(json.start);
            dateEndObj.val(json.end);
        }, 'json');
    }
});