$('a.print-selected').click(function () {
    event.preventDefault();
    var orders = [];
    var projects = [];
    var tracks = [];
    var statuses = [];
    var deliveries = [];
    $('.choose').each(function (e, val) {

        if ($(this).is(':checked')) {
            orders.push($(val).val());
            var procStatus = Number($(val).siblings('#proc_status').val());
            var project = Number($(val).siblings('#project_id').val());
            var track = Number($(val).siblings('#track').val());
            var delivery = Number($(val).siblings('#delivery').val());
            if (procStatus && procStatus !== 3) {
                statuses.push(procStatus);
            }
            if (!projects.includes(project)) {
                projects.push(project);
            }
            if (!track) {
                tracks.push(1);
            }
            if (delivery && !deliveries.includes(delivery)) {
                deliveries.push(delivery);
            }
            $('#ordersNumbers').val(orders);
        }
    });
    if (statuses.length > 0) {
        event.preventDefault();
        getMessage('warning', $('#print_error_message #approve_order').text());
        return false;
    } else if (!$('#authProjectId').val() && projects.length > 1) {
        event.preventDefault();
        getMessage('warning', $('#print_error_message #one_project').text());
        return false;
    }else if (deliveries.length > 1) {
        event.preventDefault();
        getMessage('warning', $('#print_error_message #one_delivery').text());
        return false;
    }else if (tracks.length > 0) {
        event.preventDefault();
        getMessage('warning', $('#print_error_message #must_track').text());
        return false;
    }

    $('#print-selected')
        .submit();
});
$('a.print-all').click(function () {
    event.preventDefault();
    $('#filters').val($('form.form').serialize());

    if ($('#status').val() == null || $('#status').val() != 3) {
        event.preventDefault();
        // getMessage('warning', 'Выберите только Подтвержденные заказы');
        return false;
    } else if ($('#authProjectId').val()) {
        event.preventDefault();
        // getMessage('warning', 'Вы можете выбрать только заказы со статусом Подтвержденные');
        return false;
    }

    $('#print-all')
        .submit();
});








