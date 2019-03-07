$(document).ready(function () {
    $('#success_call').on('click', function () {
        event.preventDefault();
        var inputs = $('#feedback-block').clone().html();
        $('.feedback').empty();
        $('.feedback').append(inputs);
    });
});

$('button#feedback').each(function () {
    $(this).on('click', function (e) {
        event.preventDefault();
        var inputs = $('#feedback-block').clone().html();
        $(e.currentTarget).parent('.feedback').empty().append(inputs);
    });
});

$(function () {
    $(document).on("submit", 'form.failed_call', sendFailedForm);
    $(document).on("submit", 'form.success_call', sendFailedForm);
    $(document).on("click", '#failed_call', getFailedForm);
    $(document).on("click", '#success_call', getSuccessForm);
    $(document).on("click", '#close-feedback-options', closeFeedbackOptions);
    $(document).on("click", 'button#feedback', function (e) {
        event.preventDefault();
        var inputs = $('#feedback-block').clone().html();
        $(e.currentTarget).parent('.feedback').empty().append(inputs);
    });
});

function getFailedForm(e) {
    event.preventDefault();
    var inputs = $('#feedback-block-failed').clone().html();
    $(e.currentTarget).parent('.feedback').empty().append(inputs);
}

function closeFeedbackOptions(e) {
    event.preventDefault();
    var inputs = $('#feedback-initial').clone().html();
    $(e.currentTarget).parent().parent('.feedback').empty().append(inputs);
}

function getSuccessForm(e) {
    event.preventDefault();
    var inputs = $('#feedback-block-success').clone().html();
    $(e.currentTarget).parent('.feedback').empty().append(inputs);
}

$('.feedback_radio').on('click', function () {
    if ($('.radio_value').attr("checked") == "checked" && $("input[type=radio]:checked").attr('data-type') == 'success_call') {
        $('.mistakes').addClass('hidden');
    } else {
        $('.mistakes').removeClass('hidden');
    }
});

function sendFailedForm() {
    event.preventDefault();
    var orderId = $('.order_id').text();
    var ordersOpenedId = $(this).closest('tr').attr('orders-opened-id');
    var form = $(this);
    window.operatorId = $(this).closest('tr').attr('id');
    $.post('/ajax/feedback-ajax/' + orderId + '/' + window.operatorId + '/' + ordersOpenedId, $(this).serialize(), function (json) {
        if (json.success) {
            var feedback = form.parents('.feedback');
            form.parents('.feedback').empty();
            feedback.append(json.html);
            getMessage("success", json.message);
        } else {
            getMessage("error", json.message);
        }
    }, 'json').fail(function (json) {
        getMessage("error");
    });
}