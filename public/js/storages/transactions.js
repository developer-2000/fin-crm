let Transactions = {

    type_select: '#type_select',
    project_select: '#project_select',
    subproject_select: '#subproject_select',
    product_id: '#product_id',
    user_select: '#user_select',
    date_start: '#created_at_start',
    date_end: '#created_at_end',
    date_radio: '#date_template :radio',
    form: '#transactions_form',
    submit: '#submit',
    clear_form: '#clear_form',
    popup_trigger: '.popup_trigger',
    popup_shower: '.popup_shower',

    init: function() {
        let self = this;
        self.bindEvents();
        $(self.type_select).select2();
        $(self.project_select).select2();
        $(self.subproject_select).select2();
        self.productSelect2();
        $(self.user_select).select2();
        self.myDatepicker($(self.date_start));
        self.myDatepicker($(self.date_end));
    },
    bindEvents: function () {
        let self = this;
        $(document)
            .on('change', self.date_radio, function(e) {
                return self.getDataTemplate(e);
            })
            .on('submit', self.form, function(e) {
                return self.formSubmit(e);
            })
            .on('click', self.clear_form, function() {
                self.clearForm();
            })
            .on('mouseover', self.popup_trigger, function(e) {
                self.popup_show(e);
            })
            .on('mouseout', self.popup_trigger, function(e) {
                self.popup_hide(e);
            });
    },
    myDatepicker: function(obj) {
        let self = this;
        let start = new Date();
        obj.datepicker({
            language: 'ru',
            startDate: start,
            onSelect: function (fd, d, picker) {
                if (!d) {
                    return false;
                }
            }
        });
    },
    getDataTemplate: function(e) {
        let self = this;
        let obj = $(e.currentTarget);
        let type = obj.val();
        $.post('/date-filter-template-ajax/', {type: type}, function(json) {
            $(self.date_start).val(json.start);
            $(self.date_end).val(json.end);
        }, 'json');
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
            if (($.inArray(form[i]['name'], ['date_template', '_token']) == -1)
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
        $(self.clear_form).attr('disabled',true);
        window.location.href = $(self.clear_form).attr('data-url');
    },
    popup_show: function(e) {
        let self = this;
        $(e.target).parent().find(self.popup_shower).removeClass('hide');
    },
    popup_hide: function(e) {
        let self = this;
        $(e.target).parent().find(self.popup_shower).addClass('hide');
    }
};


$(function () {
    //$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});

    /*$('.datetimepicker').datetimepicker({
        'format' : 'Y-m-d H:i:s'
    });*/

    Transactions.init();
});