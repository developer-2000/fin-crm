$(function () {
    /* ID заказа */
    window.orderId = $('.order_id').text();

    /**
     * reverse one order from pass
     */
    $('#reverse_one_order').on('click', reverseOneProduct);

    /**
     * Поиск по offers
     */
    $('.searchForLocked').on('keydown', searchProductsForLocked);
    /**
     * удаления товара из списка без удаления из бд
     */
    $('.delete_product_locked').on('click', deleteProductLocked);


    function reverseOneProduct() {
        $.post('/pass/reversal', $('#form_locked').serialize() + '&' + $.param({
            order_id: $('.order_id').text(),
            pk:  $('#reverse_one_order').attr('data-pk'),
        }), function (json) {
            if (json.success) {
                window.location = window.location.origin + window.location.pathname + "?tab=change_locked_order_data"
            }else if (json.errors){
                getMessage('error', json.errors);
            }
        });
    }

    /**
     * Поиск по offers
     */
    function searchProductsForLocked(e) {
        if ((e.which >= 48 && e.which <= 90) || (e.which >= 186 && e.which <= 220) || (e.which >= 96 && e.which <= 111) || e.which == 8 || e.which == 46) {
            setTimeout(function () {
                var search = $(e.currentTarget).val();
                if (!search) {
                    $('.search_block_locked').empty();
                    $('.search_block_locked').css('display', 'none');
                    return false;
                }
                $.post('/order-search-offers-ajax/', {orderId: orderId, search: search}, function (json) {
                    $('.search_block_locked').empty();
                    $('.search_block_locked').append(json.html);
                    $('.search_block_locked').css('display', 'block');
                    // $('.price_product_locked').on('keyup', calculatePrice);
                    // calculatePrice();
                    /**
                     * добовляем товар из поиска
                     */
                    $('.search_block_locked .add_product').click(function () {
                        var productId = $(this).attr('data-id'),
                            price = $(this).parents('tr').find('.price_offer_add').val();
                        $.post('/add-new-product-locked-ajax', {
                            productId: productId,
                            price: price,
                            orderId: window.orderId
                        }, function (json) {
                            if (json.error) {
                                getMessage('error', 'Произошла ошибка');
                                $(this).parents('tr').find('.price_offer_add').parents('td').addClass('has-error');
                            } else if (json.success) {
                                $('.table_products_locked tbody  tr.row-total').before(json.success.html);
                                $('.price_product_locked').on('keyup', updateTotalPrice);
                                $('.cost_locked').on('keyup', updateTotalPrice);
                                updateTotalPrice();
                                addEditableForComments();
                            }
                            $('.delete_product_locked').on('click', deleteProductLocked);
                        });
                        return false;
                    })
                }, 'json');
            }, 200);
        }

        /**
         * закрытие поиска
         */
        $(document).mouseup(function (e) {
            var container = $(".search_block_locked");
            if (container.has(e.target).length === 0) {
                container.hide();
            }
        });

        /**
         * Добавляем комментарий
         */
        $('.add_comment').on('click', addComment);

        function addEditableForComments() {
            $('.product_option').editable({
                url: '/ajax/product_option/save',
                ajaxOptions: {
                    type: 'post',
                    dataType: 'json'
                },
                emptytext: 'Выберите опцию',
                select2: {
                    placeholder: 'Выберите опцию',
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
                        return 'Заполните поле!';
                    }
                },
                success: function (data, config) {
                    if (data.success) {
                        getMessage('success', "Успешно изменен")
                    } else {
                        getMessage('error', "Произошла ошибка!");
                    }

                },
                error: function (errors) {
                    getMessage('error', "Произошла ошибка на сервере!");
                }
            });
        }

    }

    function deleteProductLocked() {
        event.preventDefault();
        $(this).attr('data-id');
        $("input[name='products[" + $(this).attr('data-id') + "][disabled]']").val(1);
        if ($("input[name='products[" + $(this).attr('data-id') + "][price]']").length > 0) {
            $("input[name='products[" + $(this).attr('data-id') + "][price]']").removeClass('price_product_locked').attr('disabled', true);
        } else {
            $("input[name='products_new[" + $(this).attr('data-id') + "][price]']").removeClass('price_product_locked').attr('disabled', true);
        }

        $("tr[data-id = " + $(this).attr('data-id') + "]").addClass('warning');
        $("a[data-id = " + $(this).attr('data-id') + "]").remove();
        updateTotalPrice();
    }

    $('.price_product_locked').on('keyup', updateTotalPrice);
    $('.cost_locked').on('keyup', updateTotalPrice);

    function updateTotalPrice() {
        var price = 0;
        var cost = parseInt($('#cost').val());
        $('.price_product_locked').each(function () {
            if (parseInt($(this).val())) {
                price += parseInt($(this).val());
            }
        });

        $('#total_price_locked').empty();
        $('#total_price_locked').val(price);
        var income = Number(price) + cost;
        $('#income').empty();
        $('#income').text(income);
    }

    $('#total_price_locked').keyup(function () {
        var cost = parseInt($('#cost').val());
        var price = $('#total_price_locked').val();
        var income = parseInt(price) + cost;
        $('#income').empty();
        $('#income').text(income);
    });

    $(document).on("click", 'button.editable-submit', function (e) {
        event.preventDefault();
        $.post('/save-order-locked-changes', $('#form_locked').serialize() + '&' + $.param({
            order_id: $('.order_id').text()
        }), function (json) {
            if (json.success) {
                window.location = window.location.origin + window.location.pathname + "?tab=change_locked_order_data"
            }else if (json.errors){
                getMessage('error', json.errors);
            }
        });
    });

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
    $.fn.editable.defaults.mode = 'popup';
    $('.save-order-changes').editable({
        type: 'none',
        html : true,
        pk: 1,
        tpl: '',
    });

    var options = {
        attach: '.info-tooltip',
        getTitle: 'data-title',
        getContent: 'data-content',
        theme: 'TooltipBorderThick',
        maxWidth : 300,
    };
    new jBox(options);
    new jBox('Tooltip', options);

    var options = {
        attach: '.operations-tooltip',
        getTitle: 'data-title',
        getContent: 'data-content',
        theme: 'TooltipBorderThick',
        maxWidth : 300,
    };
    new jBox(options);
    new jBox('Tooltip', options);
});

