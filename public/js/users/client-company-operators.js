$(document).ready(function() {
    var updateOutput = function(e){
        var list   = e.length ? e : $(e.target),
            output = list.data('output');
        if (window.JSON) {
            output.val(window.JSON.stringify(list.nestable('serialize')));//, null, 2));
        }
        else {
            output.val('JSON browser support required for this demo.');
        }
    };

    $('.operators').sortable({
        connectWith : '.target_groups',
        beforeStop : addOperators
    });

    $('.target_groups').sortable({
        connectWith : 'ul',
    });

    $('.operators_in_group').sortable({
        connectWith : '.target_groups',
        beforeStop : addOperators
    });

});

function addOperators(event, ui) {
    var operator = ui.item.context.attributes['id'].value;
    if (!$('.operators #' + operator).length) {
        var groupId = $('.groups #' + operator).parents('ul').attr('group-id');
        var operatorHtml =  $('.groups #' + operator).clone().css('display', 'none');
        var color = $('.group_operator #group_' + groupId).find('.content').css('background-color');
        $.post('/add-user-in-client-company-ajax', {company : groupId, user : operatorHtml.attr('data-id')}, function (json) {
            if (json.status) {
                $('.group_operator #group_' + groupId + ' .operators_in_group').append(operatorHtml);
                $('.groups #' + operator).fadeOut();
                $('.group_operator #' + operator).css('border-color', color).fadeIn();
                setTimeout(function () {
                    $('.groups #' + operator).remove();
                    for (var i = 0; i < $('.group_operator li').length; i++) {
                        var count = $('.group_operator li').eq(i).find('.operators_in_group li').length;
                        $('.group_operator li').eq(i).find('.count').text(count);
                    }
                }, 600)
            } else {
                $('.group_operator #group_' + groupId + ' .operators_in_group').append(operatorHtml);
                $('.groups #' + operator).fadeOut();
                $('.group_operator #' + operator).css('border-color', color).fadeIn();
                setTimeout(function () {
                    $('.groups #' + operator).remove();
                },600);
            }
        });
    }
}