$(function () {
    $('#change_offer').on('submit', changeOffer);
    $('body').on('click', '.add_price', addPrice);
    $('body').on('click', '.del_price', delPrice);
    $('#add_product').on('submit', addNewProductOffer);
    $('body').on('click', '.delete_product', deleteProduct);
    $('#product').select2({
        placeholder: "Выберите товар.",
        minimumInputLength: 1,
        multiple: false,
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

    $.post('/add-new-product-for-offers-ajax/' + window.offerId, $(this).serialize(), function (json) {
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





// $(function() {
//     /**
//      * Изменяем описание
//      */
//     $('.change_desc').on('click', changeDesc);
//
//     /**
//      * Удаляем описание
//      */
//     $('.delete_desc').on('click', deleteDesc);
//
//     /**
//      * Добавляем описание
//      */
//     $('.add_desc').on('click', function(e) {
//         if ($('#ckeditor-description').length) {
//             return false;
//         }
//         var html = '<div class="panel panel-info add_block">'+
//                         '<div class="panel-heading">'+
//                             '<div class="panel-title add-title-desc">'+
//                                 '<input type="text" style="border: none" value="">'+
//                             '</div>'+
//                         '</div>'+
//                         '<div class="panel-body add-text-desc">'+
//                             '<textarea id="ckeditor-description"></textarea>'+
//                             '<div style="margin-top: 10px"><button type="submit" class="btn btn-success save_desc">Сохранить</button> <button type="submit" class="btn btn-success cancel_desc">Отменить</button></div>'+
//                         '</div>'+
//                     '</div>';
//         $('.container_desc').prepend(html);
//         settingsCkeditor();
//         CKEDITOR.replace('ckeditor-description');
//         $('.save_desc').on('click', function() {
//             var title = $('.add-title-desc input').val();
//             var text = CKEDITOR.instances["ckeditor-description"].getData();
//             if (!title || !text) {
//                 return false;
//             }
//             var offerId = $('.offer_id').text();
//             $.post('/change-add-offer-desc-ajax/', {offerId: offerId, descId: 0, title: title, text: text}, function(json) {
//                 if (json.status) {
//                     var html = '<div class="panel panel-info">'+
//                         '<div class="panel-heading"> '+
//                             '<div class="panel-title title-desc ' +  json.status + '">'+
//                                 '<input type="text" style="border: none" value="'+ title +'" disabled="disabled">'+
//                                 '<div style="float: right">'+
//                                     '<a href="#" class="table-link change_desc" data-id="' + json.status + '">'+
//                                         '<span class="fa-stack">'+
//                                             '<i class="fa fa-square fa-stack-2x"></i>'+
//                                             '<i class="fa fa-pencil fa-stack-1x fa-inverse"></i>'+
//                                         '</span>'+
//                                     '</a>'+
//                                     '<a href="#" class="table-link danger delete_desc" data-id="' + json.status + '">'+
//                                         '<span class="fa-stack">'+
//                                             '<i class="fa fa-square fa-stack-2x"></i>'+
//                                             '<i class="fa fa-trash-o fa-stack-1x fa-inverse"></i>'+
//                                         '</span>'+
//                                     '</a>'+
//                                 '</div>'+
//                             '</div>'+
//                         '</div>'+
//                         '<div class="panel-body text-desc ' + json.status + '">'+
//                             text+
//                         '</div>'+
//                     '</div>';
//                     $('.container_desc').prepend(html);
//                     $('.add_block').remove();
//                     var objChangeDesc = $('.change_desc');
//                     var objDeleteDesc = $('.delete_desc');
//                     objChangeDesc.off('click');
//                     objDeleteDesc.off('click');
//                     objChangeDesc.on('click', changeDesc);
//                     objDeleteDesc.on('click', deleteDesc);
//                 }
//             }, 'json');
//         });
//         $('.cancel_desc').on('click', function(e) {
//             $('.add_block').remove();
//         });
//     });
// });
//
// /**
//  * Изменяем описание
//  */
// function changeDesc(e) {
//     if ($('#ckeditor-description').length) {
//         return false;
//     }
//     var id = $(e.currentTarget).attr('data-id');
//     var obj = $('.text-desc.' + id);
//     var titleObj = $('.title-desc.' + id + ' input');
//     titleObj.removeAttr('disabled', 'disabled');
//     var text = obj.html();
//     var title = titleObj.val();
//     obj.empty();
//     obj.append('<textarea id="ckeditor-description">' + text + '</textarea>');
//     settingsCkeditor();
//     CKEDITOR.replace('ckeditor-description');
//     obj.append('<div style="margin-top: 10px"><button type="submit" class="btn btn-success save_desc">Сохранить</button> <button type="submit" class="btn btn-success cancel_desc">Отменить</button></div>');
//     $('.cancel_desc').on('click', function() {
//         titleObj.attr('disabled', 'disabled');
//         titleObj.val(title);
//         obj.empty();
//         obj.append(text);
//     });
//     $('.save_desc').on('click', function() {
//         var offerId = $('.offer_id').text();
//         var titleNew = titleObj.val();
//         var textNew = CKEDITOR.instances["ckeditor-description"].getData();
//         if (!titleNew || !textNew) {
//             return false;
//         }
//         $.post('/change-add-offer-desc-ajax/', {offerId: offerId, descId: id, title: titleNew, text: textNew}, function(json) {
//             titleObj.attr('disabled', 'disabled');
//             if (json.status) {
//                 titleObj.val(titleNew);
//                 obj.empty();
//                 obj.append(textNew);
//             } else {
//                 titleObj.val(title);
//                 obj.empty();
//                 obj.append(text);
//             }
//         }, 'json');
//     });
//     return false;
// }
//
// /**
//  * Удаляем описание
//  */
// function deleteDesc(e) {
//     var id = $(e.currentTarget).attr('data-id');
//     $.post('/delete-offer-desc-ajax/', {id: id}, function(json) {
//         if (json.status) {
//             $(e.currentTarget).parents('.panel-info')
//                               .remove();
//         }
//     }, 'json');
//     return false;
// }
//
// /**
//  * Настройки Ckeditor
//  */
// function settingsCkeditor() {
//     CKEDITOR.config.toolbar = [
//         { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
//         { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language' ] },
//         { name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
//         { name: 'insert', items: [ 'Image', 'Table', 'HorizontalRule', 'SpecialChar' ] },
//
//         { name: 'styles', items: [ 'Styles', 'Format' ] },
//         { name: 'tools', items: [ 'Maximize' ] },
//         { name: 'others', items: [ '-' ] },
//         { name: 'about', items: [ 'About' ] }
//     ];
//     CKEDITOR.config.toolbarGroups = [
//         { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
//         { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
//         { name: 'links' },
//         { name: 'insert' },
//         '/',
//         { name: 'styles' },
//         { name: 'colors' },
//         { name: 'tools' },
//         { name: 'others' },
//         { name: 'about' }
//     ];
// }