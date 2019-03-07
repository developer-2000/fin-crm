$(function () {
    $('#setting').on('click', sendUpdate);
});

function searchError() {
    var htmlError = '<span class="badge badge-danger error-massage" style="background-color: #f4786e;margin: 0 0 10px 5px">',
        end = '</span>',
        errors = 0,
        change = 0;
    if ($('#min_price').val() != $('#min_price').attr('start-value')) {
        if ($('#min_price').val()) {
            $('#label_min').find('.error-massage').remove();
            if ($.isNumeric($('#min_price').val()) != true) {
                $('#label_min').append(htmlError + 'Не валидные данные' + end);
                errors = 1;
            } else {
                if ($('#min_price').val() > 0) {
                    if ($('#min_price').val() * 1 > $('#max_price').val() * 1 && $('#max_price').val() > 0) {
                        $('#label_min').append(htmlError + 'Мин.цена > Макс.цена' + end);
                        errors = 1;
                    }
                }
                if ($('#min_price').val() < 0) {
                    $('#label_min').append(htmlError + 'Мин.цена отрицательная' + end);
                    errors = 1;
                }
            }
        }
        change = 1;
    }
    if ($('#max_price').val() != $('#max_price').attr('start-value')) {
        if ($('#max_price').val()) {
            $('#label_max').find('.error-massage').remove();
            if ($('#max_price').val() > 0) {
                if ($.isNumeric($('#max_price').val()) != true) {
                    $('#label_max').append(htmlError + 'Не валидные данные' + end);
                    errors = 1;
                }
            } else {
                $('#label_max').append(htmlError + 'Макс.цена отрицатеьлная' + end);
                errors = 1;
            }
        }
        change = 1;
    }
    if ($('#approve').val() != $('#approve').attr('start-value')) {
        if ($('#approve').val()) {
            $('#label_approve').find('.error-massage').remove();
            if ($.isNumeric($('#approve').val()) != true) {
                $('#label_approve').append(htmlError + 'Не валидные данные' + end);
                errors = 1;
            } else {
                if ($('#approve').val() < 0 || $('#approve').val() > 100) {
                    if ($('#approve').val() > 100) {
                        var message = 'Значение больше 100';
                    } else {
                        var message = 'Значение меньше 0';
                    }
                    $('#label_approve').append(htmlError + message + end);
                    errors = 1;
                }
            }
        }
        change = 1;
    }
    if ($('#alias').val() != $('#alias').attr('start-value')) {
        if ($('#alias').val()) {
            $('#label_alias').find('.error-massage').remove();
            if ($("#alias").val().length > 255) {
                $('#label_alias').append(htmlError + 'Слишком длиное значение' + end);
                errors = 1;
            }
        }
        if (errors == 0) {
            $('.error-massage').remove();
        }
        change = 1;
    }
    if (change == 1) {
        return errors;
    }
}

function createData() {
    if (searchError() == 0) {
        var data = {
            'min_price' : $('#min_price').val(),
            'max_price' : $('#max_price').val(),
            'approve'   : $('#approve').val(),
            'alias'     : $('#alias').val(),
        };
        return data;
    }
}

function sendUpdate() {
    var data = createData();
    if (data) {
        updateAjax(data);
    }
}

function updateAjax(data) {
    var id = $('#setting').attr('id_offer');
    $.post('/update-offer-ajax/' + id, {data: data}, function(json) {
        if (json.error == 0) {
            alert('Успешно обновлено');
        } else {
            alert('Произошла ошибка');
        }

    }, 'json');
}