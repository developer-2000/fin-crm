
$(function () {
    var today =moment(new Date).format('DD-MM-YYYY');
    console.log( $('#date_start').val());
    console.log(today);

    $('input[name="daterange"]').daterangepicker({
        opens: 'left',
        startDate: $('#date_start').val() ? $('date_start').val() : '',
        endDate: $('#date_end').val() ? $('date_end').val() : '',
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
});
