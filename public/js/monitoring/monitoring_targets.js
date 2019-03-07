$(function() {
    window.flag = true;
    setInterval(function () {
        if (window.flag) {
            getOrders();
        }
    },1000)
});

function getOrders() {
    $.post('/monitoring-targets-ajax', {}, function (json) {
        window.flag = false;
        if (json.orders) {
            $.each(json.orders, function () {
                var order = this;
                var id = order.id + '_' + order.target_status + '_' + order.proc_status;
                var oldTr = $('#' + id);
                if (!oldTr.length) {
                    var classTr = '';
                    if (order.proc_status == 3) {
                        classTr = 'info';
                    }
                    if (order.target_status == 1) {
                        classTr = 'success';
                    }
                    if (order.target_status == 2) {
                        classTr = 'danger';
                    }
                    if (order.target_status == 3) {
                        classTr = 'warning';
                    }
                    if (order.proc_callback_time != 0) {
                        classTr = 'active';
                    }
                    if (order.proc_status == 7) {
                        classTr = 'info';
                    }
                    var targetFinal = '';
                    if((Array.isArray(order.target_final)) && (order.target_status == 1 || order.target_status == 3)) {
                        if (order.target_status == 1) {
                            targetFinal += '<h4>Подтвержден</h4>';
                        } else {
                            targetFinal += '<h4>Аннулирован</h4>';
                        }
                        for ( var j = 0; j < order.target_final.length; j++) {
                            targetFinal += '<div>' + order.target_final[j].text +' </div> ';
                        }
                    } else if (order.target_final && order.target_status == 2) {
                        targetFinal += '<h4>Отказ</h4>' +
                            'Прична отказа - ' + order.target_final;
                    } else if (order.proc_callback_time != 0) {
                        targetFinal += '<h4>Перезвонить</h4>' + order.proc_callback_time;
                    } else  if (order.proc_status) {
                        if (order.proc_status == 3) {
                            targetFinal += '<h4>Заказ открылся</h4>';
                        }
                        if (order.proc_status == 7) {
                            targetFinal += '<h4>Говорит на другом языке</h4>';
                        }
                    }
                    var offer = order.offer ? order.offer : '';
                    var product = '<b>' + offer + '</b><br>';
                    var img = '<img src="' + window.location.origin + '/img/flags/' + order.geo.toUpperCase() + '.png">',
                        name = order.name ? order.name : '',
                        surname = order.surname ? order.surname : '';
                    $.each(order.products, function () {
                        product += this + '<br>';
                    });
                    var html = '<tr id="' + id + '" class="' + classTr + '"> ' +
                        '<td class="text-center">' + order.time_modified  + '</td>' +
                        '<td class="text-center">' + order.id + '</td> ' +
                        '<td>' + name + ' ' + surname + '</td>' +
                        '<td class="text-center">' + img + '</td> ' +
                        '<td class="text-center">' + product + '</td> ' +
                        '<td class="text-center">' + targetFinal + '</td>' +
                        '<td>' +
                        '<a href="' + window.location.origin + '/orders/' + order.id + '" class="table-link">'+
                        '<span class="fa-stack">' +
                        '<i class="fa fa-square fa-stack-2x"></i>' +
                        '<i class="fa fa-long-arrow-right fa-stack-1x fa-inverse"></i>' +
                        '</span>' +
                        '</a>' +
                        '</td>' +
                        '</tr>' ;
                    $('#orders > tbody').prepend(html);
                    $('#' + id).css('display', 'none');
                    $('#' + id).fadeIn('slow')
                }

            });
            var tr = $('#orders > tbody > tr');
            if (tr.length > 100) {
                var countOldtr = tr.length - 100;
                for (var i = 1; i <= countOldtr; i++) {
                    tr.eq(100 + i).remove();
                }
            }
        }
        window.flag = true;
    });
}