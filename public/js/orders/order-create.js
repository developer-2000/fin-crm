$(function () {
    $('#partner_id').on('change', getData);
    $('#project_id').on('change', resetSubProjectValue);
    $('#search_product').on('keydown', searchProduct);
    $('#country').on('change', resetTarget);
    $('#target_id').on('change', changeTarget);
    $('#create_order').on('submit', createOrder);
    setSelect2();
    setDefaultValue();


    $('.delete_product').on('click', deleteProduct);
    $('.product_price').on('change', calculatePrice);

    /**
     * закрытие поиска
     */
    $(document).mouseup(function (e) {
        var container = $("#search_block");
        if (container.has(e.target).length === 0) {
            container.hide();
        }
    });
});

function setDefaultValue() {
    var inputs = [
        'project_id',
        'sub_project_id',
        'offer_id',
        'target_id'
    ];

    for (key in inputs) {
        var defData = $('#' + inputs[key]).attr('data-content');
        if (defData) {
            $('#' + inputs[key]).select2("data", JSON.parse(defData));
        }
    }

}

function setSelect2() {
    $('.select2').each(function () {
        var settings = {
            width: '100%'
        };

        switch ($(this).attr('id')) {
            case 'sub_project_id' :
                settings = {
                    ajax: {
                        url: '/sub_projects/find',
                        dataType: 'json',
                        data: function (params) {
                            return {
                                query: $.trim(params),
                             //   partner_id: $('#partner_id').val(),
                                project_id: $('#project_id').val(),
                            };
                        },
                        results: function (data) {
                            return {
                                results: data,
                            };
                        }
                    },
                    width: '100%'
                };
                break;
            case 'project_id' : {
                settings = {
                    ajax: {
                        url: '/projects/find',
                        dataType: 'json',
                        data: function (params) {
                            return {
                                query: $.trim(params),
                               // partner_id: $('#partner_id').val()
                            };
                        },
                        results: function (data) {
                            return {
                                results: data,
                            };
                        }
                    },
                    width: '100%'
                };
                break;
            }
            case 'offer_id' : {
                settings = {
                    ajax: {
                        url: '/offer/find',
                        dataType: 'json',
                        data: function (params) {
                            return {
                                q: $.trim(params),
                                partner_id: $('#partner_id').val() ? [$('#partner_id').val()] : [],
                            };
                        },
                        results: function (data) {
                            return {
                                results: data,
                            };
                        }
                    },
                    width: '100%'
                };
                break;
            }
            case 'target_id' : {
                settings = {
                    ajax: {
                        url: '/target/find',
                        dataType: 'json',
                        data: function (params) {
                            return {
                                q: $.trim(params),
                                country_code: $('#country').val(),
                            };
                        },
                        results: function (data) {
                            return {
                                results: data,
                            };
                        }
                    },
                    width: '100%'
                };
                break;
            }
        }

        $(this).select2(settings);
    });

}

function searchProduct(e) {
    if ((e.which >= 48 && e.which <= 90) || (e.which >= 186 && e.which <= 220) || (e.which >= 96 && e.which <= 111) || e.which == 8 || e.which == 46) {
        setTimeout(function () {
            var search = $(e.currentTarget).val(),
                projectId = $('#project_id').val();
            var subProjectId = $('#sub_project_id').val();
            if (!search) {
                $('#search_block').empty();
                $('#search_block').css('display', 'none');
                return false;
            }
            $.post('/ajax/search-product', {
                search: search,
                project_id: projectId,
                subProjectId: subProjectId
            }, function (json) {
                $('#search_block').empty();
                $('#search_block').append(json.html);
                $('#search_block').css('display', 'block');
                /**
                 * добовляем товар из поиска
                 */
                $('#search_block .add_product').on('click', addNewProduct);
            }, 'json');
        }, 200);
    }
}

function calculatePrice() {
    var price = 0;
    $('.product_price').each(function () {
        if (parseInt($(this).val())) {
            price += parseInt($(this).val());
        }

    });
    $('#total_price').empty();
    $('#total_price').text(price);

    renameProduct();
}

function renameProduct() {
    var productsTr = $('#products tbody tr')
    i = 0;
    if (productsTr.length) {
        $.each(productsTr, function (index, object) {
            var id = 'products[' + i + '][';
            $(object).find('.product_price').attr('id', id + 'product_price]');
            $(object).find('.product_price').attr('name', id + 'product_price]');
            $(object).find('input[type="hidden"]').attr('id', id + 'product_id]');
            $(object).find('input[type="hidden"]').attr('name', id + 'product_id]');
            i++;
        })
    }
}

