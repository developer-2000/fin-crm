$(function () {
    $('#add_call_time').on('click', function () {
        var html = '<div><input type="text" class="form-control input_call time" style="display: inline-block;margin: 10px 16px 0 0; width: 85%"><label class="btn btn-primary delete_call" style="margin-bottom: 2px"> X </label></div>';
        $('#inputs').append(html);
        $('.delete_call').on('click', deleteCountTime);
        placeholder();
    });
    $('.delete_call').on('click', deleteCountTime);
    $('.add-something').on('click', add);
    $('html').on('click', '.del', function () {
        $(this).parent().remove();
    });
    $('#submit').on('click', sendToServer);
    $('#update').on('click', updateCompany);
});

function deleteCountTime(e) {
    $(e.currentTarget).parent()
        .remove();
    placeholder();
}

function placeholder() {
    var count = $('.input_call').length;
    for (var i = 0; i < count; i++) {
        $('.input_call').eq(i).attr('placeholder', i + 2);
    }
}

function createData() {
    if (searchErrors() == 0) {
        var call_time = [],
            country = [],
            source = [],
            offer = [];
        for (var i = 0; i < $('.time').length; i++) {
            call_time.push($('.time').eq(i).val());
        }
        for (var i = 0; i < $('#label_country').find('.added').length; i++) {
            country.push([$('#label_country').find('.added').eq(i).attr('data-id'), $('#label_country').find('.added').eq(i).attr('data-incl')]);
        }
        for (var i = 0; i < $('#label_source').find('.added').length; i++) {
            source.push([$('#label_source').find('.added').eq(i).attr('data-id'), $('#label_source').find('.added').eq(i).attr('data-incl')]);
        }
        for (var i = 0; i < $('#label_offer').find('.added').length; i++) {
            offer.push([$('#label_offer').find('.added').eq(i).attr('data-id'), $('#label_offer').find('.added').eq(i).attr('data-incl')]);
        }
        var data = {
            'name': $('#name').val(),
            'status': $('#status').prop('checked'),
            'learning': $('#learning').prop('checked'),
            'min_call_count': $('#min_call_count').val(),
            'callTime': call_time,
            'country': country,
            'company_id': $('#company').val(),
            'source': source,
            'offer': offer
        };
        return data;
    }
}

function sendToServer() {
    var data = createData();
    addNewCompany(data);
}

function updateCompany() {
    var data = createData();
    updateCompanyAjax(data);
}

function searchErrors() {
    var htmlError = '<span class="badge badge-danger error-massage" style="background-color: #f4786e;margin: 0 0 10px 5px">',
        end = '</span>',
        errors = 0;
    if ($("#name").val().length < 3 || $("#name").val().length > 50) {
        if ($("#name").val().length > 50) {
            $('#label-name').find('.error-massage').remove();
            $('#label-name').append(htmlError + 'Слишком большое название' + end);
            errors = 1;
        } else {
            $('#label-name').find('.error-massage').remove();
            $('#label-name').append(htmlError + 'Заполените поле' + end);
            errors = 1;
        }
    } else {
        $('#label-name').find('.error-massage').remove();
    }
    if ($.isNumeric($('#call-time').val()) != true) {
        $('#label-time').find('.error-massage').remove();
        $('#label-time').append(htmlError + 'Заполените поле' + end);
        errors = 1;
    } else {
        $('#label-time').find('.error-massage').remove();
    }
    if ($('#inputs').find('.input_call').length > 0) {
        for (var i = 0; i < $('#inputs').find('.input_call').length; i++) {
            if ($('#inputs').find('.input_call').eq(i).val() == '') {
                errors = 2;
            }
            if (!$.isNumeric($('#inputs').find('.input_call').eq(i).val())) {
                errors = 2;
            }
            if (errors == 2) {
                $('#label-time').find('.error-massage').remove();
                $('#label-time').append(htmlError + 'Одно из полей не валидно' + end);
            }
        }
    }
    $('label[for="min_call_count"]').find('.error-massage').remove();
    if ($('#min_call_count').val().length) {
        if (!($('#min_call_count').val() / 1)) {
            $('label[for="min_call_count"]').find('.error-massage').remove();
            $('label[for="min_call_count"]').append(htmlError + 'Поле должно быть числовым' + end);
        }
    }

    return errors;
}

function addNewCompany(data) {
    $.post('/ajax/campaigns-create', {data: data}, function (json) {
        var html = '<span class="badge badge-danger error-massage" style="background-color: #f4786e;margin: 0 0 10px 5px">',
            end = '</span>';
        if (json.error.length > 0) {
            $('#label-name').find('.error-massage').remove();
            $('#label-name').append(html + json.message + end);
        } else {
            $('#label-name').find('.error-massage').remove();
            $('.error-massage').remove();
            $('.input_call').parent().remove();
            $('.added').remove();
            alert('Успешно добавлено');
            $('#form')[0].reset();
        }
    }, 'json');
}

function updateCompanyAjax(data) {
    var id = $('#update').attr('id_company');
    $.post('/company_elastix_ajax/' + id, {data: data}, function (json) {
        var html = '<span class="badge badge-danger error-massage" style="background-color: #f4786e;margin: 0 0 10px 5px">',
            end = '</span>';
        if (json.error.length > 0) {
            $('#label-name').find('.error-massage').remove();
            $('#label-name').append(html + json.message + end);
        } else {
            alert('Успешно обновлено');
        }
    }, 'json');
}

function add() {
    var div = $(this).parents('.form-group-select2'),
        select = div.children('select'),
        check = div.find('.include').attr('id'),
        label = div.find('.label_'),
        count = label.find('.added').length,
        flag = 0,
        country_id = $('#' + select.attr('id')).val(),
        country = $('#' + select.attr('id') + ' option:selected').text(),
        html_green = '<span class="badge badge-danger added" style="background-color: #2ecc71;margin: 0 0 10px 5px" data-id="' + country_id + '" data-incl="1">' + country + ' <span class="badge badge-danger del" style="background-color: #ff0000">X</span></span>',
        html_red = '<span class="badge badge-danger added" style="background-color: #f4786e;margin: 0 0 10px 5px" data-id="' + country_id + '" data-incl="0">' + country + ' <span class="badge badge-danger del" style="background-color: #ff0000">X</span></span>';
    if (country) {
        if (count != 0) {
            for (var i = 0; i < count; i++) {
                if (label.find('.added').eq(i).attr('data-id') == country_id) {
                    flag = 1;
                }
            }
            if (flag != 1) {
                if ($('#' + check).prop('checked')) {
                    $('#' + label.attr('id')).append(html_green);
                    $('#' + select.attr('id') + ' option:selected').each(function () {
                        this.selected = false;
                    });
                }
                else {
                    $('#' + label.attr('id')).append(html_red);
                    $('#' + select.attr('id') + ' option:selected').each(function () {
                        this.selected = false;
                    });
                }
            }
        }
        else {
            if ($('#' + check).prop('checked')) {
                $('#' + label.attr('id')).append(html_green);
                $('#' + select.attr('id') + ' option:selected').each(function () {
                    this.selected = false;
                });
            }
            else {
                $('#' + label.attr('id')).append(html_red);
                $('#' + select.attr('id') + ' option:selected').each(function () {
                    this.selected = false;
                });
            }
        }

    }
}
