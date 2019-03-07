$(function () {
    /*
   *Стилизация select
    */
    $('#category').select2({
        placeholder: 'Все',
        allowClear: true
    });

    $('#project').select2({
        placeholder: "",
        minimumInputLength: 0,
        multiple: true,
        ajax: {
            method: 'get',
            url: '/projects/find',
            dataType:
                'json',
            data:
                function (params) {
                    return {
                        q: $.trim(params)
                    };
                },
            results: function (data) {
                return {
                    results: data,
                    "pagination": {
                        "more": true
                    }
                };
            }
        },
    });

    if ($('#project').attr('data-project')) {
        var arrayForSelect2 = [];
        $.each(JSON.parse($('#project').attr('data-project')), function (element, value) {
            arrayForSelect2.push(value);
        });
        $("#project").select2('data', arrayForSelect2);
    }

    $('#sub_project').select2({
        placeholder: "",
        minimumInputLength: 0,
        multiple: true,
        ajax: {
            method: 'get',
            url: '/sub_projects/find',
            dataType:
                'json',
            data:
                function (params) {
                    return {
                        q: $.trim(params),
                        project_id: $('#project').val() ? $('#project').val().split(",") : ''
                    };
                },
            results: function (data) {
                return {
                    results: data,
                    "pagination": {
                        "more": true
                    }
                };
            }
        },
    });
    if ($('#sub_project').attr('data-sub_project')) {
        var arrayForSelect2 = [];
        $.each(JSON.parse($('#sub_project').attr('data-sub_project')), function (element, value) {
            arrayForSelect2.push(value);
        });
        $("#sub_project").select2('data', arrayForSelect2);
    }

    //  * Поиск по офферам
    $('.sub_projects').select2({
        placeholder: "Выберите подпроект.",
        minimumInputLength: 0,
        multiple: true,
        ajax: {
            url: '/sub_projects/find',
            dataType: 'json',
            data: function (params) {

                return {
                    q: $.trim(params)
                };
            },

            results: function (data) {
                return {
                    results: data,
                    "pagination": {
                        "more": true
                    }
                };
            }
        }
    });
    if ($('#subProjectsJson').val()) {
        $('.sub_projects').select2('data', JSON.parse($('#subProjectsJson').val()));
    }


    $('#offers').select2({
        placeholder: "",
        minimumInputLength: 0,
        multiple: true,
        ajax: {
            method: 'get',
            url: '/offer/find',
            dataType:
                'json',
            data:
                function (params) {
                    return {
                        q: $.trim(params),
                        allowWithoutPtoject : true
                    };
                },
            results: function (data) {
                return {
                    results: data,
                    "pagination": {
                        "more": true
                    }
                };
            }
        },
    });
    if ($('#offers').attr('data-offers')) {
        var arrayForSelect2 = [];
        $.each(JSON.parse($('#offers').attr('data-offers')), function (element, value) {
            arrayForSelect2.push(value);
        });
        $("#offers").select2('data', arrayForSelect2);
    }

    $('#product').select2({
        placeholder: "",
        minimumInputLength: 0,
        multiple: true,
        ajax: {
            method: 'get',
            url: '/product/find',
            dataType:
                'json',
            data:
                function (params) {
                    return {
                        q: $.trim(params),
                        project_id: $('#project').val() ? $('#project').val().split(",") : '',
                        sub_project_id: $('#sub_project_id').val() ? $('#sub_project_id').val().split(",") : ''
                    };
                },
            results: function (data) {
                return {
                    results: data,
                    "pagination": {
                        "more": true
                    }
                };
            }
        },
    });
    if ($('#product').attr('data-product')) {
        var arrayForSelect2 = [];
        $.each(JSON.parse($('#product').attr('data-product')), function (element, value) {
            arrayForSelect2.push(value);
        });
        $("#product").select2('data', arrayForSelect2);
    }


    $('#product_to_merge').select2({
        placeholder: "Выберите товар",
        minimumInputLength: 0,
        multiple: false,
        ajax: {
            method: 'get',
            url: '/product/find',
            dataType:
                'json',
            data:
                function (params) {
                    return {
                        q: $.trim(params),
                        except_product: $('#productId').val() ? $('#productId').val() : '',
                        project_id: $('#project').val() ? $('#project').val().split(",") : '',
                        sub_project_id: $('#sub_project_id').val() ? $('#sub_project_id').val().split(",") : ''
                    };
                },
            results: function (data) {
                return {
                    results: data,
                    "pagination": {
                        "more": true
                    }
                };
            }
        },
    });
    if ($('#product_to_merge').attr('data-product-merge')) {
        var arrayForSelect2 = [];
        $.each(JSON.parse($('#product_to_merge').attr('data-product-merge')), function (element, value) {
            arrayForSelect2.push(value);
        });
        $("#product_to_merge").select2('data', arrayForSelect2);
    }


    $(document).on('change', '.activate_product', function () {
        var product = $(this).val();
        if ($(this).prop('checked')) {
            var status = 'on';
        } else {
            status = 'off';
        }

        $.getJSON('/ajax/products/' + product + '/set-status/' + status, function (json) {
            if (json.success) {
                getMessage('success', 'Товар успешно обновлен')
            }
            else {
                getMessage('error', 'Ошибка');
            }
        });
    });

    $('.activate_product').on('change', changeStatus);

    $('form#product-create').submit(function () {
        event.preventDefault();
        $.post('/ajax/products/create', $(this).serialize(), function (json) {
            if (json.success) {
                $('#product-create').removeClass('md-show');
                getMessage('success', 'Продукт успешно добавлен!');

                $('tbody').prepend(json.html);
                $(document).ready(function () {
                    $('.destroy-product').editable({
                        type: 'none',
                        escape: true,
                        title: 'Вы действительно хотите удалить товар?',
                        tpl: '',
                        success: function (data) {
                            if (data.pk) {
                                var parent = $("a[data-pk='" + Number(data.pk) + "']").parents('tr');
                                parent.fadeOut(400);
                                setTimeout(function () {
                                    parent.remove();
                                }, 400);
                            }
                        }
                    });

                    $(document).ready(function () {
                        $('.product').editable({
                            escape: true,
                            title: 'Редактировать наименование',
                        });
                    });
                });
            }
        }).fail(function (json) {
            var errors = [];
            $.each(JSON.parse(json.responseText).errors, function (e, obj) {
                errors.push(obj + '<br>');
            });
            getMessage('error', errors);
        });
    })

    $('form#product-store').submit(function () {
        event.preventDefault();
        $.post('/ajax/products/store', $(this).serialize(), function (json) {
            if (json.success) {
                getMessage('success', 'Продукт успешно обновлен!');
            }
        }).fail(function (json) {
            var errors = [];
            $.each(JSON.parse(json.responseText).errors, function (e, obj) {
                errors.push(obj + '<br>');
            });
            getMessage('error', errors);
        });
    })

    $('#merge_products_button').click(function () {
        event.preventDefault();
        $.post('/ajax/products/merge', {
            productId: $('#productId').val(),
            product_to_merge: $('#product_to_merge').val(),
        }, function (json) {
            disableButton($('#merge_products_button'), true);
            if (json.success) {
                getMessage('success', 'Товары успешно обьединены!');
            }
            if (!json.success) {
                getMessage('error', json.error_message);
            }
        }).fail(function (json) {
            var errors = [];
            $.each(JSON.parse(json.responseText).errors, function (e, obj) {
                errors.push(obj + '<br>');
            });
            getMessage('error', errors);
        });
    })

    $('#add_option').on('click', function () {
        var clone = $('.hidden .option').clone();

        $('#options').append(clone);

        optionIndex();
        return false;
    });

    $('#options').on('click', '.delete_option', function () {
        var block = $(this).parents('.option');
        block.fadeOut(300);
        setTimeout(function () {
            block.remove();
            optionIndex();
        }, 300);
        return false;
    });
});

function optionIndex() {
    var options = $('#options .option'),
        i = 0;
    if (options.length) {
        $.each(options, function (id, value) {
            $(value).find('input[type="hidden"]').attr('name', 'options[' + i + '][id]');
            $(value).find('input[type="text"]').attr('name', 'options[' + i + '][value]');
            i++;
        })
    }
}

function changeStatus() {
    var product = $(this).val();
    if ($(this).prop('checked')) {
        var status = 'on';
    } else {
        status = 'off';
    }

    $.getJSON('/ajax/products/' + product + '/set-status/' + status, function (json) {
        if (json.success) {
            getMessage('success', 'Товар успешно обновлен')
        }
        else {
            getMessage('error', 'Ошибка');
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