$(function () {
    $('#delivery_block').find('input, select, textarea').each(function (id,obj) {
        $(obj).attr('readonly', true);
    });

    $('#save_locked_order').on('click', saveStatusForLocked);
});

function cancelSend() {
    disableButton($(this), true);
    $.post('/ajax/orders/cancel-send', {id : $(this).attr('data-id')}, function (json) {
        if (json.success) {
            location.reload();
        } else {
            getMessage('error', 'Произошла ошибка!');
        }
        disableButton($(this));
    });
}

function saveStatusForLocked() {
    disableButton($(this), true);
    $.post('/ajax/save-status-for-locked-order', {procStatusId : $('#procStatusNew').val(),
    orderId: $('.order_id').text()}, function (json) {
        if (json.success) {
            location.reload();
        } else if(json.current_status) {
            getMessage('warning', 'Выбран текущий статус!');
        } else {
            getMessage('error', 'Произошла ошибка!');
        }
        disableButton($(this));
    });
}