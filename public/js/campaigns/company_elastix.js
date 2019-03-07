$(function() {
    $('.change_company_elastix').on('click', function(e) {
        var id = $(e.currentTarget).attr('data-id');
        var queues = $(e.currentTarget).parents('tr')
                                       .find('.company')
                                       .val();
        $.post('/change-operator-queues-elastix/', {id: id, queues: queues}, function(json) {
            if (json.status) {
                alert('Группа успешно измененна');
            }
        }, 'json');
        return false;
    });
});