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

    $('body').on('click', '.count', toggleOperators);

    /**
     * Поиск по операторам
     */
    $('.search').on('keydown', searchOperators);

});

function addOperators(event, ui) {
    var operator = ui.item.context.attributes['id'].value;
    if (!$('.operators #' + operator).length) {
        var groupId = $('.groups #' + operator).parents('ul').attr('group-id');
        var operatorHtml =  $('.groups #' + operator).clone().css('display', 'none');
        var color = $('.group_operator #group_' + groupId).find('.content').css('background-color');
        $.post('/ajax/change-operator-queues-elastix', {queues : groupId, id : operatorHtml.attr('data-id')}, function (json) {
            if (json.status) {
                if (groupId == 0) {
                    $('.operators').append(operatorHtml);
                    $('.groups #' + operator).fadeOut();
                    $('.operators #' + operator).css('border-color', '#e1e1e1').fadeIn();
                } else {
                    $('.group_operator #group_' + groupId + ' .operators_in_group').append(operatorHtml);
                    $('.groups #' + operator).fadeOut();
                    $('.group_operator #' + operator).css('border-color', color).fadeIn();
                }
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

function toggleOperators() {
    var ul = $(this).parents('li').find('.operators_in_group');
    ul.slideToggle(400);
}

function searchOperators(e) {
    if ((e.which >= 48 && e.which <= 90) || (e.which >= 186 && e.which <= 220) || (e.which >= 96 && e.which <= 111) || e.which == 8 || e.which == 46) {
        setTimeout(function() {
            var search = $(e.currentTarget).val();

            $.post('/search-operators-for-pgx-campaign-ajax', {search: search}, function(json) {
                $('.wrapper').empty();
                $('.wrapper').append(json.html);
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

            }, 'json');
        }, 200);
    }
}