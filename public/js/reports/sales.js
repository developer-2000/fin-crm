$(function () {
    var today =moment(new Date).format('DD-MM-YYYY');
    console.log( $('#date_start').val());
    console.log(today);

    $('input[name="daterange"]').daterangepicker({
        opens: 'left',
        startDate: $('#date_start').val() ? $('date_start').val() : today,
        endDate: $('#date_end').val() ? $('date_end').val() : today,
        locale: {
            "format": "DD-MM-YYYY",
            "separator": " - ",
            "applyLabel": "Apply",
            "cancelLabel": "Cancel",
            "fromLabel": "From",
            "toLabel": "To",
            "customRangeLabel": "Custom",
            "weekLabel": "W",
            "daysOfWeek": [
                "Вс",
                "Пн",
                "Вт",
                "Ср",
                "Чт",
                "Пт",
                "Сб"
            ],
            "monthNames": [
                "Январь",
                "Февраль",
                "Март",
                "Апрель",
                "Май",
                "Июнь",
                "Июль",
                "Август",
                "Сентябрь",
                "Октябрь",
                "Ноябрь",
                "Декабрь"
            ],
            "firstDay": 1
        }
    }, function (start, end, label) {
        $('#date_start').val(start.format('DD-MM-YYYY'));
        $('#date_end').val(end.format('DD-MM-YYYY'));
        console.log("A new date selection was made: " + start.format('DD-MM-YYYY') + ' to ' + end.format('DD-MM-YYYY'));
    });

    /*
   *Стилизация select
    */
    $('#status').select2({
        placeholder: 'Все',
        allowClear: true
    });

    if ($('.table_sales tr.tr_sales').length > 1) {
        $('.table_sales').tablesorter({sortList: [[0, 1]]});
    }

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
                        project_id: $('#project').val().split(",")
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

    if ($('#product').attr('data-product')) {
        $("#product").select2('data', JSON.parse($('#product').attr('data-product'))[0]);
    }

    $('#date_template :radio').on('change', getDataTemplate);
    function getDataTemplate(e) {
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
    }
});