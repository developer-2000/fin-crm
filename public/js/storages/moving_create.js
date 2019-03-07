let MovingCreate = {

    $project_id: $('#project_id'),
    project_id: '#project_id',

    storage: '.storage',

    $for_sub: $('#for_sub'),
    for_sub_id: '#for_sub',
    sel_sub:'0',

    $for_sender: $('#for_sender'),
    sender_id: '#for_sender',
    my_storage_id:'0',

    $for_receiver: $('#for_receiver'),
    receiver_id: '#receiver_id',

    button: '#button',
    button_papa: '#button_papa',

    product_id: '#product_id',
    product_id_papa: '#product_id_papa',
    product_papa: '.product_papa',

    product_minus: '.product_minus',

    product_list: {},

    $for_products: $('#for_products'),
    $for_errors: $('#for_errors'),
    $for_errors_span: $('#for_errors span'),

    init: function() {
        let self = this;
        if (self.$project_id.is('select')) {
            self.$project_id.select2();
        }
        self.fonLoad(1);
        self.getStorages();
        self.bindEvents();
        self.$for_errors.hide();
        // если выбрал в select - select фоновая исчезнит
        if ($('#project_id').val() === ''){ self.fonLoad(0); }

    },

// события
    bindEvents: function () {
        let self = this;
        $(document)
            // изменения в первом select компании
            .on('change', self.project_id, function() {
                self.getStorages(); // подгрузка подпроектов
            })
            // изменения в 2 select подпроектов
            .on('change', self.for_sub_id, function() {
                self.getStorages2(); // подгрузка моих складов
            })
            // изменения в 3 select в моих складах
            .on('change', self.sender_id, function() {
                self.getStorages3(); // подгрузка на какие склады перемещение
            })
            // изменения в 4
            .on('change', self.receiver_id, function() {
            self.fonLoad(1);
            })
        // изменения в любых select, подгружаю список склады
            .on('change', self.storage, function(e) {

                self.$for_errors.hide();
                let for_sub_id = $(self.for_sub_id).val();
                let sender_id = $(self.sender_id).val();
                let receiver_id = $(self.receiver_id).val();
// console.log(e.target.id);

                self.infoSelect();
                // self.fonLoad(1);
                    self.$for_products.empty();
                    // выборка ajax отображая select поиска товара
                    self.getProducts();
                    self.showHideButton();
            })
        // выбрал из поиска товар
            .on('change', self.product_id, function() {
                self.plusProduct();
            })
        // клик по ведру удаления товара из перемещения
            .on('click', self.product_minus, function() {
                self.minusProduct($(this).data('id'), $(this).data('url'));
            })
        // клик по кнопке отправки перемещения товара
            .on('click', self.button, function() {
                self.createMoving();
            });
    },

// вызывается при изменении select проекта
// подгрузка и показ подпроетов select
    getStorages: function() {
        let self = this;

        if ($(self.project_id).val() !== '') { self.fonLoad(1); }
        else{ self.fonLoad(0); }

        if (!$(self.project_id).val()) {
            self.$for_products.empty();
            self.$for_sub.empty();
            self.$for_sender.empty();
            self.$for_receiver.empty();
        }
        else {
            $.ajax({
                url: self.$project_id.attr('data-url'), // http://crm.lara/storages/movings/get-storages
                type: 'post',
                dataType: 'json',
                data: {project_id:self.$project_id.val()}, // передача id выбранного проекта
                // вызывается перед отправкой запроса ajax
                beforeSend: function() {
                    // Очищает содержание выбранных элементов
                    self.$for_products.empty();
                    self.$for_sub.empty();
                    self.$for_sender.empty();
                    self.$for_receiver.empty();
                },
                success: function (data) {
                    // вставкаа подпроекты
                    self.$for_sub.append(data.subproj_html);

                    // видоизменить select
                    if ($('#subproj').is('select')){ $('#subproj').select2(); }

                    self.infoSelect();
// в том случае если у юзера есть subproject и сразу подгружается вместо select - input
                    if (self.sel_sub != ''){
                        self.getStorages2();
                    }

                    // отобразит поисковый input
                    self.getProducts();
                    // отобразит кнопку отправки перемещения
                    self.showHideButton();

                    self.fonLoad(0);
                }
            });
        }

    },

// вызывается при изменении select подпроекта
// подгрузка и показ моих складов select
    getStorages2: function() {
        let self = this;

        if ($('#subproj').val() !== '') { self.fonLoad(1); }
        else{ self.fonLoad(0); }

            $.ajax({
                url: self.$for_sub.attr('data-url'), // http://crm.lara/storages/movings/get-storages
                type: 'post',
                dataType: 'json',
                data: {id_sub:self.sel_sub }, // передача id выбранного подпроекта
                // вызывается перед отправкой запроса ajax
                beforeSend: function() {
                    // Очищает содержание выбранных элементов
                    self.$for_products.empty();
                    // self.$for_sub.empty();
                    self.$for_sender.empty();
                    self.$for_receiver.empty();
                    // отменить загрузку ajax если в подпроектах выбран --Select--
                    if (self.sel_sub == ''){ return false; }
                },
                success: function (data) {
                    // console.log(data);
                    // вставкаа 3 select
                    // self.$for_sub.append(data.subproj_html);
                    self.$for_sender.append(data.my_storage_html);
                    // self.$for_receiver.append(data.all_storages_html);

                    // видоизменить select
                    // if ($('#subproj').is('select')){ $('#subproj').select2(); }
                    if ($('#my_storage').is('select')){ $('#my_storage').select2(); }
                    // if ($(self.receiver_id).is('select')){ $(self.receiver_id).select2(); }

                    // отобразит поисковый input
                    self.getProducts();
                    // отобразит кнопку отправки перемещения
                    self.showHideButton();

                    self.fonLoad(0);
                }
            });


    },

    // вызывается при изменении select подпроекта
// подгрузка и показ на какие склады
    getStorages3: function() {
        let self = this;

        if ($('#my_storage').val() !== '') { self.fonLoad(1); }
        else{ self.fonLoad(0); }

            $.ajax({
                url: self.$for_sender.attr('data-url'), // http://crm.lara/storages/movings/get-storages
                type: 'post',
                dataType: 'json',
                data: {project_id:self.$project_id.val(), // id проекта
                       id_sub:self.sel_sub,               // id подпроекта
                       my_storage_id:self.my_storage_id   // id моего склада
                },
                // вызывается перед отправкой запроса ajax
                beforeSend: function() {
                    // Очищает содержание выбранных элементов
                    self.$for_products.empty();
                    // self.$for_sub.empty();
                    // self.$for_sender.empty();
                    self.$for_receiver.empty();
                    // отменить загрузку ajax если в подпроектах выбран --Select--
                    if (self.sel_sub == '' || self.my_storage_id == ''){
                        self.fonLoad(0);
                        return false;
                    }
                },
                success: function (data) {

                    self.$for_receiver.append(data.all_storages_html);
                    self.$for_receiver.append('<div class="rel"></div>');

                    // видоизменить select
                    if ($(self.receiver_id).is('select')){ $(self.receiver_id).select2(); }

                    // отобразит поисковый input
                    self.getProducts();
                    // отобразит кнопку отправки перемещения
                    self.showHideButton();
                    self.fonLoad(0);
                }
            });


    },

// выборка ajax отображая select поиска товара
    getProducts: function() {
        let self = this;
        let project_id = self.$project_id.val();
        let for_sub_id = $(self.for_sub_id).val();
        let sender_id = $(self.sender_id).val();

// если выбрал в select - select фоновая исчезнит
        $('#block_sel select').each(function() {
            if ($(this).val() === ''){ self.fonLoad(0); }
        });


        self.$for_products.empty();
        // отобразит поисковый input только если в 3 select выбраны option
        if (
            ($('#subproj').length && $('#subproj').val().length) &&
            ($('#my_storage').length && $('#my_storage').val().length) &&
            ($(self.receiver_id).length && $(self.receiver_id).val().length) ) {

            $.ajax({
                // http://crm.lara/storages/movings/get-products
                url: $(self.receiver_id).attr('data-url'),
                type: 'post',
                dataType: 'json',
                // данные не задействованы в blade
                data: {
                    // project_id: project_id,
                    // sender_id: sender_id
                },
                beforeSend: function() {
                    self.$for_products.empty();

                },
                success: function (data) {
                    self.$for_products.append(data.product_html);
                    self.$for_products.append(data.button_html);
                    $('#for_products').show();
                    self.productSelect2();
                    self.showHideButton();
                    self.fonLoad(0);
                }
            });
        }
        else{
            $('#for_products').hide();

        }


    },

// ищет и отображает искомы продукты
    productSelect2: function () {
        let self = this;
        let project_id = $('#project_id').val();
        let my_sub_project = $('#subproj').val();
        let my_storage = $('#my_storage').val();
        let sender_id = $(self.receiver_id).val();
        // let sender_id = $(self.sender_id).val();

        $(self.product_id).select2({
            placeholder: $(self.product_id).attr('placeholder'),
            minimumInputLength: 2,
            multiple: false,
            ajax: {
                // http://crm.lara/storages/movings/get-products-list
                url: $(self.product_id).attr('data-url_p'),
                type: 'POST',
                dataType: 'json',
                data: function (word) {
                    return {
                        project_id: project_id, // "11"​ с какого проекта
                        my_sub_project: my_sub_project, // "15"​ с какого склада
                        my_storage: my_storage, // "15"​ с какого склада
                        sender_id: sender_id, // "4"​ на какой склад
                        products: self.setProductList(), // Object { 2001: "3", 3185: "123" } перечень до этого выбраных товаров (id и кол-во перемещаемых)
                        word: $.trim(word) // "слово" что ищем
                    };
                },
                results: function (data) { // Object { id: 2001, text: "Xiaomi Mi Power Bank 20800 (Колличество:-493)" }
                    // console.log(data);
                    return {
                        results: data,
                        "pagination": {
                            "more": true
                        }
                    };

                }
            }
        });
    },

// добавить товар - клик по найденному товару в поиске
    plusProduct: function() {
        let self = this;
        let project_id = $('#project_id').val();
        console.log($(self.product_id).attr('data-url'));
        $.ajax({
            url: $(self.product_id).attr('data-url'), // http://crm.lara/storages/movings/plus-product
            type: 'post',
            dataType: 'json',
            data: {
                project_id: project_id,
                product_id: $(self.product_id).val(),
                sender_id: self.my_storage_id,
            },
            beforeSend: function() {},
            success: function (data) {
                // console.log(data);
                $(self.product_id_papa).remove();
                $(self.button_papa).before(data.new_product_html);
                $(self.button_papa).before(data.product_html);
                self.productSelect2();
                self.showHideButton();
            }
        });
    },

    minusProduct: function(product_id, url) {
        let self = this;
        $.ajax({
            url: url,
            type: 'post',
            dataType: 'json',
            data: {
                product_id: product_id,
                sender_id: $(self.sender_id).val(),
            },
            beforeSend: function() {},
            success: function (data) {
                $(self.product_id_papa).remove();
                $(self.product_papa + '[data-id="' + product_id + '"]').remove();
                $(self.button_papa).before(data.product_html);
                self.productSelect2();
                self.showHideButton();
            }
        });
    },

    setProductList: function() {
        let self = this;
        let products = {};
        self.product_list = {};
        if ($(self.product_papa).length) {
            $(self.product_papa).each(function(){
                let id = $(this).find('[name="product_list_id"]').val();
                let amount = $(this).find('[name="product_list_amount"]').val();
                self.product_list[id] = amount;
            })
        }
        return self.product_list;
    },

// отображение кнопки отправки перемещения товара
    showHideButton: function() {
        let self = this;


        if ($(self.sender_id).val() == $(self.receiver_id).val()) {
            $(self.button).hide();
        } else {
            $(self.product_papa).length ? $(self.button).show() : $(self.button).hide();
        }
    },

// отправляет перемещение
    createMoving: function() {
        let self = this;

        $.ajax({
            url: $(self.button).attr('data-url'), // http://crm.lara/storages/movings/store
            type: 'post',
            dataType: 'json',
            data: {
                sender_id: $('#my_storage').val(),
                receiver_id: $(self.receiver_id).val(),
                products: self.setProductList()
            },
            beforeSend: function() {
                self.$for_errors.hide();
                self.$for_errors_span.empty();
                $(self.button).attr('disabled',true);
            },
            success: function (data) {
                // console.log(data);
                //показать сообщение error
                if (typeof data.errors !== 'undefined') {
                    console.log('error creat moving');
                    self.$for_errors.show();
                    for (let i in data.errors) {
                        self.$for_errors_span.append(data.errors[i]);
                    }
                    $(self.button).removeAttr('disabled');
                }
                else if (typeof data.link !== 'undefined') {
                    console.log('link creat moving');
                    window.location.href = data.link; // "http://crm.lara/storages/movings/283"
                }
            }
        });
    },

// снять инфу с select
    infoSelect: function() {
        let self = this;

        $('#block_sel select').each(function() {
            if ($(this).attr('id') == 'subproj') {
                self.sel_sub = $(this).val();
            }
            if ($(this).attr('id') == 'my_storage') {
                self.my_storage_id = $(this).val();
            }
        });
        // в том случае если у юзера есть subproject и подгружается вместо select - input
        $('#block_sel input').each(function() {
            if ($(this).attr('id') == 'subproj') {
                self.sel_sub = $(this).val();
            }
        });


        //
    },

// closing and open select fon
    fonLoad: function($value) {


        $value ?
            $('#s2id_project_id').append('<div class="above"></div>') :
            $('#s2id_project_id').children('div.above').remove();


        $('#block_sel > div').each(function() {
            if ($value){
                $(this).children('div').append('<div class="above"></div>');
            }
            else{
                $(this).children('div').children('div.above').remove();
            }
        });



    }



};

$(function () {
    $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
    MovingCreate.init();
});