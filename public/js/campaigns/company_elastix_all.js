$(function () {
    $('.position').on('click', updatePosition);
});

function updatePosition(e) {
    var parent = $(e.currentTarget).closest('tr'),
        data = {
            'id'        : parent.attr('id'),
            'position'  : parent.attr('position'),
            'function'  : $(e.currentTarget).attr('data')
        };
    $.post('/company_elastix/position_update', {data: data}, function(json) {
        if (json.status) {
            $('.table').remove();
            $('.table-responsive').append(json.view);
            $('.position').on('click', updatePosition);
        }

    }, 'json');
}