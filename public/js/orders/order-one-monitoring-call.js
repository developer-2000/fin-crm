function getMonitoringCompany(data) {
    var callList = $('#call_list tbody');
    var tableProc = $('#processing tbody');

    if (data.data) {
        var orderlist = {};
        var procList = {};

        function isInArray(array, search) {
            return array.indexOf(search) >= 0;
        }

        $.each(data.data, function (id, value) {
            if ($('span.order_id').text() == id) {
                $("div .moderator-block input").prop('disabled', true);
                $("div .moderator-block select").prop('disabled', true);
                $("div .slider-minmax").prop('disabled', true);

                if (value.status == 'Success' || value.status == 'Dialing') {

                    if (value.status == 'Success') {
                        value.status = 'Разговаривает';
                    }
                    if (value.status == 'Dialing') {
                        value.status = 'Набор номера';
                    }
                    if (value.user == data.operators[value.user]) {
                        var name = data.operators[value.user].name;
                        var surname = data.operators[value.user].surname;
                    }


                    var html = '';
                    var geo = '<img src="' + window.location.origin + '/img/flags/' + data.countries[value.crm_id].geo.toUpperCase() + '.png" />';

                    html += '<tr>';
                    html += '<td class="text-left"> ' +
                        '<div class="user_id">' +
                        value.user +
                        '</div> ' +
                        '<div class="name">' +
                        name +
                        '</div> ' +
                        '<div class="user_name">' +
                        surname +
                        '</div> ' +
                        '</td>';
                    html += '<td class="text-center">' + value.crm_id + '</td>';
                    html += '<td class="text-center country">' + geo + '</td>';
                    html += '<td class="text-center">' + value.phone + '</td>';
                    html += '<td class="text-center"><img src="http://crm.badvps.com/img/agent_oncall.png" alt="Говорит"></td>';
                    window.socketTime = value.time;
                    html += '<td class="text-center time" data-time="' + window.socketTime + '">' + getFormatTime(value.time) + '</td>';
                    html += '</tr>';
                    $('#processing tbody').empty();
                    $('#processing tbody').append(html);

                }
            }
            if (!isInArray(Object.keys(data.data), $('span.order_id').text())) {
                $('#processing tbody').empty();
                $("div .moderator-block input").prop('disabled', false);
                $("div .moderator-block select").prop('disabled', false);
                $("div .slider-minmax").prop('disabled', false);
            }
        });

        deleteRows(callList, orderlist, 'order_');
        deleteRows(tableProc, procList, 'proc_order_');
        deleteHeaderCompany(tableProc);
        deleteHeaderCompany(callList);
    }
}

var second = 0;
var newTime = 0;

function addSeconds() {
    newTime = window.socketTime;
    var getTime = Number(newTime) + 1;
    var showTime = getFormatTime(getTime);
    $('td.time').text(showTime);
    window.socketTime = getTime;
    setTimeout(addSeconds, 1000);

}

addSeconds();

function getFormatTime(sec, hour) {
    var date = new Date(sec * 1000);
    var seconds = date.getSeconds() + '';
    var minutes = date.getMinutes() + '';
    var hours = (date.getHours() - 2) + '';
    if (seconds.length == 1) {
        seconds = '0' + seconds;
    }
    if (minutes.length == 1) {
        minutes = '0' + minutes;
    }
    if (hours.length == 1) {
        hours = '0' + hours;
    }
    if (hour) {
        return hours + ':' + minutes + ':' + seconds;
    }
    return minutes + ':' + seconds;
}

function getDataSocket(data) {
    var data = jQuery.parseJSON(data);
    switch (data.key) {
        case ('getCompanyMonitoring') :
            getMonitoringCompany(data);
            break;
    }
}