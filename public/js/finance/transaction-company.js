$(function () {
    /**
     * Календарик
     */
    myDatepicker($('#date_start'));
    myDatepicker($('#date_end'));
    $('#country, #company, #operator').select2({
        placeholder: 'Все',
        allowClear: true
    });
    $('#date_template :radio').on('change', function(e) {
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
            $.post('/date-filter-template-ajax/', {type: type}, function(json) {
                dateStartObj.val(json.start);
                dateEndObj.val(json.end);
            }, 'json');
        }
    });
});

/**
 * Календарик
 */
function myDatepicker(obj) {
    var start = new Date(), prevDay, startHours = 0;
    start.setHours(1);
    start.setMinutes(0);
    obj.datepicker({
        timepicker: true,
        language: 'ru',
        startDate: start,
        minHours: startHours,
        maxHours: 18,
        onSelect: function (fd, d, picker) {
            if (!d) {
                return;
            }
            var day = d.getDay();
            if (prevDay != undefined && prevDay == day) {
                return;
            }
            prevDay = day;
            picker.update({
                minHours: 0,
                maxHours: 23
            });
        }
    });
}