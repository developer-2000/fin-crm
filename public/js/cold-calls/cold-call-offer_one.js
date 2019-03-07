$(function () {
    $('#change_offer').on('submit', changeOffer);
    $('body').on('click', '.add_price', addPrice);
    $('body').on('click', '.del_price', delPrice);
    $('#add_product').on('submit', addNewProductOffer);
    $('body').on('click', '.delete_product', deleteProduct);
    $('#product').select2();

    $('.select2-input').on('keydown', searchProducts);




});

window.offerId = $('#of_id').val();

function changeOffer() {
    getMessage('wait', 'Обрабатывется');
    $.post('/change-offer-information-ajax/' + window.offerId, $(this).serialize(), function (json) {
        $('#change_offer').find('.has-error').removeClass('has-error');
        if (json.errors) {
            for (key in json.errors) {
                $('#' + key).parents('.form-group').addClass('has-error');
            }
            getMessage('error', 'Ошибка');

        }

        if(json.status) {
            getMessage('success', 'Данные изменены');
        }
    });
    return false;
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

function addNewProductOffer() {

    $.post('/add-new-product-for-offers-ajax-cold-calls/' + window.offerId, $(this).serialize(), function (json) {
        $('#add_product').find('.has-error').removeClass('has-error');
        if (json.errors) {
            for (key in json.errors) {
                $('#' + key).parents('.form-group').addClass('has-error');
            }
            getMessage('error', 'Ошибка');
        }
        if(json.success) {
            $('.all_products').empty();
            $('.all_products').append(json.html);
            getMessage('success', 'Товар добавлен');
        }
    });
    return false;
}

function deleteProduct() {
    var id = $(this).find('span').attr('data-id');
    var parent = $(this).parents('tr');
    $.post('/ajax/delete-product-from-offer', {id: id}, function (json) {
        if (json.status) {
            parent.fadeOut(400);
            setTimeout(function () {
                parent.remove();
            }, 400);
            getMessage('success', 'Товар удален');
        } else {
            getMessage('error', 'Ошибка');
        }
    });
    return false;
}

/**
 * Поиск по товарам
 */
function searchProducts(e) {
    if ((e.which >= 48 && e.which <= 90) || (e.which >= 186 && e.which <= 220) || (e.which >= 96 && e.which <= 111) || e.which == 8 || e.which == 46) {
        setTimeout(function() {
            var search = $(e.currentTarget).val();
            if (!search) {
                $('#product').empty();
                return false;
            }
            $.post('/search-products-for-offer-ajax/', {offerId: window.offerId, search: search}, function(json) {
                $('#product').empty();
                $('#product').append(json.html);
            }, 'json');
        }, 200);
    }
}

/**
 * добавление цены
 */
function addPrice() {
    var td = $(this).parents('td'),
        select = td.find('select option:selected'),
        price = td.find('input').val();
    $('.search_block').find('.has-error').removeClass('has-error');
    if (price/1 && select.val()) {
        var new_price ='<div>' +
            '<span class="country" code="' + select.val() + '" >' + select.text() + '</span> - <span class="price">' + price + '</span> <span class="del_price"><i class="fa fa-times"></i></span><br></div>';
        td.find('.block_price').append(new_price);
        select.remove();
    } else {
        td.find('input').parent('.form-group').addClass('has-error');
    }

    return false;
}

/**
 * удаляем цены
 */
function delPrice() {
    var parent = $(this).parent('div'),
        code = parent.find('.country').attr('code'),
        name = parent.find('.country').text();
    $(this).parents('td').find('select').append('<option value="' + code + '">' + name + '</option>')
    parent.remove();
}


//  * Поиск по товарам
$('.products-select2').select2({
    placeholder: "выберите товар.",
    minimumInputLength: 1,
    multiple: true,
    ajax: {
        url: '/cold-calls/product/find/',
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