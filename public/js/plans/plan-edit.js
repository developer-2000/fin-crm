$(function () {
    $(document).on("change", ':checkbox[name="fixed-rate-success"]', fixedRateSuccess);
    $(document).on("change", ':checkbox[name="fixed-rate-failed"]', fixedRateFailed);
    $(document).on("change", ':checkbox[name="rate-success"]', rateSuccess);
    $(document).on("change", ':checkbox[name="rate-failed"]', rateFailed);
    $(document).on("change", ':checkbox[name="product-type-action[]"]', productTypeActionCondition);
    //$(document).on("change", 'input:select[name = "type-method"]', getTypeMethod);
    $('#myonoffswitch').on('change', changeStatusAjax);
    $('form').on('submit', update);
    $(document).on("change", 'input:radio[name="basis-for-action"]', getOptionForBasis);
    $(document).on("change", 'input:radio[name="basis-for-schedule"]', getOptionForCalculation);

});

function update() {
    $(this).find('.has-error').removeClass('has-error');
    $.post(location.pathname, $(this).serialize(), function (json) {
        if (json.errors) {
            for (key in json.errors) {
                $('#' + key).parents('.form-group').addClass('has-error');
            }
        }
        if (json.success) {
            if (!json.update) {
                getMessage('success', "План добавлен");
                $('form')[0].reset();
                getForm();
                $('.billing_block').empty();
            } else {
                getMessage('success', "План успешно изменен!");
            }
        }
    });

    return false
}

function changeStatusAjax() {
    $planId = $('#plan_id').val();
    if ($(this).prop('checked')) {
        var status = 'active';
        $.getJSON('/plan-change-status-ajax/' + $planId + '/' + status, function (data) {
        })
    }
    else {
        status = 'inactive';
        $.getJSON('/plan-change-status-ajax/' + $planId + '/' + status, function (data) {
        })
    }
}

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

