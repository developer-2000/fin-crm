$(function () {
    /**
     * Календарик
     */
    myDatepicker($('#period_start'));
    myDatepicker($('#period_end'));

    $('#unlock').on('click', getTransaction);

    $("#pay").on('click', getInputs);
    
    $('form').on('submit', addPayout)
});

function addPayout() {
    $(this).find('has-error').removeClass('has-error');
    $.post( location.href , $(this).serialize(), function (json) {
        $('.col-sm-6').find('.has-error').removeClass('has-error');
        if (json.errors) {
            for (key in json.errors) {
                $('#' + key).parents('.form-group').addClass('has-error');
            }
            getMessage('error', 'Ошибка');
        }
        if(json.status) {
            getMessage('success', 'Данные изменены');
        }
    })
    return false;
}

function getTransaction() {
    var start = $('#period_start').val();
    var end = $('#period_end').val();
    var id = $(this).attr('data-id');
    $.post('/get-info-transaction-ajax/' + id, {date_start: start, date_end: end}, function (json) {
        if (json.html) {
            $('#table').empty();
            $('#table').append(json.html);
            $('.block_price').hide();
        }
    });
    return false;
}

function getInputs() {
    var price = $("#allPrice").text();
    $('.block_price').show();
    $('#valuation').val(parseInt(price));

}

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
