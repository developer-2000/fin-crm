let Movings = {
    status_select: '#status_select',
    sender_select: '#sender_select',
    receiver_select: '#receiver_select',
    date_start: '#date_start',
    date_end: '#date_end',
    date_radio: '#date_template :radio',
    form: '#movings_form',
    submit: '#submit',
    clear_form: '#clear_form',
    date_label: '.date_type label',

    init: function() {
        let self = this;
        self.bindEvents();
        $(self.status_select).select2();
        $(self.sender_select).select2();
        $(self.receiver_select).select2();
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
            .on('click', self.date_label, function(e) {
                self.changeLabel(e);
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
    changeLabel: function (e) {
        let self = this;
        let name = $(e.currentTarget).attr('id');
        $(self.date_start).attr('name', name + '_start');
        $(self.date_end).attr('name', name + '_end');
    },
    formSubmit: function(e) {
        let self = this;
        e.preventDefault();

        let form = $(self.form).serializeArray();

        let data = {};
        for (let i in form) {
            if (($.inArray(form[i]['name'], ['date-type', 'date_template', '_token']) == -1)
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
    }
};


$(function () {
    Movings.init();
});