$(window).load(function () {

    if ($('#type_method_hidden').val() == "schedule") {

        var inputs = $('#basis-for-schedule').clone().html();
        $('#type-method option[value="schedule"]').attr('selected', true);
        $('.type-method').empty();
        $('.type-method').append(inputs);
    }

    if ($('#type_method_hidden').val() == "action") {
        var inputs = $('#basis-for-action').clone().html();
        $('#type-method option[value="action"]').attr('selected', true);
        $('.type-method').empty();
        $('.type-method').append(inputs);
    }

    window.company_id = $('#company_id').val();
    var typesAction = $('#product_type_action').val();
    if (typesAction && typesAction == '["total"]') {
        $('#total').attr('checked', true);
    }
    if (typesAction && typesAction == '["approve"]') {
        $('#approve').attr('checked', true);
    }
    if (typesAction && $.inArray('1', typesAction) >= 0) {
        console.log('true');
        $('#base_up_sell_1').attr('checked', true);
    }
    if (typesAction && $.inArray('2', typesAction) >= 0) {
        $('#base_up_sell_2').attr('checked', true);
    }
    if (typesAction && $.inArray('4', typesAction) >= 0) {
        $('#base_cross_sell').attr('checked', true);
    }

    var types = $('#product_type').val();

    if (types && $.inArray('1', types) >= 0) {
        $('#up_sell_1').attr('checked', true);
    }
    if (types && $.inArray('2', types) >= 0) {
        $('#up_sell_2').attr('checked', true);
    }
    if (types && $.inArray('4', types) >= 0) {
        $('#cross_sell').attr('checked', true);
    }

    var typesExcept = $('#product_type_except').val();
    if ($.inArray('1', typesExcept) >= 0) {
        $('#up_sell_1_except').attr('checked', true);
    }
    if ($.inArray('2', typesExcept) >= 0) {
        $('#up_sell_2_except').attr('checked', true);
    }
    if ($.inArray('4', typesExcept) >= 0) {
        $('#cross_sell_except').attr('checked', true);
    }

    if ($('#operators_groups_json').val()) {
        $('.user-group-select2').select2('data', JSON.parse($('#operators_groups_json').val()));
    }
    if ($('#operators_json').val()) {
        $('.user-select2').select2('data', JSON.parse($('#operators_json').val()));
    }
    if ($('#operators_json').val()) {
        $('.user-select2').select2('data', JSON.parse($('#operators_json').val()));
    }
    if ($('#countries_json').val()) {
        $('.country-select2').select2('data', JSON.parse($('#countries_json').val()));
    }
    if ($('#offers_json').val()) {
        $('.offers-select2').select2('data', JSON.parse($('#offers_json').val()));
    }
    if ($('#products_json').val()) {
        $('.products-select2').select2('data', JSON.parse($('#products_json').val()));
    }

    if ($('#operators_groups_except_json').val()) {
        $('.user-group-select2-except').select2('data', JSON.parse($('#operators_groups_except_json').val()));
    }
    if ($('#operators_except_json').val()) {
        $('.user-select2-except').select2('data', JSON.parse($('#operators_except_json').val()));
    }
    if ($('#countries_except_json').val()) {
        $('.country-select2-except').select2('data', JSON.parse($('#countries_except_json').val()));
    }
    if ($('#offers_except_json').val()) {
        $('.offers-select2-except').select2('data', JSON.parse($('#offers_except_json').val()));
    }
    if ($('#products_except_json').val()) {
        $('.products-select2-except').select2('data', JSON.parse($('#products_except_json').val()));
    }

    if ($('#interval_hidden').val() == 'month') {
        $('#month').attr('checked', true);
    }
    if ($('#interval_hidden').val() == 'week') {
        $('#week').attr('checked', true);
    }
    if ($('#interval_hidden').val() == 'day') {
        $('#day').attr('checked', true);
    }

    /*block for basis criteria*/
    if ($('#basis_for_calculation_hidden').val() == 'sum-each') {
        $('#base_up_sell_1').attr('checked', false);
        $('#base_up_sell_2').attr('checked', false);
        $('#base_cross_sell').attr('checked', false);


        $('#sum-action').attr('disabled', false);
        $('#success-plan').attr('disabled', true);
        $('#product-type-action').attr("disabled", true);
        $('#action-sum-operator').attr("disabled", false);
        $('#action-sum-value').attr("disabled", false);
        $('#action-quantity-operator').attr("disabled", true);
        $('#action-quantity-value').attr("disabled", true);
        $('#sum-percent-product-type-action').attr("disabled", false);
        $('#base_up_sell_1').attr("disabled", true);
        $('#base_up_sell_2').attr("disabled", true);
        $('#base_cross_sell').attr("disabled", true);

        var compareOperator = $('#compare_operator').val();
        if (compareOperator == '>=') {
            $('#action-sum-operator option[value=">="]').attr('selected', true);
        }
        if (compareOperator == '>') {
            $('#action-sum-operator option[value=">"]').attr('selected', true);
        }
        /*block product_type_action*/
        var productTypeAction = $('#product_type_action').val();

        if (productTypeAction && productTypeAction == '["total"]') {
            $('#sum-percent-product-type-action option[value="total"]').attr('selected', true)
        }
        if (productTypeAction && productTypeAction == '["1"]') {
            $('#sum-percent-product-type-action option[value="1"]').attr('selected', true)
        }
        if (productTypeAction && productTypeAction == '["2"]') {
            $('#sum-percent-product-type-action option[value="2"]').attr('selected', true)
        }
        if (productTypeAction && productTypeAction == '["4"]') {
            $('#sum-percent-product-type-action option[value="4"]').attr('selected', true)
        }

        $('#sum-each').attr('checked', true);
        $('#percent-each').attr('checked', false);
        $('#percent').attr('checked', false);
        $('#quantity').attr('checked', false);


        $('#action-quantity-value').attr("disabled", true);
        $('#action-quantity-operator').attr("disabled", true);

        $('#action-sum-value').attr("disabled", false);
        $('#action-sum-operator').attr("disabled", false);
        $('#approve').attr("disabled", true).attr("checked", false);
        $('#base_up_sell_1').attr("disabled", true).attr("checked", false);
        $('#base_up_sell_2').attr("disabled", true).attr("checked", false);
        $('#base_cross_sell').attr("disabled", true).attr("checked", false);
        $('#sum-percent-product-type-action').attr("disabled", false);

    }
    if ($('#basis_for_calculation_hidden').val() == 'percent') {
        $('#base_up_sell_1').attr('checked', false);
        $('#base_up_sell_2').attr('checked', false);
        $('#base_cross_sell').attr('checked', false);

        var compareOperator = $('#compare_operator').val();
        if (compareOperator == '>=') {
            $('#action-sum-operator option[value=">="]').attr('selected', true);
        }
        if (compareOperator == '>') {
            $('#action-sum-operator option[value=">"]').attr('selected', true);
        }
        /*block product_type_action*/
        var productTypeAction = $('#product_type_action').val();
        if (productTypeAction && productTypeAction == '"total"') {
            $('#sum-percent-product-type-action option[value="total"]').attr('selected', true)
        }
        if (productTypeAction && productTypeAction == '"1"') {
            $('#sum-percent-product-type-action option[value="1"]').attr('selected', true)
        }
        if (productTypeAction && productTypeAction == '"2"') {
            $('#sum-percent-product-type-action option[value="2"]').attr('selected', true)
        }
        if (productTypeAction && productTypeAction == '"4"') {
            $('#sum-percent-product-type-action option[value="4"]').attr('selected', true)
        }

        $('#sum-action').attr('disabled', false);
        $('#success-plan').attr('disabled', true);
        $('#product-type-action').attr("disabled", true);
        $('#action-sum-operator').attr("disabled", false);
        $('#action-sum-value').attr("disabled", false);
        $('#action-quantity-operator').attr("disabled", true);
        $('#action-quantity-value').attr("disabled", true);
        $('#sum-percent-product-type-action').attr("disabled", true);
        $('#base_up_sell_1').attr("disabled", true);
        $('#base_up_sell_2').attr("disabled", true);
        $('#base_cross_sell_1').attr("disabled", true);
        $('#basis-sum').attr('checked', false);
        $('#basis-percent').attr('checked', true);

        $('#sum-each').attr('checked', false);
        $('#percent-each').attr('checked', false);
        $('#percent').attr('checked', true);
        $('#quantity').attr('checked', false);

        $('#action-quantity-value').attr("disabled", false);
        $('#action-quantity-operator').attr("disabled", false);

        $('#action-sum-value').attr("disabled", true);
        $('#action-sum-operator').attr("disabled", true);
        $('#base_up_sell_1').attr("disabled", false);
        $('#base_up_sell_2').attr("disabled", false);
        $('#base_cross_sell_1').attr("disabled", false);
    }
    if ($('#basis_for_calculation_hidden').val() == 'percent-each') {
        $('#base_up_sell_1').attr('checked', false);
        $('#base_up_sell_2').attr('checked', false);
        $('#base_cross_sell').attr('checked', false);

        var compareOperator = $('#compare_operator').val();
        if (compareOperator == '>=') {
            $('#action-sum-operator option[value=">="]').attr('selected', true);
        }
        if (compareOperator == '>') {
            $('#action-sum-operator option[value=">"]').attr('selected', true);
        }
        /*block product_type_action*/
        var productTypeAction = $('#product_type_action').val();
        if (productTypeAction && productTypeAction == '["total"]') {
            $('#sum-percent-product-type-action option[value="total"]').attr('selected', true)
        }
        if (productTypeAction && productTypeAction == '"1"') {
            $('#sum-percent-product-type-action option[value="1"]').attr('selected', true)
        }
        if (productTypeAction && productTypeAction == '"2"') {
            $('#sum-percent-product-type-action option[value="2"]').attr('selected', true)
        }
        if (productTypeAction && productTypeAction == '"4"') {
            $('#sum-percent-product-type-action option[value="4"]').attr('selected', true)
        }

        $('#sum-action').attr('disabled', false);
        $('#success-plan').attr('disabled', true);
        $('#product-type-action').attr("disabled", true);

        $('#sum-percent-product-type-action').attr("disabled", false);

        $('#sum-each').attr('checked', false);
        $('#percent-each').attr('checked', true);
        $('#percent').attr('checked', false);
        $('#quantity').attr('checked', false);

        $('#action-quantity-value').attr("disabled", true);
        $('#action-quantity-operator').attr("disabled", true);

        $('#action-sum-value').attr("disabled", false);
        $('#action-sum-operator').attr("disabled", false);
        $('#approve').attr("disabled", true).attr("checked", false);
        $('#base_up_sell_1').attr("disabled", true).attr("checked", false);
        $('#base_up_sell_2').attr("disabled", true).attr("checked", false);
        $('#base_cross_sell').attr("disabled", true).attr("checked", false);
        $('#sum-percent-product-type-action').attr("disabled", false);
    }
    if ($('#basis_for_calculation_hidden').val() == 'quantity') {

        var compareOperator = $('#compare_operator').val();
        if (compareOperator == '>=') {
            $('#action-sum-operator option[value=">="]').attr('selected', true);
        }
        if (compareOperator == '>') {
            $('#action-sum-operator option[value=">"]').attr('selected', true);
        }
        /*block product_type_action*/
        var productTypeAction = $('#product_type_action').val();
        if (productTypeAction && productTypeAction == '"total"') {
            $('#sum-percent-product-type-action option[value="total"]').attr('selected', true)
        }
        if (productTypeAction && productTypeAction == '"1"') {
            $('#sum-percent-product-type-action option[value="1"]').attr('selected', true)
        }
        if (productTypeAction && productTypeAction == '"2"') {
            $('#sum-percent-product-type-action option[value="2"]').attr('selected', true)
        }
        if (productTypeAction && productTypeAction == '"4"') {
            $('#sum-percent-product-type-action option[value="4"]').attr('selected', true)
        }
        $('#sum-action').attr('disabled', true);
        $('#success-plan').attr('disabled', false);
        $('#product-type-action').attr("disabled", false);
        $('#action-sum-value').attr("disabled", true);
        $('#action-quantity-operator').attr("disabled", false);
        $('#action-quantity-value').attr("disabled", false);
        $('#action-sum-operator').attr("disabled", true);
        $('#sum-percent-product-type-action').attr("disabled", true);

        $('#basis-for-schedule option[value=">="]').attr('selected', true);

        $('#sum-each').attr('checked', false);
        $('#percent-each').attr('checked', false);
        $('#percent').attr('checked', false);
        $('#quantity').attr('checked', true);

        $('#base_up_sell_1').attr("disabled", false);
        $('#base_up_sell_2').attr("disabled", false);
        $('#base_cross_sell').attr("disabled", false);
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

                /*разрешение на редактирование полей формы*/
                $('.user-group-select2').select2('enable');
                $('.user-select2').select2('enable');
                $('.country-select2').select2('enable');
                $('.offers-select2').select2('enable');
                $('.products-select2').select2('enable');
                $('#up_sell_1').attr("disabled", false);
                $('#up_sell_1_except').attr("disabled", false);
                $('#up_sell_2').attr("disabled", false);
                $('#up_sell_2_except').attr("disabled", false);
                $('#cross_sell').attr("disabled", false);
                $('#cross_sell_except').attr("disabled", false);
                $('#fixed-rate').attr("disabled", false);
                $('.user-group-select2-except').select2('enable');
                $('.user-select2-except').select2('enable');
            }
            if ($("#type-object").val() == 'operator') {
                $('.rate_block').empty();
                var inputs = $('#rate_block_' + window.companyType).clone().html();
                $('.type-payment').empty();
                $('.rate_block').append(inputs);

                $('#approve-bonus-def').val(billing.billing_approve);
                $('#up_sell-bonus-def').val(billing.billing_up_sell);
                $('#up_sell_2-bonus-def').val(billing.billing_up_sell_2);
                $('#cross_sell-bonus-def').val(billing.billing_cross_sell);

                $('#in_system-bonus').val(billing.billing_in_system);
                $('#in_talk-bonus').val(billing.billing_in_talk);

                /*разрешение на редактирование полей формы*/
                $('#failed-plan').attr("disabled", false);
                $('.user-group-select2').select2('enable');
                $('.user-select2').select2('enable');
                $('.country-select2').select2('enable');
                $('.offers-select2').select2('enable');
                $('.country-select2-except').select2('enable');
                $('.offers-select2-except').select2('enable');
                $('.products-select2-except').select2('enable');
                $('#up_sell_1-except').attr("disabled", false);
                $('#up_sell_2-except').attr("disabled", false);
                $('#cross_sell-except').attr("disabled", false);
                $('.products-select2').select2('enable');
                $('#up_sell_1').attr("disabled", false);
                $('#up_sell_2').attr("disabled", false);
                $('#cross_sell').attr("disabled", false);
                $('#fixed-rate').attr("disabled", false);
                $('.user-select2-except').select2('enable');
                $('.user-select2-except').select2('enable');
                $('.country-select2-except').select2('enable');
                $('.offers-select2-except').select2('enable');
                $('.products-select2-except').select2('enable');
            }
        }
    );
    $planId = $('#plan_id').val();
    $.getJSON('/plan-get-new-prices-ajax/' + $planId, function (data) {
        $('#in_system-bonus').val(data['in_system_bonus']);
        $('#in_talk-bonus').val(data['in_talk_bonus']);
        $('#in_system-retention').val(data['in_system_retention']);
        $('#in_talk-retention').val(data['in_talk_retention']);

        if (data['approve-bonus'] || data['up_sell-bonus'] || data['up_sell_2-bonus'] ||
            data['cross_sell-bonus']) {
            $('#approve-bonus').val(data['approve-bonus']);
            $('#up_sell-bonus').val(data['up_sell-bonus']);
            $('#up_sell_2-bonus').val(data['up_sell_2-bonus']);
            $('#cross_sell-bonus').val(data['cross_sell-bonus']);
            $('#rate-success').attr('checked', true);
        } else {
            $('#rate-success').attr('checked', false);
            $('#approve-bonus').attr('disabled', true);
            $('#up_sell-bonus').attr('disabled', true);
            $('#up_sell_2-bonus').attr('disabled', true);
            $('#cross_sell-bonus').attr('disabled', true);
        }

        if (data['approve-retention'] || data['up_sell-retention'] || data['up_sell_2-retention'] ||
            data['cross_sell-retention']) {
            $('#rate-failed').attr('checked', true);
        } else {
            $('#rate-failed').attr('checked', false);
            $('#approve-retention').attr('disabled', true);
            $('#up_sell-retention').attr('disabled', true);
            $('#up_sell_2-retention').attr('disabled', true);
            $('#cross_sell-retention').attr('disabled', true);
        }
        if (data['approve-retention'] || data['up_sell-retention'] || data['up_sell_2-retention'] ||
            data['cross_sell-retention']) {
            $('#approve-retention').val(data['approve-retention']);
            $('#up_sell-retention').val(data['up_sell-retention']);
            $('#up_sell_2-retention').val(data['up_sell_2-retention']);
            $('#cross_sell-retention').val(data['cross_sell-retention']);
            $('#rate-failed').attr('checked', true);
        } else {
            $('#rate-failed').attr('checked', false);
            $('#approve-retention').attr('disabled', true);
            $('#up_sell-retention').attr('disabled', true);
            $('#up_sell_2-retention').attr('disabled', true);
            $('#cross_sell-retention').attr('disabled', true);
        }
    })
});

