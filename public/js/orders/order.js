$(function () {
    /*
   *Стилизация select
    */
    $('#status, #country, #company, #mistake_type, #moderator, #group, #user, #target, #partners, #sub_status, #deliveries, #cause_cancel').select2({
        placeholder: $(this).data('placeholder'),
        allowClear: true,
    });

    $('#project').select2({
        minimumInputLength: 0,
        multiple: true,
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
        var arrayForSelect2 = [];
        $.each(JSON.parse($('#project').attr('data-project')), function (element, value) {
            arrayForSelect2.push(value);
        });
        $("#project").select2('data', arrayForSelect2);
    }

    $('#sub_project').select2({
        minimumInputLength: 0,
        multiple: true,
        ajax: {
            method: 'get',
            url: '/sub_projects/find',
            dataType:
                'json',
            data:
                function (params) {
                    return {
                        query: $.trim(params),
                        project_id: $('#project').val() ? $('#project').val().split(",") : ''
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
        var arrayForSelect2 = [];
        $.each(JSON.parse($('#sub_project').attr('data-sub_project')), function (element, value) {
            arrayForSelect2.push(value);
        });
        $("#sub_project").select2('data', arrayForSelect2);
    }

    $('#divisions').select2({
        minimumInputLength: 0,
        multiple: true,
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

    if ($('#divisions').attr('data-division')) {
        var arrayForSelect2 = [];
        $.each(JSON.parse($('#divisions').attr('data-division')), function (element, value) {
            arrayForSelect2.push(value);
        });
        $("#divisions").select2('data', arrayForSelect2);
    }

    $('#offers').select2({
        minimumInputLength: 0,
        multiple: true,
        ajax: {
            method: 'get',
            url: '/offer/find',
            dataType:
                'json',
            data:
                function (params) {
                    return {
                        q: $.trim(params),
                        partner_id: $('#partners').val() ? $('#partners').val() : [],
                        allowWithoutPtoject : true
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
    if ($('#offers').attr('data-offers')) {
        var arrayForSelect2 = [];
        $.each(JSON.parse($('#offers').attr('data-offers')), function (element, value) {
            arrayForSelect2.push(value);
        });
        $("#offers").select2('data', arrayForSelect2);
    }

    $('#product').select2({
        minimumInputLength: 0,
        multiple: true,
        ajax: {
            method: 'get',
            url: '/product/find',
            dataType:
                'json',
            data:
                function (params) {
                    return {
                        q: $.trim(params),
                        project_id: $('#project').val() ? $('#project').val().split(",") : '',
                        sub_project: $('#sub_project').val() ? $('#sub_project').val().split(",") : ''
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
        var arrayForSelect2 = [];
        $.each(JSON.parse($('#product').attr('data-product')), function (element, value) {
            arrayForSelect2.push(value);
        });
        $("#product").select2('data', arrayForSelect2);
    }

    //tags
    $('.tag').select2({
        minimumInputLength: 0,
        multiple: true,
        ajax: {
            method: 'get',
            url: '/tags/find',
            dataType:
                'json',
            data:
                function (params) {
                    return {
                        query: $.trim(params),
                        partner_id: $('#partners').val() ? $('#partners').val() : '',
                        tag_name: $(this).attr('id') ? $(this).attr('id') : ''
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

    var tags = [
        'tag_medium',
        'tag_source',
        'tag_term',
        'tag_content',
        'tag_campaign',
    ];

    jQuery.each(tags, function (e, val) {
        var arrayForSelect2 = [];
        if ($('#' + val).length && $('#' + val).attr('data-tag')){
            $.each(JSON.parse($('#' + val).attr('data-tag')), function (element, value) {
                arrayForSelect2.push(value);
            });
            $("#" + val).select2('data', arrayForSelect2);
        }
    });


    $('#date_template :radio').on('change', getDataTemplate);
    $('.select_all_company_elastix').on('click', selectAllCompany);
    $('.change_company_elastix').on('change', changeCompanyElastix);
    $('#choose_all').on('change', chooseAllOrders);
    $('.change_company_elastix_button').on('click', changeCompanyElastixButton);

    /**
     * Календарик
     */
    myDatepicker($('#date_start'));
    myDatepicker($('#date_end'));

    countByStatus();

    $('#track_filter').select2({
        minimumInputLength: 0,
        multiple: true,
        ajax: {
            method: 'get',
            url: '/track/find',
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
    if ($('#track_filter').attr('data-tracks')) {
        var arrayForSelect2 = [];
        $.each(JSON.parse($('#track_filter').attr('data-tracks')), function (element, value) {
            arrayForSelect2.push(value);
        });
        $("#track_filter").select2('data', arrayForSelect2);
    }



    $('#initiator').select2({
        // placeholder: "",
        minimumInputLength: 1,
        multiple: true,
        ajax: {
            url: '/user/find',
            dataType: 'json',
            data: function (params) {
                return {
                    query: $.trim(params),
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
        }
    });
    if ($('#initiator').attr('data-initiators')) {
        var arrayForSelect2 = [];
        $.each(JSON.parse($('#initiator').attr('data-initiators')), function (element, value) {
            arrayForSelect2.push(value);
        });
        $("#initiator").select2('data', arrayForSelect2);
    }

    $("#change_order_proc_stage").on('click', function () {
        var btn = $(this);
        disableButton(btn, true);
        showMessage('processing');
        var orders = [];

        $('.change_company_elastix').each(function (e, val) {

            if ($(this).is(':checked')) {
                orders.push($(val).val());
            }
        });
        $.post('/ajax/reset-pros-stage' + window.location.search, $.param({
            priority : $('#proc_stage').val(),
            allOrders : +$('#add_all').prop('checked'),
            orders : orders
        }), function (json) {
            if (json.success) {
                showMessage('success', json.message);
            } else if (json.message) {
                showMessage('error', json.message);
            } else {
                showMessage('error');
            }
            disableButton(btn);
        })
    });
});

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
            dateStartObj.val(json.start);
            dateEndObj.val(json.end);
        }, 'json');
    }
}

function selectAllCompany() {
    // var text;
    if ($('.change_company_elastix:checked').length == $('.change_company_elastix').length) {
        $('.change_company_elastix').prop('checked', false);
        // text = 'Выбрать все заказы';
    } else {
        $('.change_company_elastix').prop('checked', true);
        // text = 'Снять выбранные заказы';
    }
    // $('.select_all_company_elastix').text(text);
}

function changeCompanyElastix() {
    // var text = 'Выбрать все заказы';

    if ($('.change_company_elastix:checked').length == $('.change_company_elastix').length) {
        // text = 'Снять выбранные заказы';
    }
    $('.select_all_company_elastix').text(text);
}

function changeCompanyElastixButton() {
    var obj = $('.change_company_elastix:checked');
    var count = obj.length;
    if (count) {
        var id = $('#elastix_company_select').val();
        var data = [];
        for (var i = 0; i < count; i++) {
            data.push(obj.eq(i).val());
        }
        $.post('/change-elastix-company-orders-ajax/', {data: data, id: id}, function (json) {
            if (json.success) {
                showMessage('success', json.message);
            }
        }, 'json').fail(function (json) {
            showMessage('error');
        });
    }
}

function chooseAllOrders() {
    if ($(this).is(':checked')) {
        $('.choose').each(function (e, val) {
            $(val).prop('checked', true);
        });
    } else {
        $('.choose').each(function (e, val) {
            $(val).prop('checked', false);
        })
    }
}

/**
 * Календарик
 */
function myDatepicker(obj) {
    var start = new Date();
    obj.datepicker({
        language: 'en',
        startDate: start,
        onSelect: function (fd, d, picker) {
            if (!d) {
                return;
            }
        }
    });
}

function countByStatus() {
    if ($('#countByStatus').length) {
        $.post('ajax/count-orders-by-status' + location.search, function (json) {
            try {
                var labels = $('#countByStatus .status');
                $.each(labels, function (index, obj) {
                    $(obj).empty();
                });
                $.each(json, function (index, value) {
                    $('#countByStatus .status[data-status="' + value.proc_status + '"').text(value.count);
                });
            } catch (e) {
                console.log(e.message);
            }
            setTimeout(countByStatus, 10000);
        })
    }

    $('.pass-reversal').click(function () {
        event.preventDefault();
        var div = $(this).parent('li').parent('ul').parent('div');
        setTimeout(function () {
            $(div).addClass('open');
        }, 10);
    });
}
