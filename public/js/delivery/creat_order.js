$(document).ready(function() {

    let project_id = $('#project_id').val();
    let sub_project = '';
    let select_str = "";

    // зачистить поля компании и sub
    // $( ".block_projects select" ).each(function() {
    //     if ($(this).val() !== '') {
    //         $(this).val("").change();
    //     }
    //
    // });

    // $('.block_projects option[value=""]').attr("selected", "selected");
//     -----------------------------------------------------------------------------
//     -----------------------------------------------------------------------------
// видоизменить select -------------------------------------------------------------

    if ($('#project_id').is('select')){ $('#project_id').select2(); }
    insertSub(); //  изменение sub-проектов

//     -----------------------------------------------------------------------------
//     -----------------------------------------------------------------------------
// select проекта и sub-проекта -------------------------------------------------------------
    $(".block_projects select").change(function (eve) {
        $target_id = eve.target.id
        project_id = $('#project_id').val();
        sub_project = $('#sub_id').val();

        if($target_id == "project_id"){
            insertSub(); //  изменение sub-проектов
            $('.block_insert').text('');
            $('#product_id_papa, #block_order').css({'display':'none'});
        }
        if($target_id == "sub_id"){
            if (sub_project != '') {
                $('#product_id_papa, #block_order').css({'display':'block'});
            }
            else{
                $('.block_insert').text('');
                $('#product_id_papa, #block_order').css({'display':'none'});
            }

        }

    alertError(0);
    });

//     -----------------------------------------------------------------------------
//     -----------------------------------------------------------------------------
// искать товар --------------------------------------------------------------------
    $('#product_id').select2({
        placeholder: '--Выбрать товар--',
        minimumInputLength: 2,
        multiple: false,
        ajax: {

            url: $('#product_id').attr('data-url'), // http://crm.lara/all_orders/get-products-list
            type: 'POST',
            dataType: 'json',
            data: function (word) {
                return {
                    // products: setProductList(),
                    project_id: project_id,   // "11"​ с какого проекта
                    sub_project: sub_project, // "15"​ с какого склада
                    word: $.trim(word) // "слово" что ищем
                };
            },
            results: function (data) { // Object { id: 2001, text: "Xiaomi Mi Power Bank 20800 (Колличество:-493)" }
                return {
                    results: data,
                    "pagination": {
                        "more": true
                    }
                };

            }
        }
    });

//     -----------------------------------------------------------------------------
//     -----------------------------------------------------------------------------
// добавить товар - клик по найденному товару в поиске -----------------------------

    $('#product_id').change(function () {

        console.log('tyt');
        $.ajax({
            url: $(this).attr('data-url2'), // http://crm.lara/all_orders/plus-product
            type: 'post',
            dataType: 'json',
            data: {
                project_id: project_id,
                product_id: $(this).val(),
                sender_id: sub_project,
                count_product: $('.product_papa').length,
            },
            success: function (data) {
                // console.log(data);
                // $(self.product_id_papa).remove();
                $('.block_insert').prepend(data.new_product_html);
                $('#product_papa_'+data.kol_vo+' .numbo_product').text(data.kol_vo);
                $('#color_'+data.kol_vo).select2();
                $('#product_id').val('--Выбрать товар--');
                $('#product_id_papa .select2-chosen').text('--Выбрать товар--');
            }
        });
    });


//     -----------------------------------------------------------------------------
//     -----------------------------------------------------------------------------
// клики по области выбраных товаров -----------------------------

    $('.block_insert').click(function (eve) {
        $target = $(eve.target);
        $class = eve.target.className;
        $one_color = $(eve.target).parent().parent();
        // выбрать цвет у товара -----------------------------
        if($class == 'fa fa-plus'){
            $elem_color = $target.attr('data-color');
            $target.parent().remove();
            $($one_color).append('<input class="form-control numbo_inut" value="0" type="number" data-color="'+$elem_color+'">');
        }
        if($class == 'add_color btn btn-primary'){
            $one_color = $target.parent();
            $elem_color = $target.attr('data-color');
            $target.remove();
            $($one_color).append('<input class="form-control numbo_inut" value="0" type="number" data-color="'+$elem_color+'">');
        }
        // удалить товар из списка -----------------------------
        if($class == 'btn btn-primary product_minus'){
            if(confirm("Удалить выбраный товар ?")){
                $("#product_papa_"+$target.attr('data-id')).remove();
            }
        }
    });


//     -----------------------------------------------------------------------------
//     -----------------------------------------------------------------------------
// кнопка создания заказа -----------------------------
    $('#but_order').click(function (eve) {

        $Data = makeArray();
        // если сформирован масив с проуктами
        if(typeof($Data) == 'object'){

            $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});


            $.ajax({
                url: $(this).attr('data-url'), // http://crm.lara/all_orders/add-order
                type: 'post',
                dataType: 'json',
                data: {
                    project_id: project_id,
                    sub_project: sub_project,
                    products: $Data,
                },
                beforeSend: function() {},
                success: function (data) {
                    window.location.href = '../all_orders';
                    // window.location.href = $(this).attr('data-url2');
                }
            });




            console.log($Data);

        }



    });

