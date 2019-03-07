$(function () {
    $('.var_value').on('change', function () {
        var val = $(this).prop('checked') ? 1 : 0,
            key = $(this).attr('name');
        $.post('/ajax/change-variable', { key : key, value : val}, function () {
            
        })
    })
});