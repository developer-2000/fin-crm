$(function () {
    $(document).on("change", ':checkbox[name="fixed-rate-success"]', fixedRateSuccess);
    $(document).on("change", ':checkbox[name="fixed-rate-failed"]', fixedRateFailed);
    $(document).on("change", ':checkbox[name="rate-success"]', rateSuccess);
    $(document).on("change", ':checkbox[name="rate-failed"]', rateFailed);
    $(document).on("change", ':checkbox[name="product-type-action[]"]', productTypeActionCondition);
    $(document).on("change", '#type-method', getMethod);
    $(document).on("change", '#type-object', getObject);
    $(document).on("change", 'input:radio[name="basis-for-action"]', getOptionForBasis);
    $(document).on("change", 'input:radio[name="basis-for-schedule"]', getOptionForCalculation);
});

$("#company_id").change(function () {
    window.company_id = $(this).val();
    $.getJSON('/plan-get-default-prices-ajax/' + window.company_id, function (data) {
        window.companyType = data.company.type;
        window.companyBillingType = data.company.billing_type;
        $('#type-object').attr('required',true).attr('disabled', false);
        });
});

function productTypeActionCondition() {
    if ($(this).is(':checked') && ($(this).val() == 'approve')) {
        $('#base_up_sell_1').attr('disabled', true).attr('checked', false);
        $('#base_up_sell_2').attr('disabled', true).attr('checked', false);
        $('#base_cross_sell').attr('disabled', true).attr('checked', false);
    }
    else {
        $('#base_up_sell_1').attr('disabled', false);
        $('#base_up_sell_2').attr('disabled', false);
        $('#base_cross_sell').attr('disabled', false);
    }
}

function  getObject() {
    if( typeof window.companyBillingType != 'undefined' && window.companyBillingType == 'hour'
    || typeof window.companyType != 'undefined' && window.companyType == 'hour') {
        $('#type-method option[value="schedule"]').attr('selected', true);
        var inputs = $('#basis-for-schedule').clone().html();
        $('.type-method').empty();
        $('.type-method').append(inputs);

        $.getJSON('/plan-get-default-prices-ajax/' + window.company_id, function (data) {

                window.companyType = data.company.type;
                prices = JSON.parse(data.company.prices);
                billing = JSON.parse(data.company.billing);

                if ($("#type-object").val() == 'company') {
                    $('.rate_block').empty();
                    var inputs = $('#rate_block_' + window.companyType).clone().html();
                    $('.type-payment').empty();
                    $('.rate_block').append(inputs);

                    $('#approve-bonus-def').val(prices.approve);
                    $('#up_sell-bonus-def').val(prices.up_sell);
                    $('#up_sell_2-bonus-def').val(prices.up_sell_2);
                    $('#cross_sell-bonus-def').val(prices.cross_sell);

                    $('#in_system-success-def').val(prices.in_system);
                    $('#in_talk-success-def').val(prices.in_talk);
                    $('#in_system-failed-def').val(prices.in_system);
                    $('#in_talk-failed-def').val(prices.in_talk);
                }
                if ($("#type-object").val() == 'operator') {
                    window.companyType = data.company.type;
                    $('.rate_block').empty();
                    var inputs = $('#rate_block_' + window.companyType).clone().html();
                    $('.type-payment').empty();
                    $('.rate_block').append(inputs);

                    $('#approve-bonus-def').val(billing.billing_approve);
                    $('#up_sell-bonus-def').val(billing.billing_up_sell);
                    $('#up_sell_2-bonus-def').val(billing.billing_up_sell_2);
                    $('#cross_sell-bonus-def').val(billing.billing_cross_sell);


                    $('#in_system-success-def').val(prices.billing_in_system);
                    $('#in_talk-success-def').val(prices.billing_in_talk);

                    $('#in_system-failed-def').val(billing.billing_in_system);
                    $('#in_talk-failed-def').val(billing.billing_in_talk);
                }
                /*разрешение на редактирование полей формы*/
                $('#operator-group').attr("disabled", false);
                $('.user-select2').select2('enable');
                $('.user-group-select2').select2('enable');
                $('.country-select2').select2('enable');
                $('.offers-select2').select2('enable');
                $('#up_sell_1_except').attr("disabled", false);
                $('#up_sell_2_except').attr("disabled", false);
                $('#cross_sell_except').attr("disabled", false);
                $('.products-select2').select2('enable');
                $('#up_sell_1').attr("disabled", false);
                $('#up_sell_2').attr("disabled", false);
                $('#cross_sell').attr("disabled", false);
                $('#fixed-rate').attr("disabled", false);
                $('#operator-group-except').attr("disabled", false);
                $('.user-select2-except').select2('enable');
                $('.user-group-select2-except').select2('enable');
                $('.country-select2-except').select2('enable');
                $('.offers-select2-except').select2('enable');
                $('.products-select2-except').select2('enable');
            }
        );
    }
    if(typeof window.companyBillingType != 'undefined' && window.companyBillingType == 'lead'
        || typeof window.companyType != 'undefined' && window.companyType == 'lead'){
        $('#type-method').attr('disabled', false).attr('required', true);
    }
}

