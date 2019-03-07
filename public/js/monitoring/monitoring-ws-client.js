webSocket();


function webSocket() {
    var conn = new WebSocket('ws://' + window.location.host + ':5151');
    conn.onopen = function (p1) {
    };
    conn.onclose = function (p1) {
        setTimeout(function(){webSocket()}, 5000);
    };
    conn.onmessage = function (mes) {
        try {
            var data = jQuery.parseJSON(mes.data);
            if (data.key == 'logout' && data.data == window.targetUserId) {
                logout();
            } else {
                getDataSocket(mes.data);
            }
        } catch (err) {
        }
    };
}