function fixedRateSuccess() {
    if ($(this).prop('checked')) {
        $('#success-plan').attr('disabled', false);
    }
    else {
        $('#success-plan').attr('disabled', true);
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
    if ($(this).is(':checked') && $(this).val() == 'percent' || $(this).is(':checked') && $(this).val() == 'quantity') {

        $('#sum-each').attr("checked", false);
        $('#percent-each').attr("checked", false);

        $('#action-quantity-value').attr("disabled", false);
        $('#action-quantity-operator').attr("disabled", false);
        $('#sum-percent-product-type-action').attr("disabled", true);
        $('#action-sum-value').attr("disabled", true);
        $('#action-sum-operator').attr("disabled", true);
        $('#approve').attr("disabled", false);
        $('#base_up_sell_1').attr("disabled", false);
        $('#base_up_sell_2').attr("disabled", false);
        $('#base_cross_sell').attr("disabled", false);
    }
    if ($(this).is(':checked') && $(this).val() == 'sum-each' || $(this).is(':checked') && $(this).val() == 'percent-each') {
        $('#action-quantity-value').attr("disabled", true);
        $('#action-quantity-operator').attr("disabled", true);

        $('#action-sum-value').attr("disabled", false);
        $('#action-sum-operator').attr("disabled", false);
        $('#approve').attr("disabled", true).attr("checked", false);
        $('#base_up_sell_1').attr("disabled", true).attr("checked", false);
        $('#base_up_sell_2').attr("disabled", true).attr("checked", false);
        $('#base_cross_sell').attr("disabled", true).attr("checked", false);
        $('#sum-percent-product-type-action').attr("disabled", false);
    }
}

function getOptionForCalculation() {
    if ($(this).is(':checked') && $(this).val() == 'percent' || $(this).is(':checked') && $(this).val() == 'quantity') {
        $('#sum-each').attr("checked", false);
        $('#percent-each').attr("checked", false);

        $('#action-quantity-value').attr("disabled", false);
        $('#action-quantity-operator').attr("disabled", false);
        $('#sum-percent-product-type-action').attr("disabled", true);
        $('#action-sum-value').attr("disabled", true);
        $('#action-sum-operator').attr("disabled", true);
        $('#approve').attr("disabled", false);
        $('#base_up_sell_1').attr("disabled", false);
        $('#base_up_sell_2').attr("disabled", false);
        $('#base_cross_sell').attr("disabled", false);
    }
    if ($(this).is(':checked') && $(this).val() == 'sum-each' || $(this).is(':checked') && $(this).val() == 'percent-each') {
        $('#action-quantity-value').attr("disabled", true);
        $('#action-quantity-operator').attr("disabled", true);
        $('#quantity').attr('checked', false);
        $('#percent').attr('checked', false);

        $('#action-sum-value').attr("disabled", false);
        $('#action-sum-operator').attr("disabled", false);
        $('#approve').attr("disabled", true).attr("checked", false);
        $('#base_up_sell_1').attr("disabled", true).attr("checked", false);
        $('#base_up_sell_2').attr("disabled", true).attr("checked", false);
        $('#base_cross_sell').attr("disabled", true).attr("checked", false);
        $('#sum-percent-product-type-action').attr("disabled", false);
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
/*поиск по странам*/
$('.country-select2').select2({
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

// $('.user-select2').select2("data", [{id: 1, text: 'All ticket types'}]);
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
        url: '/user/find',
        dataType: 'json',
        data: function (params) {
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
