$(function () {
    $('#country, #product').select2();
    /**
     * Добавление рекомендуемых товаров
     */
    $('.add_product').on('click', addNewProduct);
    /**
     * Имитация checkbox как radio и отображение под меню
     */
    $('.callback_status_ext').on('click', showSubMeny);
    $('.call_status').on('click', showSubMeny);

    $('#suspicious').on('change', function () {
        $('#suspicious_comment').stop().slideToggle();
    });

    /**
     * удаления товара из списка
     */
    $('body').on('click', '.delete_product', delProduct);

    /**
     * открытие похожих заказов
     */
    $('.same_phone').on('click', showRepeats);

    $('#config-tool-cog').on('click', function () {
        $('#config-tool').toggleClass('closed');
    });
    $('.close-script').on('click', function () {
        $('#config-tool').toggleClass('closed');
    });
    /* ID заказа */
    window.orderId = $('.order_id').text();

    $('.result .target').on('change', changeTarget);

    /**
     * убираем таб
     */
    $('.close_tab').on('click', customTabs);

    /**
     * Поиск по offers
     */
    $('.search').on('keydown', searchOffers);


    $('.price_offer').on('keyup', calculatePrice);

    /**
     * закрытие поиска
     */
    $(document).mouseup(function (e) {
        var container = $(".search_block");
        if (container.has(e.target).length === 0) {
            container.hide();
        }
    });

    /**
     * Добавляем комментарий
     */
    $('.add_comment').on('click', addComment);

    addEditableForComments();

    /**
     Календарик
     */
    myDatepicker($('.callback_date'));

    /**
     Календарик
     */
    myDatepicker($('.callback_date_moderator'));

    /**
     * Управляем checkbox для up sell и cross sell
     */
    if ($('.up_cross_sell').length) {
        $('body').on('click', '.up_cross_sell', checkBoxUpCrossSell);
    }

    /**
     * Удаляем ошибки input
     */
    $('input[type="text"]').on('click', deleteError);
    $('textarea').on('click', deleteError);

    /**
     * Кнопка сохраниения заявки
     */
    $('#save_order').on('click', saveAllData);

    /**
     * Кнопка сохраниения заказа
     */
    $('#save_order_sending').on('click', saveAllDataSending);

    /**
     *  устанавливаем значение target_status по нажатиб на таб
     */
    $('.targets .target a').on('click', function () {
        $(this).find('input').prop('checked', true);
    });

    $('#annul_moderation').on('click', function () {
        showMessage('processing');

        $.post('/ajax/annul-moderation/' + window.orderId, function (json) {

            if (json.success) {
                showMessage('success', json.message);
            } else {
                showMessage('error', json.message);
            }

        }).fail(function (json) {
            showMessage('error');
        });

        return false;
    });

    $('#new_target_user').select2({
        ajax: {
            method: 'get',
            url: '/user/find',
            dataType:
                'json',
            data:
                function (params) {
                    return {
                        query: $.trim(params)
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
});

function addComment() {
    var objComment = $('.field_comment');
    var comment = objComment.val();
    if (!comment) {
        return false;
    }
    $.post('/ajax/add-comment/', {orderId: orderId, comment: comment}, function (json) {
        var obj = $('#comment_block');
        obj.empty();
        obj.prepend(json.html);
        // getSlimScroll(obj);
        $('.order_date_change').text(json.dateChange);
        objComment.val('');
    }, 'json').fail(function (json) {
        showMessage('error');
    });
}

/**
 * Удаляем ошибки input
 */
function deleteError(e) {
    $(e.currentTarget).parent()
        .removeClass('has-error');
}

/**
 * Календарик
 */
function myDatepicker(obj) {
    var start = new Date(), prevDay, startHours = 1;
    start.setHours(1);
    start.setMinutes(0);
    obj.datepicker({
        //dateFormat: ' ',
        timeFormat : 'hh:ii',
        timepicker: true,
        language: 'en',
        startDate: start,
        minHours: startHours,
        maxHours: 18,
        onSelect: function (fd, d, picker) {
            if (!d) {
                return;
            }
            var day = d.getDay();
            if (prevDay != undefined && prevDay == day) {
                return;
            }
            prevDay = day;
            picker.update({
                minHours: 1,
                maxHours: 23
            });
        }
    });
}

/*
 * Управляем checkbox для up sell и cross sell
 */
function checkBoxUpCrossSell(e) {
    var obj = $(e.currentTarget).parents('tr')
        .find('.up_cross_sell');
    var count = obj.length;
    var objCount = 0;
    for (var i = 0; i < count; i++) {
        if (obj.eq(i).prop("checked")) {
            objCount++;
        }
    }
    if (objCount > 1) {
        $(e.currentTarget).removeAttr('checked');
    }
}


/**
 * Поиск по offers
 */
function searchOffers(e) {
    if ((e.which >= 48 && e.which <= 90) || (e.which >= 186 && e.which <= 220) || (e.which >= 96 && e.which <= 111) || e.which == 8 || e.which == 46) {
        setTimeout(function () {
            var search = $(e.currentTarget).val();
            if (!search) {
                $('.search_block').empty();
                $('.search_block').css('display', 'none');
                return false;
            }
            $.post('/order-search-offers-ajax/', {orderId: orderId, search: search}, function (json) {
                $('.search_block').empty();
                $('.search_block').append(json.html);
                $('.search_block').css('display', 'block');

                $('.price_offer').on('keyup', calculatePrice);
                calculatePrice();
                /**
                 * добовляем товар из поиска
                 */
                $('.search_block .add_product').on('click', addNewProduct);
            }, 'json');
        }, 200);
    }
}

/**
 * Добавляем товар из поиска
 */
function addNewProduct() {
    var productId = $(this).attr('data-id'),
        price = $(this).parents('tr').find('.price_offer_add').val();
    $(this).parents('tr').find('.has-error').removeClass('has-error');
    if (!(price / 1 && productId / 1)) {
        $(this).parents('tr').find('.price_offer_add').parents('td').addClass('has-error');
        return false;
    }
    showMessage('processing');
    $.post('/add-new-product-ajax', {productId: productId, price: price, orderId: window.orderId}, function (json) {
        if (json.error) {
            showMessage('error', json.message);
            $(this).parents('tr').find('.price_offer_add').parents('td').addClass('has-error');
        } else if (json.success) {
            $('.table_products tbody').empty();
            $('.table_products tbody').append(json.success.html);
            $('#order-price').val(json.success.price);
            if ($("[data-product-description='1']").length) {
                $("[data-product-description='1']").val(json.success.productsActiveList);
            }

            showMessage('success', json.message);
            $('.price_offer').on('keyup', calculatePrice);
            addEditableForComments();
        }
        calculatePrice();
    });
    return false;
}

function delProduct() {
    var id = $(this).find('span').attr('data-id');
    showMessage('processing');
    $.post('/delete-products-from-order', {productId: id}, function (json) {
        if (json.disabled) {
            var tr = $('tr [data-id="' + id + '"').parents('tr'),
                price = tr.find('.price_offer');
            $(tr).find('input[name $= "[disabled]"]').val(1);
            price.parents('td').append(price.val());
            price.remove();
            tr.find('.delete_product').remove();
            tr.addClass('warning');
            if ($("[data-product-description='1']").length) {
                $("[data-product-description='1']").val(json.productsActiveList);
            }
            showMessage('success', json.message);
        }
        if (json.success) {
            $('tr [data-id="' + id + '"').parents('tr').remove();
            showMessage('success', json.message);
        }
        $('.price_offer').on('keyup', calculatePrice);
        calculatePrice();
    });
    return false;
}

function customTabs() {
    $('.result .tab-content .active').removeClass('active').removeClass('in');
    $('.result .tab-content #def').addClass('active').addClass('in');
    $(this).parent('li').removeClass('active');

    $('#target_status_def').prop('checked', true);
}

function showSubMeny() {
    if ($(this).prop("checked") === true) {
        var count = $(this).parents('ul').find('li').length;
        for (var i = 0; i < count; i++) {
            var li = $(this).parents('ul').find('li').eq(i);
            li.find('input.' + $(this).attr('class')).removeAttr('checked');
        }
        $(this).parents('ul').find('ul').css('display', 'none');
        $(this).parents('li').find('ul:first').css('display', 'block');
        $(this).parents('li:first').find('ul:first').find('[type="checkbox"]').removeAttr('checked');


        var count = $('.now').length;
        for (var i = 0; i < count; i++) {
            $('.now').eq(i).prop('checked', false);
            $('.callback_date').eq(i).prop('disabled', false);
            $('.callback_date').eq(i).val('');
        }
    }
    $(this).prop('checked', true);
}

function showRepeats() {
    if ($(this).hasClass('collapsed')) {
        $.post('/repeat-orders-in-order-ajax', {id: window.orderId}, function (json) {
            $('#collapseOne .table-responsive').empty();
            $('#collapseOne .table-responsive').append(json.html);

            $('a.pop').hover(function () {
                $(this).siblings('.data_popup').fadeToggle(100);

            });
            $('a.pop').on('click', function () {
                return false;
            });
        });

    }
}

// function changeInfoClient() {
//     showMessage('processing');
//     $.post('/save-contact-data-client-ajax/' + window.orderId, $(this).serialize(), function (json) {
//         $('#info_client').find('.has-error').removeClass('has-error');
//         if (json.errors) {
//             for (key in json.errors) {
//                 $('#' + key).parents('.form-group').addClass('has-error');
//             }
//             showMessage('error', 'Ошибка');
//         }
//
//         if (json.status) {
//             showMessage('success', 'Данные изменены');
//         }
//     }, 'json');
// }

function changeTarget() {
    var targetId = $(this).val();
    var targetName = $(this).attr('name');
    var parent = $(this).parents('.result').find('.tab-pane.active').find('.target_fields');

    var integration = $(this).find('option:selected').attr('data-alias');

    $.post('/ajax/order/change-target-in-order', {
        targetId: targetId,
        orderId: window.orderId,
        targetName: targetName,
        integration: integration
    }, function (json) {
        if (json.success) {
            parent.empty();
            parent.append(json.html);

            showMessage('success', json.message);
        } else {
            showMessage('error', json.message);
        }
    }).fail(function (json) {
        showMessage('error');
    });
}

function getMessages(json) {
    let messages = '';
    if (json.messages) {
        messages = createAlertsHtml(json);
        $('#order_data').find('has-error').removeClass('has-error');
        $('.ns-close').click();
    } else {
        showMessage('error')
    }
    if (messages.length) {
        $('.error-messages').empty();
        $('.error-messages').append(messages);
        $('#order_data .error-messages').slideDown();

    }

    $('.alert .close').on('click', setStyleErrorBlock);
}

function saveAllData() {

    if (!$('#order_data .error-messages .alert').length) {
        $('#order_data .error-messages').css("display", "none");
    }

    showMessage('processing');
    $('#order_data .has-error').removeClass('has-error');
    var data = $('#order_data').serialize();

    $.post('/ajax/orders/' + window.orderId + '/save-order-data', data, getMessages).fail(getValidationMessages);
}

function saveAllDataSending() {
    if (!$('#order_data .error-messages .alert').length) {
        $('#order_data .error-messages').css("display", "none");
    }

    showMessage('processing');
    $('#order_data .has-error').removeClass('has-error');
    var data = $('#order_data').serialize();

    $.post('/ajax/orders/' + window.orderId + '/save-order-sending-data', data, getMessages).fail(getValidationMessages);
}

function calculatePrice() {
    var price = 0;
    $('.price_offer').each(function () {
        if (parseInt($(this).val())) {
            price += parseInt($(this).val());
        }

    });
    $('#total_price').empty();
    $('#total_price').text(price);
    $('#order-price').val(price);
}

function addEditableForComments() {
    $('.product_comments').editable({
        type: 'textarea',
        url: '/ajax/order/add-comment-for-product',
        ajaxOptions: {
            type: 'post',
            dataType: 'json'
        },
        success: function (data, config) {
            if (data.success) {
                showMessage('success', data.message)
            } else {
                showMessage('error', data.message)
            }

        },
        error: function (errors) {
            showMessage('error');
        }
    });

    $('.product_option').editable({
        url: '/ajax/product_option/save',
        ajaxOptions: {
            type: 'post',
            dataType: 'json'
        },
        select2: {
            allowClear: true,
            width: '170px',
            ajax: {
                url: '/product_options/find',
                dataType: 'json',
                data: function (params) {
                    var tr = $(this).parents('tr');
                    var product_id = tr.find('.product_option').attr('data-product');
                    return {
                        q: $.trim(params),
                        product_id: product_id,
                    };
                },
                results: function (data) {
                    return {
                        results: data,
                    };
                }
            }
        },
        validate: function (value) {
            if ($.trim(value) == '') {
                return $('#messages_alert #field_required').text().trim();
            }
        },
        success: function (data, config) {
            if (data.success) {
                showMessage('success', data.message)
            } else {
                showMessage('error', data.message);
            }

        },
        error: function (errors) {
            showMessage('error');
        }
    });
}

function setStyleErrorBlock() {
    if ($('#order_data .error-messages .alert').length == 1) {
        $('#order_data .error-messages').slideUp();
    }
}

$(function () {
    $(window).on('scroll', function () {
        var scrollPos = $(document).scrollTop();
        $('#block-content').css({
            top: scrollPos
        });
        $('#block-objections').css({
            top: scrollPos
        });
        $('#block-other-questions').css({
            top: scrollPos
        });
    }).scroll();
});

$('a#link-block').click(function (e) {
    var block = $(this).attr('href');
    $('div.initial-block').children('blockquote').removeClass('block');
    // $('div.initial-block').css('opacity', '0.5');
    $('div.initial-block').children('blockquote').css('border-color', 'rgba(194, 199, 226, 0.29)');
    $('div' + block).children('blockquote').addClass('block').css('opacity', '1');
    $('div' + block).children('blockquote').css('border-color', '#1ABC9C');

    $('a#link-block').closest('div').removeClass('current_block');
    $(this).closest('div').addClass('current_block');
});

$('#config-tool #config-tool-options ul.nav.nav-tabs li a').click(function (e) {
    $('#config-tool').addClass('closed');
});
$('.log-inner').slimScroll({
    height: '150px',
    wheelStep: 35,
});
$('.block-inner').slimScroll({
    height: '500px',
    wheelStep: 35,
});

$(document).ready(function () {
    $(document).on("scroll", onScroll);

    //smoothscroll
    $('#block-content a[href^="#"]').on('click', function (e) {
        e.preventDefault();
        $(document).off("scroll");

        var target = this.hash,
            menu = target;
        $target = $(target);
        $('html, body').stop().animate({
            'scrollTop': $target.offset().top + 2
        }, 500, 'swing', function () {
            window.location.hash = target;
            $(document).on("scroll", onScroll);
        });
    });
});

function onScroll(event) {
    var scrollPos = $(document).scrollTop();

    $('a#link-block').each(function () {
        var currLink = $(this);
        var refElement = $(currLink.attr("href"));
        if (refElement.position().top <= scrollPos && refElement.position().top + refElement.height() > scrollPos) {
            $('a#link-block').closest('div').removeClass('current_block');
            currLink.closest('div').addClass("current_block");
        }
        else {
            currLink.closest('div').removeClass("current_block");
        }
    });

    $('div .initial-block').each(function () {
        var currDiv = $(this);

        if (currDiv.position().top <= scrollPos && currDiv.position().top + currDiv.height() > scrollPos) {
            $('div .initial-block').children('blockquote').removeClass('block');
            currDiv.children('blockquote').addClass("block").css('border-color', '#1ABC9C');
            ;
        }
        else {
            currDiv.children('blockquote').removeClass("block").css('border-color', 'rgba(194, 199, 226, 0.29)');
        }
    });
}

$('a.return-to-order').click(function () {
    $("li.config-li").removeClass('active');
    $("li.config-li.order-li").addClass('active');
});
$(window).ready(function () {
    // var start = Number($('span.proc_stage').text());
    // $('.slider-minmax').closest('.noUi-handle.noUi-handle-lower').prop('title', start);
    // $('.noUi-handle.noUi-handle-lower').addClass('simple-slider');
});

//разделить с модерацией
// $(document).ready(function () {
//     $(".alert").alert();
//     var start = Number($('span.proc_stage').text());
//     //min/max slider
//     $('.slider-minmax').noUiSlider({
//         range: [0, 13],
//         start: [start],
//         handles: 1,
//         step: 1,
//         connect: 'lower',
//         slide: function () {
//             var val = $(this).val();
//
//             $(this).next('span').text(
//                 'Этап прозвона: ' + Math.round(val)
//             );
//         },
//         set: function () {
//             var val = $(this).val();
//             window.contentvalue = Math.round(val)
//
//             $(this).next('span').text(
//                 'Этап прозвона:' + Math.round(val)
//             );
//             $('.noUi-handle.noUi-handle-lower').attr('title', Math.round(val));
//             $('.noUi-handle.noUi-handle-lower').addClass('simple-slider');
//         }
//     });
// });
var integration = $('.target').find('option:selected').attr('data-alias');
var integrationSending = $('#integration').find('option:selected').attr('data-alias');

if (integration == 'novaposhta' || integrationSending == 'novaposhta') {
    $("#approve-city").on("select2:select", function (e) {
        var select_val = $(e.currentTarget).val();
    });


}
$('#sender').change(function () {
    if ($('#sender').find(':selected').attr('data-value')) {
        var senderData = JSON.parse($('#sender').find(':selected').attr('data-value'));
        $('#volume_general').val(senderData.size);
        $('#weight').val(senderData.weight);
        $('#description').val(senderData.description);
    } else {
        $('#volume_general').val('');
        $('#weight').val('');
    }
});
if ($('#sender').find(':selected').attr('data-value')) {
    var senderData = JSON.parse($('#sender').find(':selected').attr('data-value'));
    $('#volume_general').val(senderData.size);
    $('#weight').val(senderData.weight);
}

function createAlertsHtml(json) {
    let messages = '';
    if (json.messages) {
        $.each(json.messages, function (id, messagesResponse) {
            let cl, icon;
            if (id == 'success') {
                cl = 'alert-success';
                icon = 'check';
            } else {
                cl = 'alert-danger';
                icon = 'times';
            }

            $.each(messagesResponse, function (id, text) {
                messages += '<div class="alert ' + cl + ' fade in"> ' +
                    '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> ' +
                    '<i class="fa fa-' + icon + '-circle fa-fw fa-lg"></i> ' + text +
                    '</div>';
            });
        });
    }

    return messages;
}




