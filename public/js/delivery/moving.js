let Moving = {

    comment_form: '#moving_comment',
    $project_id: $('#project_id'),
    $help_block: $('#moving_comment .help-block'),
    $help_block_span: $('#moving_comment .help-block span'),
    $textarea: $('#moving_comment textarea[name="text"]'),
    $for_new_comment: $('.conversation-inner'),
    migmig: '.conversation-item .conversation-body',
    arrived_button: '#arrived_button',
    $message: $('#message'),
    $message_span: $('#message span'),
    $error: $('#error'),
    $error_span: $('#error span'),
    send_date: '#send_date',
    received_date: '#received_date',
    send_date_btn: '#send_date_btn',
    received_date_btn: '#received_date_btn',

    moving_id: '#moving_id',
    sender_id: '#sender_id',
    button: '#button',
    button_papa: '#button_papa',
    product_id: '#product_id',
    product_id_papa: '#product_id_papa',
    product_papa: '.product_papa',
    product_minus: '.product_minus',
    product_list: {},

    //$for_products: $('#for_products'),
    $for_errors: $('#for_errors'),
    $for_errors_span: $('#for_errors span'),
    $for_message: $('#for_message'),
    $for_message_span: $('#for_message span'),

    close_form: '#close_form',
    close_button: '#close_button',

    colors: ['#dceffc', '#32CD32'],

    init: function() {
        let self = this;
        self.bindEvents();

        if ($(self.product_id).length)
            self.productSelect2();
    },
    bindEvents: function () {
        let self = this;
        $(document)
            .on('submit', self.comment_form, function(e) {
                self.submitComment(e);
            })
            .on('click', self.arrived_button, function(e) {
                self.arrivedSubmit(e);
            })
            .on('click', self.send_date_btn + ', ' + self.received_date_btn, function(e) {
                self.dateChange(e, $(this));
            })
            .on('change', self.product_id, function() {
                self.plusProduct();
            })
            .on('click', self.product_minus, function() {
                self.minusProduct($(this).data('id'), $(this).data('url'));
            })
            .on('click', self.button, function() {
                if($(this).attr('data-type') == 'arrived') {
                    self.receiveMoving();
                } else {
                    self.moveMoving();
                }
            })
            .on('submit', self.close_form, function() {
                $(self.close_button).attribute('disabled', 'true');
            })
    },
    submitComment: function(e) {
        let self = this;
        e.preventDefault();

        $.ajax({
            url: $(self.comment_form).attr('data-url'),
            type: 'post',
            dataType: 'json',
            data: $(self.comment_form).serialize(),
            beforeSend: function() {
                self.$help_block.hide();
                self.$help_block_span.empty();
            },
            success: function (data) {
                if (typeof data.errors !== 'undefined') {
                    self.$help_block.show();
                    self.$help_block_span.text(data.errors.text[0]);
                } else if (typeof data.comment_html !== 'undefined') {

                    self.$textarea.val('');
                    self.$for_new_comment.prepend(data.comment_html);

                    let block = self.$for_new_comment.find(self.migmig).first();
                    block.animate({backgroundColor : self.colors[1]}, 1000);
                    setTimeout(function() {
                        block.animate({backgroundColor : self.colors[0]}, 1000);
                    }, 1000);
                } else {
                    self.$help_block.show();
                    self.$help_block_span.text('code: moving01');
                }
            },
            error(data) {
                self.$help_block.show();
                self.$help_block_span.text((typeof data.statusText === 'undefined')
                    ? 'code: moving02'
                    : data.statusText);
            }
        });
        return false;
    },
    arrivedSubmit: function(e) {
        let self = this;
        e.preventDefault();

        $.ajax({
            url: $(self.arrived_button).attr('data-url'),
            type: 'post',
            beforeSend: function() {
                $(self.arrived_button).attr('disabled',true);
                self.messageAndErrorHide();
            },
            success: function (data) {
                if (typeof data.btn_hide === 'undefined') {
                    $(self.arrived_button).removeAttr('disabled');
                    $(self.arrived_button).show();
                } else {
                    $(self.arrived_button).hide();
                }
                if (typeof data.errors !== 'undefined') {
                    self.errorShow(data.errors);
                } else if (typeof data.message !== 'undefined') {
                    self.messageShow(data.message);
                    $(self.received_date).text(data.date);
                } else {
                    self.errorShow([['code: moving03']]);
                }
            },
            error(data) {
                self.errorShow([[
                    (typeof data.statusText === 'undefined')
                        ? 'code: moving04'
                        : data.statusText
                ]]);
            }
        });

        return false;
    },
    dateChange: function(e, obj) {
        let self = this;
        e.preventDefault();

        let type = (('#' + obj.attr('id')) == self.send_date_btn) ? 'send' : 'received';
        let date = $((('#' + obj.attr('id')) == self.send_date_btn) ? self.send_date : self.received_date).val();
        let link = obj.attr('data-url');

        $.ajax({
            url: link,
            type: 'post',
            data: {type: type, date: date},
            beforeSend: function() {
                self.messageAndErrorHide();
            },
            success: function (data) {
                if (typeof data.btn_hide === 'undefined') {
                    $(self.arrived_button).removeAttr('disabled');
                    $(self.arrived_button).show();
                } else {
                    $(self.arrived_button).hide();
                }
                if (typeof data.errors !== 'undefined') {
                    self.errorShow(data.errors);
                } else if (typeof data.message !== 'undefined') {
                    self.messageShow(data.message);
                    $(self.received_date).text(data.date);
                } else {
                    self.errorShow([['code: moving05']]);
                }
            },
            error(data) {
                self.errorShow([[
                    (typeof data.statusText === 'undefined')
                        ? 'code: moving06'
                        : data.statusText
                ]]);
            }
        });
        return false;
    },
    productSelect2: function () {
        let self = this;
        let project_id = self.$project_id.val();
        let sender_id = $(self.sender_id).val();

        $(self.product_id).select2({
            placeholder: $(self.product_id).attr('placeholder'),
            minimumInputLength: 2,
            multiple: false,
            ajax: {
                url: $(self.product_id).attr('data-url_p'),
                type: 'POST',
                dataType: 'json',
                data: function (word) {
                    return {
                        project_id: project_id,
                        sender_id: sender_id,
                        products: self.setProductList(),
                        word: $.trim(word)
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
    },
    plusProduct: function() {
        let self = this;
        $.ajax({
            url: $(self.product_id).attr('data-url'),
            type: 'post',
            dataType: 'json',
            data: {
                product_id: $(self.product_id).val(),
                sender_id: $(self.sender_id).val(),
            },
            beforeSend: function() {},
            success: function (data) {
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
    setCoolProductList: function() {
        let self = this;
        let products = {};
        self.product_list = [];
        if ($(self.product_papa).length) {
            $(self.product_papa).each(function(){
                self.product_list.push({
                    id: $(this).find('[name="product_list_id"]').val(),
                    arrived: $(this).find('[name="product_list_arrived"]').val(),
                    shortfall: $(this).find('[name="product_list_shortfall"]').val()
                });
            })
        }
        return self.product_list;
    },
    showHideButton: function() {
        let self = this;
        $(self.product_papa).length ? $(self.button).show() : $(self.button).hide();
    },
    moveMoving: function() {
        let self = this;
        $.ajax({
            url: $(self.button).attr('data-url'),
            type: 'post',
            dataType: 'json',
            data: {
                moving_id: $(self.moving_id).val(),
                products: self.setProductList()
            },
            beforeSend: function() {
                self.messageAndErrorBottomHide();
                $(self.button).attr('disabled',true);
            },
            success: function (data) {
                if (typeof data.errors !== 'undefined') {
                    self.bottomErrorsShow(data.errors);
                    $(self.button).removeAttr('disabled');
                } else {
                    window.location.reload();
                }
            },
            error:function(data) {
                self.bottomErrorsShow([[
                    (typeof data.statusText === 'undefined')
                        ? 'code: moving07'
                        : data.statusText
                ]]);
            }
        });
    },
    receiveMoving: function() {
        let self = this;
        $.ajax({
            url: $(self.button).attr('data-url'),
            type: 'post',
            dataType: 'json',
            data: {
                moving_id: $(self.moving_id).val(),
                products: self.setCoolProductList()
            },
            beforeSend: function() {
                self.messageAndErrorBottomHide();
                $(self.button).attr('disabled',true);
            },
            success: function (data) {
                $(self.button).removeAttr('disabled');
                if (typeof data.errors !== 'undefined') {
                    self.bottomErrorsShow(data.errors);
                } else if (typeof data.reload !== 'undefined') {
                    window.location.reload();
                } else {
                    self.bottomMessageShow(data.message);
                }
            },
            error:function(data) {
                self.bottomErrorsShow([[
                    (typeof data.statusText === 'undefined')
                        ? 'code: moving08'
                        : data.statusText
                ]]);
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
    errorShow: function(errors) {
        let self = this;
        self.$error.show();
        for (let i in errors) {
            self.$error_span.append(errors[i][0] + '<br />');
        }
    },
    messageShow: function(message) {
        let self = this;
        self.$message.show();
        self.$message_span.text(message);
    },
    bottomErrorsShow: function(errors) {
        let self = this;
        self.$for_errors.show();
        for (let i in errors) {
            self.$for_errors_span.append(errors[i]);
        }
    },
    bottomMessageShow: function(message) {
        let self = this;
        self.$for_message.show();
        self.$for_message_span.append(message);
    },
    messageAndErrorBottomHide: function() {
        let self = this;
        self.$for_errors.hide();
        self.$for_message.hide();
        self.$for_errors_span.empty();
        self.$for_message_span.empty();
    },
};

$(function () {
    $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});

    $('.datetimepicker').datetimepicker({
        'format' : 'Y-m-d H:i:s'
    });

    Moving.init();
});