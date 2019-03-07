$(function() {
});

function getMonitoringCompany(data) {
        var callList = $('#call_list tbody');
        var tableProc = $('#processing tbody');
        if (data.data) {
            var orderlist = {};
            var procList = {};
            $.each(data.data, function(id, value) {
                var elastix_company = value.elastix_company;
                if (!elastix_company || elastix_company == 0) {
                    return;
                }
                if (value.status != 'Success') {
                    orderlist[value.crm_id] = value.crm_id;
                    var title_campaign = callList.find('.campaign_' + elastix_company);
                    var campaigns = $('#campaign_' + elastix_company).text();
                    var statusTime = getFormatTime(value.time);
                    if (!title_campaign.length) {
                        callList.append('<tr class="campaign_' + elastix_company + '"> <td class="text-center" colspan="5">' + campaigns + '</td> </tr>')
                    }
                    if (value.status == 'Placing') {
                        value.status = 'Размещение';
                    }
                    if (value.status == 'Dialing') {
                        value.status = 'Набор номера';
                    }
                    if (value.status == 'Ringing') {
                        value.status = 'Вызов';
                    }
                    if (value.status == 'OnQueue') {
                        value.status = 'В очереди';
                    }
                    if (value.trunk == null) {
                        value.trunk = '';
                    }

                    if (!$('#order_' + value.crm_id).length) {
                        var html = '';
                        html += '<tr id="order_' + value.crm_id + '" data-id="' + value.crm_id + '" class="order_campaign_' + elastix_company + '">';
                        html += '<td class="text-center">' + value.crm_id +'</td>';
                        html += '<td class="text-center">' + value.status +'</td>';
                        html += '<td class="text-center">' + value.phone +'</td>';
                        html += '<td class="text-center">' + value.trunk +'</td>';
                        html += '<td class="text-center time" >' + statusTime +'</td>';
                        html += '</tr>';
                        title_campaign.after(html);
                    } else {
                        $('#call_list #order_' + value.crm_id).find('.time').text(statusTime);
                    }
                } else {
                    procList[value.crm_id] = value.crm_id;
                    var title_campaign = tableProc.find('.campaign_' + elastix_company);
                    var campaigns = $('#campaign_' + elastix_company).text();
                    var statusTime = getFormatTime(value.time);
                    var surname_name = $('#oper_' + value.user).text();
                    var geo = '';
                    var target_company = parseInt($('#target_user_company').text());
                    var company_order = parseInt($('#oper_' + value.user).attr('company_id'));
                    if (target_company) {
                        if (target_company == company_order) {
                            if (typeof(data.countries) != "undefined") {
                                if (typeof(data.countries[value.crm_id]) != "undefined") {
                                    geo = '<img class="country-flag" src="' + window.location.origin + '/img/flags/' + data.countries[value.crm_id].geo.toUpperCase() + '.png" />';
                                }
                            }
                            if (!title_campaign.length) {
                                tableProc.append('<tr class="campaign_' + elastix_company + ' " data-id="' + elastix_company + '"> <td class="text-center name_campaign" colspan="6">' + campaigns + '</td> </tr>')
                            }

                            if (!$('#proc_order_' + value.crm_id).length) {
                                var html = '';
                                html += '<tr id="proc_order_' + value.crm_id + '" data-id="' + value.crm_id + '" class="order_campaign_' + elastix_company + '">';
                                html += '<td class="text-left"> ' +
                                    '<div class="user_id">' +
                                    value.user +
                                    '</div> ' +
                                    '<div class="user_name">' +
                                    surname_name +
                                    '</div> ' +
                                    '</td>';
                                html += '<td class="text-center">' + value.crm_id + '</td>';
                                html += '<td class="text-center country">' + geo + '</td>';
                                html += '<td class="text-center">' + value.phone + '</td>';
                                html += '<td class="text-center"><img src="http://crm.badvps.com/img/agent_oncall.png" alt="Говорит"></td>';
                                html += '<td class="text-center time">' + getFormatTime(value.time) + '</td>';
                                html += '</tr>';
                                title_campaign.after(html);
                            } else {
                                $('#proc_order_' + value.crm_id).find('.time').text(statusTime);
                            }
                        }
                    } else {
                        if (typeof(data.countries) != "undefined") {
                            if (typeof(data.countries[value.crm_id]) != "undefined") {
                                geo = '<img class="country-flag" src="' + window.location.origin + '/img/flags/' + data.countries[value.crm_id].geo.toUpperCase() + '.png" />';
                            }
                        }
                        if (!title_campaign.length) {
                            tableProc.append('<tr class="campaign_' + elastix_company + ' " data-id="' + elastix_company + '"> <td class="text-center name_campaign" colspan="6">' + campaigns + '</td> </tr>')
                        }

                        if (!$('#proc_order_' + value.crm_id).length) {
                            var html = '';
                            html += '<tr id="proc_order_' + value.crm_id + '" data-id="' + value.crm_id + '" class="order_campaign_' + elastix_company + '">';
                            html += '<td class="text-left"> ' +
                                '<div class="user_id">' +
                                value.user +
                                '</div> ' +
                                '<div class="user_name">' +
                                surname_name +
                                '</div> ' +
                                '</td>';
                            html += '<td class="text-center">' + value.crm_id + '</td>';
                            html += '<td class="text-center country">' + geo + '</td>';
                            html += '<td class="text-center">' + value.phone + '</td>';
                            html += '<td class="text-center"><img src="http://crm.badvps.com/img/agent_oncall.png" alt="Говорит"></td>';
                            html += '<td class="text-center time">' + getFormatTime(value.time) + '</td>';
                            html += '</tr>';
                            title_campaign.after(html);
                        } else {
                            $('#proc_order_' + value.crm_id).find('.time').text(statusTime);
                        }
                    }
                }
            });

            deleteRows(callList, orderlist, 'order_');
            deleteRows(tableProc, procList, 'proc_order_');
            deleteHeaderCompany(tableProc);
            deleteHeaderCompany(callList);
        }
}

