let Countries = {

    use: '.country-use',

    init: function() {
        let self = this;

        $('#countries-table tbody').sortable({
            helper: self.fixHelper,
            axis: "y",
            handle: '.handle',
            update: function(e, ui) {
                $.ajax({
                    url: $(ui.item).attr('data-url'),
                    type: 'post',
                    dataType: 'json',
                    data: {
                        current: $(ui.item).attr('data-code'),
                        prev: $(ui.item).prev().attr('data-code')
                    }
                });
            }
        });

        self.bindEvents();
    },
    bindEvents: function () {
        let self = this;
        $(document)
            .on('change', self.use, function(e) {
                return self.useChange(e);
            });
    },
    fixHelper: function(e, ui) {
        ui.children().each(function() {
            $(this).width($(this).width());
        });
        return ui;
    },
    useChange: function(e) {
        let self = this;
        $.ajax({
            url: $(e.target).attr('data-url'),
            type: 'post',
            dataType: 'json',
            data: {'code': $(e.target).attr('data-code')}
        });

    },
};


$(function () {
    $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});

    Countries.init();
});