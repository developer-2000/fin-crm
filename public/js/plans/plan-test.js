$(function () {
    $('form').on('submit', update);
    $('#random-order').on('change', getAllOrdersNotApproved);
});

function getAllOrdersNotApproved() {
    if($(this).is(':checked')){
        $('.orders-options').empty();
        var inputs = $('#random-order-block').clone().html();
        $('.orders-options').append(inputs);
    }
   else{
        $('.orders-options').empty();
        var inputs = $('#orders-all').clone().html();
        $('.orders-options').append(inputs);
    }
}
function update() {

    $(this).find('.has-error').removeClass('has-error');
    $.post(location.pathname, $(this).serialize(), function (json) {
        if (json.errors) {
            for (key in json.errors) {
                $('#' + key).parents('.form-group').addClass('has-error');
            }
        }
        if (json) {
            if (json.success) {
                getMessage('success', "Тестовая транзакция успешно проведена!");
                $('.plan_log_block').empty();
                $('.plan_log_block').append(json.success.html);
                $('.plan_log_block').css('display', 'block');
            } else {
                getMessage('error', "Transaction N/A!");
            }
        }
    });

    return false
}
/**
 * Выводим сообщение
 */
function getMessage(type, message) {
    $('.ns-box').remove();
    var notification = new NotificationFx({
        message: '<span class="icon fa fa-bullhorn fa-2x"></span><p>' + message + '</p>',
        layout: 'bar',
        effect: 'slidetop',
        type: type,
        ttl: 3000,
    });

    notification.show();
}