function  getMethod() {
    if ($(this).val() == "schedule") {
        var inputs = $('#basis-for-schedule').clone().html();
        $('.type-method').empty();
        $('.type-method').append(inputs);
    }

    if ($(this).val() == "action") {
        var inputs = $('#basis-action').clone().html();
        $('.type-method').empty();
        $('.type-method').append(inputs);
    }

    $.getJSON('/plan-get-default-prices-ajax/' + window.company_id, function (data) {

            window.companyType = data.company.type;
            prices = JSON.parse(data.company.prices);
            billing = JSON.parse(data.company.billing);

            if ($("#type-object").val() == 'company') {
                $('.rate_block').empty();
                var inputs = $('#rate_block_' + window.companyType).clone().html();
                $('.type-payment').empty();
                $('.rate_block').append(inputs);

                $('#approve-bonus-def').val(prices.approve);
                $('#up_sell-bonus-def').val(prices.up_sell);
                $('#up_sell_2-bonus-def').val(prices.up_sell_2);
                $('#cross_sell-bonus-def').val(prices.cross_sell);

                $('#approve-retention-def').val(prices.approve);
                $('#up_sell-retention-def').val(prices.up_sell);
                $('#up_sell_2-retention-def').val(prices.up_sell_2);
                $('#cross_sell-retention-def').val(prices.cross_sell);

                $('#in_system-success-def').val(prices.in_system);
                $('#in_talk-success-def').val(prices.in_talk);
                $('#in_system-failed-def').val(prices.in_system);
                $('#in_talk-failed-def').val(prices.in_talk);
            }
            if ($('#type-object').val() == 'operator') {
                window.companyType = data.company.type;
                $('.rate_block').empty();
                var inputs = $('#rate_block_' + window.companyType).clone().html();
                $('.type-payment').empty();
                $('.rate_block').append(inputs);

                $('#approve-bonus-def').val(billing.billing_approve);
                $('#up_sell-bonus-def').val(billing.billing_up_sell);
                $('#up_sell_2-bonus-def').val(billing.billing_up_sell_2);
                $('#cross_sell-bonus-def').val(billing.billing_cross_sell);


                $('#in_system-success-def').val(billing.billing_in_system);
                $('#in_talk-success-def').val(billing.billing_in_talk);
                $('#in_system-failed-def').val(billing.billing_in_system);
                $('#in_talk-failed-def').val(billing.billing_in_talk);
            }
            /*разрешение на редактирование полей формы*/
            $('#operator-group').attr("disabled", false);
            $('.user-select2').select2('enable');
            $('.user-group-select2').select2('enable');
            $('.country-select2').select2('enable');
            $('.offers-select2').select2('enable');
            $('#up_sell_1_except').attr("disabled", false);
            $('#up_sell_2_except').attr("disabled", false);
            $('#cross_sell_except').attr("disabled", false);
            $('.products-select2').select2('enable');
            $('#up_sell_1').attr("disabled", false);
            $('#up_sell_2').attr("disabled", false);
            $('#cross_sell').attr("disabled", false);
            $('#fixed-rate').attr("disabled", false);
            $('#operator-group-except').attr("disabled", false);
            $('.user-select2-except').select2('enable');
            $('.user-group-select2-except').select2('enable');
            $('.country-select2-except').select2('enable');
            $('.offers-select2-except').select2('enable');
            $('.products-select2-except').select2('enable');
        }
    );
}


