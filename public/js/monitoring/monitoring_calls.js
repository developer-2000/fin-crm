function getDataSocket(data) {
    var data = jQuery.parseJSON(data);
    var cell = '';
    switch (data.key) {
        case ('getOrdersInProcessing') :
            cell = 'proc_crm';
            break;
        case ('getCallsNoProcessing') :
            cell = 'proc_elastix';
            break;
        case ('getCallCount') :
            cell = 'call_today';
            break;
        case ('getOrderToday') :
            cell = "new_order";
            break;
        case ('getCallProcessing') :
            cell = 'processing';
            break;
        case ('getCallDialing') :
            cell = 'dialing';
            break;
        case ('getCountBreakAgents') :
            cell = 'oper_break';
            break;
        case ('getCountAgentsToday') :
            cell = 'oper_today';
            break;
        case ('getQueuesDetails') :
            cell = 'oper_all';
            break;
        case ('getCountAgents') :
            cell = 'oper_online';
            break;
    }
    updateCell(data, cell);
}

function transparent(cell) {
    setTimeout(function () {
        cell.css('background-color', 'transparent');
    },500)
}

function updateCell(data, nameCell) {
    $.each(data.data, function (id, value) {
        var cell = $('#' + value.id_campaign + ' .' + nameCell);
        if (cell.text() != value.count) {
            cell.text(value.count).css('background-color', '#FF7A62');
            transparent(cell);
        }
    });
}
