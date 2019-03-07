$(function () {

    $('#groups').select2();
    $('#user').select2();
    $('#offer').select2();
    $('#project_select').select2();
    $('#country').select2();
    $('#source').select2();
    $('#term').select2();
    $('#medium').select2();
    $('#campaigns').select2();
    $('#content').select2();
    $('#company').select2();
    $('#trunk').select2();
    $('#proc_status').select2();
    $('#moderator').select2();
    $('#operator').select2();
    // $('#product').select2();
    //  $('#sub_project').select2();

    $('#time_created').tooltip();
    $('#time_modified').tooltip();

    $('#project').select2({
        placeholder: "",
        minimumInputLength: 0,
        multiple: false,
        ajax: {
            method: 'get',
            url: '/projects/find',
            dataType:
                'json',
            data:
                function (params) {
                    return {
                        q: $.trim(params)
                    };
                },
            results: function (data) {
                data.unshift({id: "", text: "Все"})
                return {
                    results: data,
                    "pagination": {
                        "more": true
                    }
                };
            }
        },
    });

    if ($('#project').attr('data-project')) {
        $("#project").select2('data', JSON.parse($('#project').attr('data-project'))[0]);
    }

    $('#sub_project').select2({
        placeholder: "",
        minimumInputLength: 0,
        multiple: false,
        ajax: {
            method: 'get',
            url: '/sub_projects/find',
            dataType:
                'json',
            data:
                function (params) {
                    return {
                        q: $.trim(params),
                        project_id: $('#project').val().split(",")
                    };
                },
            results: function (data) {
                data.unshift({id: "", text: "Все"})
                return {
                    results: data,
                    "pagination": {
                        "more": true
                    }
                };
            }
        },
    });
    if ($('#sub_project').attr('data-sub_project')) {
        $("#sub_project").select2('data', JSON.parse($('#sub_project').attr('data-sub_project'))[0]);
    }

    $('#divisions').select2({
        minimumInputLength: 0,
        multiple: false,
        ajax: {
            method: 'get',
            url: '/divisions/find',
            dataType:
                'json',
            data:
                function (params) {
                    return {
                        query: $.trim(params),
                        project_id: $('#project').val() ? $('#project').val() : [],
                        sub_project_id: $('#sub_project').val() ? $('#sub_project').val() : []
                    };
                },
            results: function (data) {
                return {
                    results: data,
                    "pagination": {
                        "more": true
                    }
                };
            }
        },
    });

    if ($('#divisions').attr('data-divisions')) {
        $("#divisions").select2('data', JSON.parse($('#divisions').attr('data-divisions'))[0]);
    }

    $('#offers').select2({
        placeholder: "",
        minimumInputLength: 0,
        multiple: false,
        ajax: {
            method: 'get',
            url: '/offer/find',
            dataType:
                'json',
            data:
                function (params) {
                    return {
                        q: $.trim(params),
                        partner_id: $('#partners').length ? $('#partners').val().split(",") : [],
                        allowWithoutPtoject: true
                    };
                },
            results: function (data) {
                data.unshift({id: "", text: "Все"})
                return {
                    results: data,
                    "pagination": {
                        "more": true
                    }
                };
            }
        },
    });
    if ($('#offers').attr('data-offers')) {
        $("#offers").select2('data', JSON.parse($('#offers').attr('data-offers'))[0]);
    }

    $('#product').select2({
        placeholder: "",
        minimumInputLength: 0,
        multiple: false,
        ajax: {
            method: 'get',
            url: '/product/find',
            dataType:
                'json',
            data:
                function (params) {
                    return {
                        q: $.trim(params),
                        project_id: $('#project').val().split(","),
                        sub_project: $('#sub_project').val().split(",")
                    };
                },
            results: function (data) {
                data.unshift({id: "", text: "Все"})
                return {
                    results: data,
                    "pagination": {
                        "more": true
                    }
                };
            }
        },
    });
    if ($('#product').attr('data-product')) {
        $("#product").select2('data', JSON.parse($('#product').attr('data-product'))[0]);
    }

    //$('#offer').select2();

    // $('#offer').select2({
    //     placeholder: 'Все',
    //     allowClear: true,
    // });

    if ($('#order_table tr').length > 1) {
        $('#order_table').tablesorter({sortList: [[1, 1]]});
    }

    $('.table_country').each(function () {
        if ($(this).find('tr').length > 1) {
            $(this).tablesorter({sortList: [[1, 1]]});
        }
    })

    $('#data_by_projects').each(function () {
        if ($(this).find('tr').length > 1) {
            $(this).tablesorter({sortList: [[1, 1]]});
        }
        if ($(this).find('tr doj').length > 1) {
            $(this).deleteClass('doj');
        }
    })

    $('#data_by_collectors').each(function () {
        if ($(this).find('tr').length > 1) {
            $(this).tablesorter({sortList: [[1, 1]]});
        }
    })


    /**
     * Календарик
     */
    myDatepicker($('#date_start'));
    myDatepicker($('#date_end'));

    /**
     * Фильтр
     */
    $('.filter').on('click', filter);

    // $('#date_template').on('change', function(e) {
    //
    //     var obj = $(e.currentTarget);
    //     var dateStartObj = $('#date_start');
    //     var dateEndObj = $('#date_end');
    //     var type = obj.val();
    //     $.post('/date-filter-template-ajax/', {type: type}, function (json) {
    //         dateStartObj.val(json.start);
    //         dateEndObj.val(json.end);
    //     }, 'json');
    //
    // });

    $('.zeroButton').on('click', function (e) {
        var code = $(e.currentTarget).attr('vendor_code');
        $.post('/zero-call-count/' + code, {}, function (json) {
            if (json.error == true) {
                alert('Аннулирвано')
            } else {
                alert('Произошла ошибка')
            }
        }, 'json');
    });

    $('#date_template :radio').on('change', function (e) {
        var obj = $(e.currentTarget);
        var dateStartObj = $('#date_start');
        var dateEndObj = $('#date_end');
        var type = obj.val();
        if (type == 11) {
            dateStartObj.removeAttr('disabled');
            dateEndObj.removeAttr('disabled');
            return false;
        }
        if (type == 0) {
            dateStartObj.val('');
            dateEndObj.val('');
            return false;
        } else {
            $.post('/date-filter-template-ajax/', {type: type}, function (json) {
                dateStartObj.val(json.start.split(' ')[0]);
                dateEndObj.val(json.end.split(' ')[0]);
            }, 'json');
        }
    });

    $('#date_template_statuses :radio').on('change', function (e) {
        var obj = $(e.currentTarget);

        var dateStartObj = $('#date_start');
        var dateEndObj = $('#date_end');
        var type = obj.val();
        if (type == 11) {
            dateStartObj.removeAttr('disabled');
            dateEndObj.removeAttr('disabled');
            return false;
        }
        if (type == 0) {
            dateStartObj.val('');
            dateEndObj.val('');
            return false;
        } else {
            $.post('/date-filter-template-ajax/', {type: type}, function (json) {
                dateStartObj.val(json.start.split(' ')[0].replace(/\./g, '-'));
                dateEndObj.val(json.end.split(' ')[0].replace(/\./g, '-'));
                $('input[name="daterange"]').val(json.start.split(' ')[0].replace(/\./g, '-') +
                    ' - ' + json.end.split(' ')[0].replace(/\./g, '-'));

            }, 'json');
        }
    });

    $('#product_stat tbody tr').on('click', function () {
        $('#product_stat tbody tr.click').removeClass('click');
        $(this).addClass('click');
    })
});

/**
 * Календарик
 */
function myDatepicker(obj) {
    var start = new Date();
    obj.datepicker({
        language: 'ru',
        startDate: start,
        onSelect: function (fd, d, picker) {
            if (!d) {
                return;
            }
        }
    });
}

/**
 * Фильтр
 */
function filter(e) {
    var user = $('#user').val();
    var dateStart = $('#date_start').val();
    var dateEnd = $('#date_end').val();
    var url = location.origin + location.pathname + '?date=' + dateStart + '-' + dateEnd;
    if (user != 0) {
        url = url + '&' + 'user=' + user;
    }
    location.href = url;
    // alert('1');
    // var data = {};
    // data['user'] = $('#user').val();
    // data['owner'] = $('#owner').val();
    // data['country'] = $('#countries').val();
    // data['dateStart'] = $('#date_start').val();
    // data['dateEnd'] = $('#date_end').val();
    // $.post('/account-filter-ajax/', {data: data}, function(json) {
    //     if (json.url) {
    //         location.href = json.url;
    //     }
    // }, 'json');
}