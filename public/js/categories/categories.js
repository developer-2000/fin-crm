$(function () {
    $('.nestable').each(function () {
        $(this).nestable({
            group: $(this).attr('data-group'),
            maxDepth : 3
        } );
    })
        .on('change', updateOutput);

    addEdittable();
    $('.delete_sub_categories').on('click', deleteSubCategory);
});

function updateOutput(e) {
    var list = e.length ? e : $(e.target),
        output = list.data('output');
    if (window.JSON) {
//                output.val(window.JSON.stringify(list.nestable('serialize')));//, null, 2));

        var jsonData = window.JSON.stringify(list.nestable('serialize'));
        if (list.nestable('serialize').length) {
            $.post('ajax/categories/change-position', {json : jsonData}, function (json) {
                if (!json.success) {
                    getMessage('error', 'Произошла ошибка на сервере!');
                }
            })
        }
    }
    else {
        output.val('JSON browser support required for this demo.');
    }
}

function deleteSubCategory() {
    var item = $(this).parents('.dd-item:first');
    var id = $(this).attr('data-id');
    var parent = item.parents('.dd-item:first');

    getMessage('wait', 'Обработка!');

    $.post('ajax/categories/delete', {id : id}, function (json) {

        if (!json.check) {
            getMessage('error', 'Нельзя удалить категорию');
        } else if (json.success) {
            getMessage('success', 'Категория удалена');
            item.fadeOut(300);
            setTimeout(function () {
                if (parent.length && +parent.find('.dd-item').length <= 1) {
                    parent.children('button').remove();
                }
                item.remove();
            }, 300);
        } else {
            getMessage('error', 'Категория не удалена');
        }

    });

    return false;
}

function addEdittable() {
    $('.category_name').editable({
        title : 'Выберите название',
        emptytext : '-',
        url : '/ajax/categories/edit',
        ajaxOptions : {
            type : 'post',
            dataType : 'json'
        },
        validate: function (value) {
            if ($.trim(value) == '') {
                return 'Заполните поле!';
            }
        },
        success : function (data, config) {
            if (data.success) {
                getMessage('success', "Успешно изменен")
            } else {
                var text = data.message ? data.message : '';
                getMessage('error', "Произошла ошибка!" + text);
            }

        },
        error : function (errors) {
            getMessage('error', "Произошла ошибка на сервере!");
        }
    });

    $('.add_categories').editable({
        emptytext : 'Добавить',
        url : '/ajax/categories/create',
        ajaxOptions : {
            type : 'post',
            dataType : 'json'
        },
        params: function(params) {
            var data = {};
            data['name'] = params.value;
            data['entity'] = $(this).attr('data-entity') ? $(this).attr('data-entity') : '';
            return data;
        },
        display: function (value, sourceData) {
            $(this).empty();
            $(this).text('Добавить');
        },
        validate: function (value) {
            if ($.trim(value) == '') {
                return 'Заполните поле!';
            }
        },
        success : function (data, config) {
            if (data.success) {
                getMessage('success', "Успешно добавлен");
                var mainBox = $(this).parents('.main-box'),
                    list = mainBox.find('.nestable .dd-list:first'),
                    html = '<li class="dd-item" data-id="' + data.id + '"> ' +
                            '<div class="dd-handle"> ' +
                                '<span class="dd-nodrag"> ' +
                                    '<a href="#" class="nested-link category_name" data-pk="' + data.id + '" data-value="' + config + '" data-name="name">' + config +
                                    '</a> ' +
                                '</span> ' +
                                '<div class="nested-links dd-nodrag"> ' +
                                '<a href="#" class="nested-link delete_sub_categories" data-id="' + data.id + '"> ' +
                                    '<i class="fa fa-trash"></i> ' +
                                '</a> ' +
                                '</div> ' +
                            '</div>' +
                        '</li>';
                list.append(html);
                addEdittable();
                $('.delete_sub_categories').on('click', deleteSubCategory);

            } else {
                var text = data.message ? data.message : '';
                getMessage('error', "Произошла ошибка!" + text);
            }

        },
        error : function (errors) {
            getMessage('error', "Произошла ошибка на сервере!");
        }
    });

}

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
            ttl: 60000,
        });
    } else {
        var notification = new NotificationFx({
            message: '<span class="icon fa fa-bullhorn fa-2x"></span><p>' + message + '</p>',
            layout: 'bar',
            effect: 'slidetop',
            type: type,
            ttl: 3000,
        });
    }
    notification.show();
}