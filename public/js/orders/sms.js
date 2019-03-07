
$('#template').change(function () {
    $.ajax({
        type: 'post',
        url: '/ajax/get-sms-template/' + Number($(this).val()),
        cache: false,
        contentType: false,
        processData: false,
        success: function (json) {
            $('#message').val(json.text);
            var old = $('#message').val();
            var track = $('#track').val();
            if(!track){
                track = document.getElementById('approve[track]') ? document.getElementById('approve[track]').value : '';
            }
            if(!$('#poshta').val()){
                var poshta = $('#integration').find(':selected').text();
            }else{
                 poshta = $("input[name=target_approve]").find(':selected').text();
            }

            var news = old
                .replace(/@order/g, $('.order_id').text())
                .replace(/@track/g, track)
                .replace(/@surname/g, $('#surname').val().trim())
                .replace(/@name/g, $('#name').val().trim())
                .replace(/@middle/g, $('#middle').val().trim())
                .replace(/@phone/g, $('#phone').val())
                .replace(/@poshta/g, poshta)
                .replace(/@postal_code/g, document.getElementById('approve[postal_code]') ? document.getElementById('approve[postal_code]').value : '')
                .replace(/@region/g, document.getElementById('approve[region]') ? document.getElementById('approve[region]').value : '')
                .replace(/@district/g, document.getElementById('approve[district]') ? document.getElementById('approve[district]').value : '')
                .replace(/@locality/g, document.getElementById('approve[locality]') ? document.getElementById('approve[locality]').value : '')
                .replace(/@street/g, document.getElementById('approve[street]') ? document.getElementById('approve[street]').value : '')
                .replace(/@house/g, document.getElementById('approve[house]') ? document.getElementById('approve[house]').value : '')
                .replace(/@flat/g, document.getElementById('approve[flat]') ? document.getElementById('approve[flat]').value : '')
            ;

            $('#message').val(news);
        }
    });
});

$('form#sms-send').submit(function () {
    $.post('/ajax/send-sms', $(this).serialize() + '&' + $.param({orderId: $('.order_id').text()}), function (json) {

        if (json.success) {
            $('div#sms-send').removeClass('md-show');
            getMessage('success', json.message);
            if(json.htmlSms){
                var smsBlock = $('#sms_comment_block');
                smsBlock.empty();
                smsBlock.prepend(json.htmlSms);
            }
        }
        else if(json.not_sended) {
            $('div#sms-send').removeClass('md-show');
            getMessage('error', json.message);
        }
    }).fail(function (json) {
        var errors = [];
        $.each(JSON.parse(json.responseText).errors, function (e, obj) {
            errors.push(obj + '<br>');
        });
        getMessage('error', errors);
    });
    return false;
});

/**
 * Выводим сообщение
 */
function getMessage(type, message) {
    $('.ns-box').remove();
    if (type === 'wait') {
        var notification = new NotificationFx({
            message: '<span class="fa fa-spinner fa-2x alert_spinner"></span><p>' + message + '</p>',
            layout: 'bar',
            effect: 'slidetop',
            type: 'notice',
            ttl: 60000,
        });
    } else {
        var notification = new NotificationFx({
            message: '<span class="icon fa fa-bullhorn fa-2x"></span><p>' + message + '</p>',
            layout: 'bar',
            effect: 'slidetop',
            type: type,
            ttl: 3000,
        });
    }
    notification.show();
}