//     -----------------------------------------------------------------------------
//     -----------------------------------------------------------------------------
// оображение предупреждения -----------------------------
function alertError($status, $str = null){
    $arr_status = ['none', 'block'];
    $('#alert_error').css({'display':$arr_status[$status]}).text($str);
}

//     -----------------------------------------------------------------------------
//     -----------------------------------------------------------------------------
// формирую масив продуктов для отправки -----------------------------
    function makeArray(){
        $Data = [];
        $Color = [];
        $exit = 0;
        $exit2 = 0;

        // перебор выбраных продуктов
        $('.block_insert .product_papa').each(function() {
            $prod_count = $(this).attr('data-count'); // номер добавленого продукта на стр - 1
            $bp_id = '#'+$(this).attr('id'); // строка id блока добавленого продукта на стр - #product_papa_1
            $all_count = parseInt($($bp_id+' #count_product_'+$prod_count).val()); // общее кол-во выбранного продукта

 // Exit
            if(!$all_count){ alertError(1, 'Не выбрано общее кол-во товара!'); return false; }

 // 1 заполнение выбраных опций
            $($bp_id+' .one_color').each(function() {
                $input = $(this).children('input');

                if($input.length){
                    if(!parseInt($input.val())){
                        $exit = 1;
                        return false;
                    }
                    $Color.push({
                        'color': $input.attr('data-color'),
                        'count': $input.val()
                    });
                    $exit2 = 1;
                }
            });

 // Exit
            if($exit){ alertError(1, 'Не выбрано кол-во товара в цвете!'); return false; }

 //Exit
            if(!$exit2){ alertError(1, 'У товара не выбрана ни одна опция!'); return false; }

 // 2
            $Data.push({
                'product_id': $(this).attr('data-product'),
                'all_count': $($bp_id+' #count_product_'+$prod_count).val(),
                'option': $Color,
                'description': $($bp_id+' #comment_'+$prod_count).val()
            });

        $Color = [];
        }); // each

 //Exit
        if($exit || !$exit2){ return false; }

        return $Data;
    }

//     -----------------------------------------------------------------------------
//     -----------------------------------------------------------------------------
// изменение sub-проектов --------------------------------------------------------------------

    function insertSub() {

        select_str = '<option selected="selected" value="">--Select--</option>';
        for (var prop in Sub) {
            if (project_id == Sub[prop]['parent_id']) {
                select_str += '<option value="'+Sub[prop]['id']+'">'+Sub[prop]['name']+'</option>';
            }
        }

        $("#sub_id").html(select_str);
        $('#sub_id').select2();
    }



    // function setProductList() {
    //     let self = this;
    //     let products = {};
    //     let product_list = {};
    //
    //         $('#block_order .block_product').each(function(){
    //             let id = $(this).find('[name="product_list_id"]').val();
    //             let amount = $(this).find('[name="product_list_amount"]').val();
    //             product_list[id] = amount;
    //         })
    //
    //     return self.product_list;
    // }

















}); // конец файла