let TransactionCreate = {

    project_id: '#project_id',
    storage_id: '#storage_id',
    product_id: '#product_id',
    form: '#transaction_form',
    button: '#button',
    current_amount: '#current_amount',
    current_hold: '#current_hold',
    amount_plus: '#amount_plus',
    amount_minus: '#amount_minus',
    hold_plus: '#hold_plus',
    hold_minus: '#hold_minus',

    $for_storage: $('#for_storage'),
    $for_product: $('#for_product'),

    $message: $('#message'),
    $message_span: $('#message span'),
    $error: $('#error'),
    $error_span: $('#error span'),

    init: function() {
        let self = this;
        if ($(self.project_id).is('select')) {
            $(self.project_id).select2();
        }
        if ($(self.storage_id).length > 0) {
            self.getStorages();
        }
        self.bindEvents();
    },
    bindEvents: function () {
        let self = this;
        $(document)
            .on('change', self.project_id, function() {
                self.getStorages();
            })
            .on('change', self.storage_id, function() {
                self.getProducts();
            })
            .on('submit', self.form, function(e) {
                self.submit(e);
            })
            .on('change', self.product_id, function() {
                self.productChange();
            });
    },
    getStorages: function() {
        let self = this;
        let project_id = $(self.project_id).val();
        if (project_id === '') {
            self.$for_storage.empty();
            self.$for_product.empty();
        } else {
            $.ajax({
                url: $(self.project_id).attr('data-url'),
                type: 'post',
                dataType: 'json',
                data: {project_id: project_id},
                beforeSend: function() {
                    self.$for_storage.empty();
                    self.$for_product.empty();
                },
                success: function (data) {
                    self.$for_storage.append(data.storages_html);
                    $(self.storage_id).select2();
                }
            });
        }
    },
    getProducts: function() {
        let self = this;
        let project_id = $(self.project_id).val();
        let storage_id = $(self.storage_id).val();

        self.$for_product.empty();

        if ((project_id !== '') && (storage_id !== '')) {
            $.ajax({
                url: $(self.storage_id).attr('data-url'),
                type: 'post',
                dataType: 'json',
                data: {
                    project_id: project_id,
                    storage_id: storage_id
                },
                success: function (data) {
                    self.$for_product.append(data.product_html);
                    self.productSelect2();
                }
            });
        }
    },
    productSelect2: function () {
        let self = this;
        let project_id = $(self.project_id).val();
        let storage_id = $(self.storage_id).val();

        $(self.product_id).select2({
            placeholder: $(self.product_id).attr('placeholder'),
            minimumInputLength: 2,
            multiple: false,
            ajax: {
                url: $(self.product_id).attr('data-url'),
                type: 'POST',
                dataType: 'json',
                data: function (word) {
                    return {
                        project_id: project_id,
                        storage_id: storage_id,
                        word: $.trim(word)
                    };
                },
                results: function (data) {
                    return {
                        results: data,
                        "pagination": {"more": true}
                    };
                }
            }
        });
    },
    productChange: function () {
        let self = this;
        let storage_id = $(self.storage_id).val();
        let product_id = $(self.product_id).val();
        if ((storage_id != '') && (product_id != '')) {
            $.ajax({
                url: $(self.product_id).attr('data-url_2'),
                type: 'post',
                dataType: 'json',
                data: {
                    storage_id: storage_id,
                    product_id: product_id,
                },
                success: function (data) {
                    $(self.current_amount).text(data.amount);
                    $(self.current_hold).text(data.hold);
                }
            });
        }
    },
    submit: function(e) {
        console.log(111);
        e.preventDefault();
        let self = this;
        console.log(self.button);
        $.ajax({
            url: $(self.form).attr('action'),
            type: 'post',
            dataType: 'json',
            data: $(self.form).serialize(),
            beforeSend: function() {
                self.messageAndErrorHide();
                $(self.button).attr('disabled', 'true');
            },
            success: function (data) {
                if (typeof data.error !== 'undefined') {
                    $(self.button).removeAttr('disabled');
                    self.errorShow(data.error)
                } else {
                    self.messageShow(data.message);
                    setInterval(function() {window.location.href = data.link;}, 1500);
                }
            },
            error: function() {
                self.errorShow('какая-то беда')
                $(self.button).removeAttr('disabled');
            }
        });
    },
    messageAndErrorHide: function() {
        let self = this;
        self.$message.hide();
        self.$error.hide();
        self.$message_span.empty();
        self.$error_span.empty();
    },
    errorShow: function(error) {
        let self = this;
        self.$error.show();
        self.$error_span.text(error);
    },
    messageShow: function(message) {
        let self = this;
        self.$message.show();
        self.$message_span.text(message);
    }
};

$(function () {
    $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
    TransactionCreate.init();
});