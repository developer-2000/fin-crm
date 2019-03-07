$(function () {
    $('#form_track').on('submit', addOrderByTrack);
    $('#filter').on('submit', searchOrders);
    initEditable();

    $('#pass_table').on('change', '#cost_return_all', changeTotalCostReturn);
    $('#searchResult').on('click', '.add_order',addOrder);
    $('#searchResult').on('click', '.add_order_pass_send',addOrderSend);

    $('#pass_table').on('change', '.change_input', changeValueCostReturn);

    $('#reset_form').on('click', resetForm);
});

function addOrderByTrack() {
    showMessage('processing');
    var btn = $(this).find('[type="submit"]');
    var currentForm =  $(this);
    disableButton(btn, true);
    $.post('/ajax/pass/add-order-by-track', $(this).serialize(), function (json) {
       if (json.success) {
           updatePassTable(json.html);
           showMessage('success', json.message);
           $(currentForm)[0].reset();
       } else {
           showMessage('error', json.message);
       }
        disableButton(btn);
    }).fail(function () {
        showMessage('error');
        disableButton(btn);
    });
    return false;
}

function savePass() {
    var btn = $(this).find('[type="submit"]');
    disableButton(btn, true);
    $.post('/ajax/pass/save', $(this).serialize(), function (json) {
        if (json.success) {
            showMessage('success', 'Данные сохранены');
        } else {
            showMessage('error', 'Данные не сохранены');
        }
        disableButton(btn);
    }).fail(function () {
        showMessage('error', 'Ошибка на сервере');
        disableButton(btn);
    });
    return false;
}

function savePassSending() {
    var btn = $(this).find('[type="submit"]');
    disableButton(btn, true);
    $.post('/ajax/pass/save-sending', $(this).serialize(), function (json) {
        if (json.success) {
            showMessage('success', 'Данные сохранены');
        } else {
            showMessage('error', 'Данные не сохранены');
        }
        disableButton(btn);
    }).fail(function () {
        showMessage('error', 'Ошибка на сервере');
        disableButton(btn);
    });
    return false;
}

function initEditable() {
    $('.delete_rank').editable({
        tpl : '',
        emptytext : '<span class="fa-stack "> ' +
        '<i class="fa fa-square fa-stack-2x"></i> ' +
        '<i class="fa fa-trash-o fa-stack-1x fa-inverse"></i> ' +
        '</span>',
        url : '/ajax/pass/order/delete',
        ajaxOptions : {
            type : 'post',
            dataType : 'json'
        },
        success : function (data, config) {
            if (data.success) {
                var parent = $('a[data-pk="' + data.id + '"]').parents('tr');
                parent.fadeOut(600);
                setTimeout(function () {
                    parent.remove();
                    updatePassTable(data.html);
                }, 600);
                showMessage('success', data.message)
            } else {
                showMessage('error', data.message);
            }

        },
        error : function (errors) {
            showMessage('error');
        }
    });

    $('.delete_order_send').editable({
        tpl : '',
        emptytext : '<span class="fa-stack "> ' +
        '<i class="fa fa-square fa-stack-2x"></i> ' +
        '<i class="fa fa-trash-o fa-stack-1x fa-inverse"></i> ' +
        '</span>',
        url : '/ajax/pass/order/delete-send',
        ajaxOptions : {
            type : 'post',
            dataType : 'json'
        },
        success : function (data, config) {
            if (data.success) {
                var parent = $('a[data-pk="' + data.id + '"]').parents('tr');
                parent.fadeOut(600);
                setTimeout(function () {
                    parent.remove();
                    updatePassTable(data.html);
                }, 600);
                showMessage('success', data.message)
            } else {
                showMessage('error', data.message);
            }

        },
        error : function (errors) {
            showMessage('error');
        }
    });
}

function addOrder() {
    var btn = $(this);
    $.post('/ajax/pass/order/add', {id : btn.attr('data-id'), pass_id : $('#pass_id').val()}, function (json) {
        if (json.success) {
            var parent = btn.parents('tr');
            parent.fadeOut(600);
            setTimeout(function () {
                parent.remove();
                closeSearchBlock();
            }, 600);
            showMessage('success', json.message);
            $('#filter')[0].reset();
            updatePassTable(json.html);

        } else {
            showMessage('error', json.message);
        }
    });
    return false;
}

function closeSearchBlock() {
    var block = $('#search_block'),
        tr = block.find('#searchResult tbody tr');

    if ((tr.length == 1 && $("#not_found").length) || tr.length == 0) {
        block.find('.close').click();
    }
}

function addOrderSend() {
    var btn = $(this);
    var parent = btn.parents('tr');
    var track = parent.find('.input_track').val();
    var cost_actual = parent.find('.input_cost_actual').val();
    $.post('/ajax/pass/order/add-send',
        {id : btn.attr('data-id'), pass_id : $('#pass_id').val(), track : track, cost_actual : cost_actual},
        function (json) {
        if (json.success) {
            parent.fadeOut(600);
            setTimeout(function () {
                parent.remove();
                closeSearchBlock();
            }, 600);
            showMessage('success', json.message);
            $('#filter')[0].reset();
            updatePassTable(json.html);

        } else {
            showMessage('error', json.message);
        }
    });
    return false;
}

function searchOrders() {
    var btn = $(this).find('[type="submit"]');
    $('#spinner').fadeIn(0);
    $('#searchResult').empty();
    disableButton(btn, true);
    $.post('/ajax/pass/orders/search', $(this).serialize(), function (json) {
        if (json.html) {
            $('#searchResult').append(json.html);

            closeSearchBlock();
        }
        disableButton(btn);
        $('#spinner').fadeOut(0);
    });

    return false;
}

function updatePassTable(html) {
    if (html) {
        $('#pass_table tbody').empty();
        $('#pass_table tbody').append(html);
        initEditable();
    }
}

function changeValueCostReturn() {
    var inputs = $('.change_input'),
        cost_actual = 0,
        sum = 0;
    var val = $(this).val();
    if (val > 0 && val) {
        if ((val && !(+val)) || (+val < 0)) {
            $(this).parent('td').addClass('has-error');
        } else {
            var data = {
                type: $(this).attr('data-type'),
                value: $(this).val(),
                id: $(this).attr('data-id'),
                pass_id: $(this).attr('data-pass')
            };
            $.post('/ajax/pass/orders/change', data, function (json) {
                if (json.success) {
                    showMessage('success', json.message);
                } else {
                    showMessage('error', json.message);
                }
            })
        }
    }
    $.each(inputs, function (index, obj) {
        var val = $(this).val();
        if ((val && (+val)) && (+val >= 0) && $(this).attr('data-type') == 'cost_return') {
            sum += +val;
        }

        if ((val && (+val)) && (+val >= 0) && $(this).attr('data-type') == 'cost_actual') {
            cost_actual += +val;
        }

    });

    $('#cost_return_all').val(sum);
    $('#sum_actual').text(cost_actual);
}

function changeTotalCostReturn() {
    var total = $(this).val(),
        inputs = $('input[name*="cost_return"].change_input'),
        avg = inputs.length ? total / inputs.length : total;

    $.each(inputs, function (index, obj) {
        $(obj).val(Math.round(avg * 100) / 100);
    });
}

function resetForm() {
    try {
        $('#filter')[0].reset();
    }catch (e) {
        console.log(e.message);
    }

    return false;
}