jQuery('#datetimepicker').datetimepicker({});
jQuery.datetimepicker.setLocale('en');
$('button.integrations_keys_create').on('click', function () {
    event.preventDefault();
    var block = $('#form_block');
    var formData = $('form#integrations-keys-create').serialize();

    $.post('/ajax/integrations/keys/create', formData, function (json) {
        if (json.success) {
            getMessage('success', 'Ключ добавлен');
            block.find('.close').click();
            if (json.tableHtml) {
                $('.integrations_table tbody').empty();
                $('.integrations_table tbody').append(json.tableHtml);
            }
            $('#create-role')[0].reset();
        } else {
            getMessage('error', 'Произошла ошибка');
            block.find('.close').click();
        }
    })
        .fail(function (json) {
            var errors = [];
            $.each(JSON.parse(json.responseText).errors, function (e, obj) {
                errors.push(obj + '<br>');
            });
            getMessage('error', errors);
        });
});

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

$('.activate').on('change', changeStatus);

function changeStatus() {
    var keyId = $(this).val();
    console.log(keyId);
    if ($(this).prop('checked')) {
        var status = '1';
    }
    else {
        status = '0';
    }
    $.getJSON('/ajax/novaposhta/keys/' + keyId + '/' + status, function (data) {
    })
}
