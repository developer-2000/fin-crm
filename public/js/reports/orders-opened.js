$(function () {
    /*
    *Стилизация select
    */
    $('#status, #country, #company, #mistake_type, #moderator, #group, #user, #target, #partners, #sub_status, #deliveries, #cause_cancel').select2({
        placeholder: $(this).data('placeholder'),
        allowClear: true,
    });

    $('#date_template :radio').on('change', getDataTemplate);
    /**
     * Календарик
     */
    myDatepicker($('#date_start'));
    myDatepicker($('#date_end'));

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
});