$(function () {
    /*
   *Стилизация select
    */
    $('.system_status, .proc_status').select2({
        placeholder: 'Все',
        allowClear: true,
    });

    $('a.save_changes')
        .editable({
            url: '/ajax/save-code-status',
            emptytext: 'Сохранить',
            params: function (params) {
                params.code = $(this).attr('data-code');
                params.procStatus = $(this).parent('td.save-changes').siblings('.proc-status').children('select.proc_status').val();
                params.status = $(this).parent('td.save-changes').siblings('.system-status').children('select.system_status').val();
                params.integrationId = $('#integrationId').val();
                return params;
            },
            ajaxOptions: {
                type: 'post',
                dataType: 'json'
            },
            tpl: '',
            validate: function (data) {
                let procStatus = $(this).parent('td.save-changes').siblings('.proc-status').children('select.proc_status').val();
                let status = $(this).parent('td.save-changes').siblings('.system-status').children('select.system_status').val();
                if (procStatus && !status) {
                    getMessage('warning', 'Укажите Системный статус!');
                    return ' ';
                }
                else if (!procStatus && !status) {
                    getMessage('warning', 'Укажите настройки для кода интеграции!');
                    return ' ';
                }
                else {
                    $(this).editable('setValue', "Сохранить", true);
                }
            },
            success: function (data) {
                if (data.success) {
                    getMessage('success', 'Изменения успешно сохранены!');
                } else {
                    getMessage('error', 'Ошибка');
                }
            },
            display: function (value, response) {
                return false;   //disable this method
            },
            error: function (errors) {
                getMessage('error', "Произошла ошибка на сервере!");
            }
        });

    /**
     * Выводим сообщение
     */
    function getMessage(type, message) {
        $('.ns-box').remove();
        if (type === 'wait') {
            var notification = new NotificationFx({
                message: '<span class="fa fa-spinner fa-2x alert_spinner"></span><p>' + message + '</p>',
                layout: 'bar',
                effect: 'slidetop',
                type: 'notice',
                ttl: 6000,
            });
        } else {
            var notification = new NotificationFx({
                message: '<span class="icon fa fa-bullhorn fa-2x"></span><p>' + message + '</p>',
                layout: 'bar',
                effect: 'slidetop',
                type: type,
                ttl: 6000,
            });
        }
        notification.show();
    }
});