function fixedRateSuccess() {
    if ($(this).prop('checked')) {
        $('#success-plan').attr('disabled', false)
    }
    else {
        $('#success-plan').attr('disabled', true)
    }
}

function rateSuccess() {
    if ($(this).prop('checked')) {
        $('#approve-bonus').attr('disabled', false);
        $('#up_sell-bonus').attr('disabled', false);
        $('#up_sell_2-bonus').attr('disabled', false);
        $('#cross_sell-bonus').attr('disabled', false);
    }
    else {
        $('#approve-bonus').attr('disabled', true);
        $('#up_sell-bonus').attr('disabled', true);
        $('#up_sell_2-bonus').attr('disabled', true);
        $('#cross_sell-bonus').attr('disabled', true);
    }
}

function rateFailed() {
    if ($(this).prop('checked')) {
        $('#approve-retention').attr('disabled', false);
        $('#up_sell-retention').attr('disabled', false);
        $('#up_sell_2-retention').attr('disabled', false);
        $('#cross_sell-retention').attr('disabled', false);
    }
    else {
        $('#approve-retention').attr('disabled', true);
        $('#up_sell-retention').attr('disabled', true);
        $('#up_sell_2-retention').attr('disabled', true);
        $('#cross_sell-retention').attr('disabled', true);
    }
}

function fixedRateFailed() {
    if ($(this).prop('checked')) {
        $('#failed-plan').attr('disabled', false)
    }
    else {
        $('#failed-plan').attr('disabled', true)
    }
}

function getOptionForBasis() {
    if ($(this).is(':checked') && $(this).val() == 'sum-each' || $(this).is(':checked') && $(this).val() == 'percent-each') {
        $('#quantity').attr('checked', false);
        $('#percent').attr('checked', false);

        $('#sum-action').attr('disabled', false);
        $('#success-plan').attr('disabled', true);
        $('#product-type-action').attr("disabled", true);
        $('#action-sum-operator').attr("disabled", false);
        $('#action-sum-value').attr("disabled", false);
        $('#action-quantity-operator').attr("disabled", true);
        $('#action-quantity-value').attr("disabled", true);
        $('#sum-percent-product-type-action').attr("disabled", false);
        $('#base_up_sell_1').attr("disabled", true).attr('checked', false);
        $('#base_up_sell_2').attr("disabled", true).attr('checked', false);
        $('#base_cross_sell').attr("disabled", true).attr('checked', false);
    }
    if ($(this).is(':checked') && $(this).val() == 'quantity' || $(this).is(':checked') && $(this).val() == 'percent') {

        $('#sum-each').attr('checked', false);
        $('#percent-each').attr('checked', false);


        $('#sum-action').attr('disabled', true);
        $('#success-plan').attr('disabled', false);
        $('#product-type-action').attr("disabled", false);
        $('#action-sum-value').attr("disabled", true);
        $('#action-quantity-operator').attr("disabled", false);
        $('#action-quantity-value').attr("disabled", false);
        $('#action-sum-operator').attr("disabled", true);
        $('#sum-percent-product-type-action').attr("disabled", true);
        $('#base_up_sell_1').attr("disabled", false);
        $('#base_up_sell_2').attr("disabled", false);
        $('#base_cross_sell').attr("disabled", false);
    }
}

