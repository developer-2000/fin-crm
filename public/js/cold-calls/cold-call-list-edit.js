$(function () {
    $('.change_row').on('click', changeRow)
    $('#form_pbx').on('submit', uploadAgainToPbx);
    $('#form2').on('submit', updateRow);
    $('#myonoffswitch').on('change', changeStatusAjax);
});
$(window).load(function () {
    var campaignId = $('#campaign_id_hidden').val();
    $('#campaign_id option[value=' + campaignId + ']').attr('selected', true)

    $('tr').each(function () {
        var id = $(this).find('input:checkbox').val();
        if ($(this).find('input:hidden').val() == 1) {
            $(this).addClass('danger');
        }
    });

});

function uploadAgainToPbx() {

    $.post('/ajax/upload-cold-call-lists-pbx', $(this).serialize(), function (json) {
        if (json.success) {
            getMessage('success', "Загрузка лист на прозвон произошла успешно!");
        } else {
            getMessage('error', "В процессе загрузки произошла ошибка!");
        }
    });
}

function changeRow() {
    if ($(this).is(':checked')) {
        var rowId = $(this).parents('tr').attr('id');
        $.getJSON('/cold_call_list_getData_ajax/' + rowId, function (data) {
            var phoneNumber = jQuery.parseJSON(data.list.phone_number)[0]
            var addInfo = jQuery.parseJSON(data.list.add_info);
            var fullName = addInfo['фамилия'] + '  ' + addInfo['имя'];
            $('tr#' + rowId).replaceWith(
                '<form class="form" id="#form2" method="post" >' +
                '<tr>' +
                '<td>' + rowId + '</td>' +
                '<td><input class="form-control" type="text" name="phone_number" value="' + phoneNumber + '"></td>' +
                '<td><input class="form-control" type="text" name="фио" value="' + fullName + '"></td>' +
                '<td><input class="form-control" type="text" name="status" value="" disabled></td>' +
                '<td><input type="hidden" id="rowId" value="' + rowId + '">' +
                '<td><button type="submit" class="btn btn-success">Сохранить</button></td>' +
                '</tr>' +
                '</form>'
            );
        });
    }
}

function updateRow() {
    $(this).find('.has-error').removeClass('has-error');
    $.post(location.pathname, $(this).serialize(), function (json) {
        if (json.errors) {
            for (key in json.errors) {
                $('#' + key).parents('.form-group').addClass('has-error');
            }
        }
        if (json.success) {
            if (!json.update) {
                getMessage('success', "План добавлен");
                $('form')[0].reset();
                getForm();
                $('.billing_block').empty();
            } else {
                getMessage('success', "План успешно изменен!");
            }
        }
    });

    return false;
}

function update() {
    $(this).find('.has-error').removeClass('has-error');
    $.post(location.pathname, $(this).serialize(), function (json) {
        if (json.errors) {
            for (key in json.errors) {
                $('#' + key).parents('.form-group').addClass('has-error');
            }
        }
        if (json.success) {
            if (!json.update) {
                getMessage('success', "План добавлен");
                $('form')[0].reset();
                getForm();
                $('.billing_block').empty();
            } else {
                getMessage('success', "План успешно изменен!");
            }
        }
    });

    return false;
}

function changeStatusAjax() {
    var listFileId = $('#listFileId').val();
    if ($(this).prop('checked')) {
        var status = 'active';
        $.getJSON('/ajax/cold-calls-list-change-status/' + listFileId + '/' + status, function (data) {
        })
    }
    else {
        status = 'inactive';
        $.getJSON('/ajax/cold-calls-list-change-status/' + listFileId + '/' + status, function (data) {
        })
    }
}

/**
 * Выводим сообщение
 */
function getMessage(type, message) {
    $('.ns-box').remove();
    var notification = new NotificationFx({
        message: '<span class="icon fa fa-bullhorn fa-2x"></span><p>' + message + '</p>',
        layout: 'bar',
        effect: 'slidetop',
        type: type,
        ttl: 3000,
    });

    notification.show();
}