function deleteHeaderCompany(table) {
    var headersCompany = table.find('.name_campaign');
    $.each(headersCompany, function (id, value) {
        var tr = $(value).parent();
        var orders = table.find('.order_campaign_' + tr.attr('data-id'));
        if (!orders.length) {
            tr.remove();
        }
    })
}

function getAgent(data) {
    var tableProc = $('#processing tbody');
    if (data.data) {
        $.each(data.data, function(id, value) {
            var name = $('#oper_' + value.agent).text();
            var elastix_company = $('#oper_' + value.agent).attr('data-id') ? $('#oper_' + value.agent).attr('data-id') : '';
            if (!elastix_company || elastix_company == 0) {
                return;
            }
            var title_campaign = tableProc.find('.campaign_' + elastix_company);
            var campaigns = $('#campaign_' + elastix_company).text();
            var statusTime = "00:00";

            var breakType = '<img src="' + window.location.origin + '/img/agent_online.jpg" alt="Ждун" title="Ждун">';

            if (value.id_break) {
                breakType = '<img src="' + window.location.origin + '/img/agent_paused.png" alt="Перерыв" title="Перерыв">';
                statusTime = value.datetime_init;
            }



            if (!title_campaign.length) {
                tableProc.append('<tr class="campaign_' + elastix_company + '" data-id="' + elastix_company + '"> <td class="text-center name_campaign" colspan="6">' + campaigns + '</td> </tr>')
            }
            if (!$('#user_' + value.agent).length) {
                var html = '';
                html += '<tr id="user_' + value.agent + '" data-id="' + value.agent + '" class="order_campaign_' + elastix_company + '">';
                html += '<td class="text-left" colspan="4"> ' +
                    '<span   class="user_id" >' +
                    value.agent +
                    '</span> ' +
                    '<span class="user_name" >' +
                    name +
                    '</span> ' +
                    '</td>';
                html += '<td class="text-center">' + breakType + '</td>';
                html += '<td class="text-center time">' +statusTime +'</td>';
                html += '</tr>';
                title_campaign.after(html);
            } else {
                $('#user_' + value.agent).find('.time').text(statusTime)
            }
        });


        deleteRows(tableProc, data.data, 'user_');
        deleteHeaderCompany(tableProc);
    }
}

function getFormatTime(sec, hour) {
    var date = new Date(sec * 1000);
    var seconds = date.getSeconds() + '';
    var minutes = date.getMinutes() + '';
    var hours = (date.getHours()  - 2) + '';
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


function deleteRows(table, data, elemId) {
    var rows = table.find('tr');
    for (var i = 0; i < rows.length; i++) {
        var id = rows.eq(i).attr('data-id');
        if (typeof(data[id]) == "undefined" && id) {
            $('#' + elemId + id).remove();
        }
    }
}

function getDataSocket(data) {
    var data = jQuery.parseJSON(data);
    switch (data.key) {
        case ('getBreakAgents') :
            getAgent(data);
            break;
        case ('getCompanyMonitoring') :
            getMonitoringCompany(data);
            break;
    }

}