function getOptionForCalculation() {
    if ($(this).is(':checked') && $(this).val() == 'sum-each' || $(this).is(':checked') && $(this).val() == 'percent-each') {

        $('#quantity').attr('checked', false);
        $('#percent').attr('checked', false);

        $('#sum-action').attr('disabled', false);
        $('#success-plan').attr('disabled', true);
        $('#product-type-action').attr("disabled", true);
        $('#action-sum-operator').attr("disabled", false);
        $('#action-sum-value').attr("disabled", false);
        $('#action-quantity-operator').attr("disabled", true);
        $('#action-quantity-value').attr("disabled", true);
        $('#sum-percent-product-type-action').attr("disabled", false);
        $('#base_up_sell_1').attr("disabled", true).attr('checked', false);
        $('#base_up_sell_2').attr("disabled", true).attr('checked', false);
        $('#base_cross_sell_1').attr("disabled", true).attr('checked', false);
        $('#approve').attr("disabled", true).attr('checked', false);
    }
    if ($(this).is(':checked') && $(this).val() == 'quantity' || $(this).is(':checked') && $(this).val() == 'percent') {

        $('#sum-each').attr('checked', false);
        $('#percent-each').attr('checked', false);


        $('#sum-action').attr('disabled', true);
        $('#success-plan').attr('disabled', false);
        $('#product-type-action').attr("disabled", false);
        $('#action-sum-value').attr("disabled", true);
        $('#action-quantity-operator').attr("disabled", false);
        $('#action-quantity-value').attr("disabled", false);
        $('#action-sum-operator').attr("disabled", true);
        $('#sum-percent-product-type-action').attr("disabled", true);
        $('#base_up_sell_1').attr("disabled", false);
        $('#base_up_sell_2').attr("disabled", false);
        $('#base_cross_sell_1').attr("disabled", false);
    }
}

//  * Поиск по офферам
$('.offers-select2').select2({
    placeholder: "Выберите оффер.",
    minimumInputLength: 1,
    multiple: true,
    ajax: {
        url: '/offer/find',
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

//  * Поиск по товарам
$('.products-select2').select2({
    placeholder: "выберите товар.",
    minimumInputLength: 1,
    multiple: true,
    ajax: {
        url: '/product/find',
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

$('.country-select2').select2({
    placeholder: "выберите страну.",
    minimumInputLength: 1,
    required: true,
    multiple: true,
    ajax: {
        url: '/country/find',
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

$('.user-group-select2').select2({
    placeholder: "выберите группу операторов.",
    minimumInputLength: 1,
    multiple: true,
    ajax: {
        url: '/user-group/find',
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
$('.user-select2').select2({
    placeholder: "выберите оператора.",
    minimumInputLength: 1,
    multiple: true,
    ajax: {
        url: '/user/find',
        dataType: 'json',
        data: function (params) {
            if ($('#company_id').val()) {
                var company_id = $('#company_id').val();
            }
            return {
                query: $.trim(params),
                company_id: company_id
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
//  * Поиск по офферам
$('.offers-select2-except').select2({
    placeholder: "Выберите оффер.",
    minimumInputLength: 1,
    multiple: true,
    ajax: {
        url: '/offer/find',
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


//  * Поиск по товарам
$('.products-select2-except').select2({
    placeholder: "выберите товар.",
    minimumInputLength: 1,
    multiple: true,
    ajax: {
        url: '/product/find',
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
/*поиск по странам*/
$('.country-select2-except').select2({
    placeholder: "выберите страну.",
    minimumInputLength: 1,
    multiple: true,

    ajax: {
        url: '/country/find',
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

$('.user-select2-except').select2({
    placeholder: "выберите оператора.",
    minimumInputLength: 1,
    multiple: true,
    ajax: {
        url: '/user/find',
        dataType: 'json',
        data: function (params) {
            if ($('#company_id').val()) {
                var company_id = $('#company_id').val();
            }
            return {
                query: $.trim(params),
                company_id: company_id
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
$('.user-group-select2-except').select2({
    placeholder: "выберите группу операторов.",
    minimumInputLength: 1,
    multiple: true,
    ajax: {
        url: '/user-group/find',
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

/**
 * Выводим сообщение
 */
function getMessage(type, message) {
    $('.ns-box').remove();
    var notification = new NotificationFx({
        message: '<span class="icon fa fa-bullhorn fa-2x"></span><p>' + message + '</p>',
        layout: 'bar',
        effect: 'slidetop',
        type: type,
        ttl: 3000,
    });

    notification.show();
}