function addNewProduct() {
    var productId = $(this).attr('data-id'),
        price = $(this).parents('tr').find('.price_offer_add').val();
    $(this).parents('tr').find('.has-error').removeClass('has-error');

    if (!(price / 1 && productId / 1)) {
        $(this).parents('tr').find('.price_offer_add').parents('td').addClass('has-error');
        return false;
    }

    var clone = $(this).parents('tr').clone(),
        deleteButton = '<a href="#" class="table-link danger delete_product"> ' +
            '<span class="fa-stack"> ' +
            '<i class="fa fa-square fa-stack-2x"></i> ' +
            '<i class="fa fa-trash-o fa-stack-1x fa-inverse"></i> ' +
            '</span> ' +
            '</a>' +
            '<input type="hidden" name="product_id" value="' + productId + '">';
    clone.find('.price_offer_add').removeClass('price_offer_add').addClass('product_price');
    clone.find('td:first-of-type').addClass('value');
    clone.find('td:last-of-type').empty();
    clone.find('td:last-of-type').append(deleteButton);

    $('#products tbody tr.price_product').before(clone);
    $('.delete_product').on('click', deleteProduct);
    $('.product_price').on('change', calculatePrice);

    calculatePrice();

    return false;
}

function changeTarget() {
    var targetId = $(this).val(),
        block = $('#target_block');
    $.post('/ajax/order/change-target-in-order', {
        targetId: targetId,
    }, function (json) {
        if (json.success) {
            block.empty();
            block.append(json.html);
            showMessage('success', json.message);
        } else {
            showMessage('error', json.message);
        }
    }).fail(function (json) {
        showMessage('error');
    });
}

function createOrder() {
    disableButton($('#btn_create'), true);
    showMessage('processing');
    $.post('/ajax/orders/create', $(this).serialize(), function (json) {
        let messages = '';
        if (json) {
            let message = json.message;
            if (message.length) {
                messages += '<div class="alert alert-success fade in"> ' +
                    '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> ' +
                    '<i class="fa fa-check-circle fa-fw fa-lg"></i> ' + message +
                    '</div>';
            }
            $('#create_order').find('has-error').removeClass('has-error');
            $('.ns-close').click();

            showMessage('success', json.alert)
        } else {
            showMessage('error')
        }
        if (messages.length) {
            $('#create_order .error-messages').empty();
            $('#create_order .error-messages').append(messages);
            $('#create_order .error-messages').slideDown();
        }

        disableButton($('#btn_create'));
    }).fail(function (json) {
        try {
            var response = JSON.parse(json.responseText);
            if (response.errors) {
                var messages = '';
                $.each(response.errors, function (name, value) {
                    var message = '';
                    var fieldName = deleteDoted(name);
                    if (fieldName) {
                        $.each(value, function (key, error) {
                            var obj = document.getElementById(fieldName);
                            var parent = $(obj).parents('.form-group');
                            var label = $('label[for="' + fieldName + '"]').text();

                            if (!label.length) {
                                label = parent.find('label').text();
                            }
                            if (!label.length) {
                                label = name;
                            }

                            parent.find('.help-block').remove();
                            parent.addClass('has-error');

                            name = name.split('_').join(' ');

                                message = error.replace(name, '<strong>"' + label + '"</strong>');

                            if (message.length) {
                                messages += '<div class="alert alert-danger fade in"> ' +
                                    '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> ' +
                                    '<i class="fa fa-times-circle fa-fw fa-lg"></i> ' + message +
                                    '</div>';
                            }
                        });
                    }
                });
                if (messages.length) {
                    $('#create_order .error-messages').empty();
                    $('#create_order .error-messages').append(messages);
                    $('#create_order .error-messages').slideDown();
                }
                $('.ns-close').click();

            } else {
                showMessage('error');
            }
        } catch (e) {
            showMessage('error');
        }

        disableButton($('#btn_create'));
    });

    return false;
}

/**
 * удаление товара
 */
function deleteProduct() {
    var tr = $(this).parents('tr');

    tr.fadeOut(300);
    setTimeout(function () {
        tr.remove();

        calculatePrice();
    }, 300);

    return false;
}

function getData() {
    $('#project_id').select2("val", "");
    resetSubProjectValue();
    emptyProducts();
}

function resetSubProjectValue() {
    $('#sub_project_id').select2("val", "");
    $('#offer_id').select2("val", "");
}

function emptyProducts() {
    $('#products tbody tr:not(.price_product)').fadeOut(300);

    setTimeout(function () {
        $('#products tbody').children().not('.price_product').remove();

        calculatePrice();
    }, 300);
}

function resetTarget() {
    $('#target_id').select2("val", "");
    $('#target_block div').fadeOut(300);

    setTimeout(function () {
        $('#target_block').empty();
    }, 300);
}

function deleteDoted(fieldName) {
    var params = fieldName.split('.');
    var result = fieldName;
    if (params.length > 1) {
        result = '';
        for (var i = 0; i < params.length; i++) {
            if (i == 0) {
                result += params[i] + '[';
            } else if (i + 1 != params.length) {
                result += params[i] + '][';
            } else if (i + 1 == params.length) {
                result += params[i] + ']';
            }
        }
    }

    return result;
}