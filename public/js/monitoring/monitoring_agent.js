$(function() {
    getMonitoringAgents();
    setInterval(function() { 
        getMonitoringAgents();
    }, 1000);
});

function getMonitoringAgents() {
    $.post('/get-monitoring-agents-ajax/', {}, function(json) {  
        if (json.data) { 
            $('#agents .operator_tr').hide();
            $.each(json.data, function(id, value) {
                var statusTime = value.status_time != undefined ? value.status_time : ''; 
                var img;
                switch (value.agentstatus) {
                    case 'online': {
                        img = '<img src="http://crm.badvps.com/img/agent_online.png" alt="Онлайн">';
                        break;    
                    }
                    case 'paused': {
                        img = '<img src="http://crm.badvps.com/img/agent_paused.png" alt="Пауза">';
                        break;
                    }
                    case 'oncall': {
                        img = '<img src="http://crm.badvps.com/img/agent_oncall.png" alt="Говорит">';
                        break;
                    }
                    default: {
                        img = '';
                        break;
                    }
                }
                $('#agents #' + id).show();
                $('#status-' + id).html(img + ' ' + statusTime);
                $('#calls-' + id).text(value.num_calls);
                $('#time-login-' + id).text(value.logintime);
                $('#time-talk-' + id).text(value.break_total);
                $('#break-pause-' + id).text(value.break_pause);
                $('#break-order-' + id).text(value.break_order);
                $('#break-total-' + id).text(value.break_total); 
            });  
        }
    }, 'json');
}