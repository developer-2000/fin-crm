let Remainders = {

    date: '#date',
    product_id: '#product_id',
    project_id: '#project_id',
    storage_id: '#storage_id',
    user_id: '#form_user_id', // в шапке ещё один #user_id затаился
    form: '#remainders_form',
    button: '#button',
    clear: '#clear',

    init: function() {
        let self = this;
        self.bindEvents();
        $(self.user_id).select2();
        if ($(self.project_id).is('select'))
            $(self.project_id).select2();
        if ($(self.storage_id).is('select'))
            $(self.storage_id).select2();
        self.productSelect2();
        $(self.date).datetimepicker({
            'format' : 'd.m.Y H:i:s'
        });

    },
    bindEvents: function () {
        let self = this;
        $(document)
            .on('submit', self.form, function(e) {
                return self.formSubmit(e);
            })
            .on('click', self.clear, function() {
                self.clearForm();
            });
    },
    productSelect2: function () {
        let self = this;

        $(self.product_id).select2({
            placeholder: $(self.product_id).attr('placeholder'),
            minimumInputLength: 2,
            multiple: false,
            initSelection : function (element, callback) {
                let product_name = $(element).attr('product_name');
                if (product_name && (product_name != '')) {
                    $(element).parent().find('.select2-choice .select2-chosen').text(product_name);
                }
            },
            ajax: {
                url: $(self.product_id).attr('data-url'),
                type: 'POST',
                dataType: 'json',
                data: function (word) {
                    return {word: $.trim(word)};
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
    formSubmit: function(e) {
        let self = this;
        e.preventDefault();

        let form = $(self.form).serializeArray();

        let data = {};
        for (let i in form) {
            if (($.inArray(form[i]['name'], ['_token']) == -1)
                && form[i]['value'] && (form[i]['value'] != '') && (form[i]['value'].length > 0)) {
                    data[form[i]['name']] = form[i]['value'];
            }
        }
        let j = false;
        if (!data.length) {
            let cl = window.location;
            link = cl.protocol + '//' + cl.hostname + cl.pathname;
            for (i in data) {
                link += j ? '&' : '?';
                link += encodeURI(i) + '=' + encodeURI(data[i])
                j = true;
            }
            $(self.submit).attr('disabled',true);
            $(self.clear_form).attr('disabled',true);
            window.location = link
        }
        return false;
    },
    clearForm: function() {
        let self = this;
        $(self.submit).attr('disabled',true);
        $(self.clear).attr('disabled',true);
        window.location.href = $(self.clear).attr('data-url');
    },
};


$(function () {
    $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});

    /*$('.datetimepicker').datetimepicker({
        'format' : 'Y-m-d H:i:s'
    });*/

    Remainders.init();
});