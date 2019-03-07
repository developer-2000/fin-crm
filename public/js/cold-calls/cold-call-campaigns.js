$('.company-select2').select2({
    placeholder: "Выберите компанию",
    minimumInputLength: 1,
    multiple: true,
    ajax: {
        url: '/company/find/',
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
