jQuery(document).ready(function($){

    $.ajaxSetup({
        beforeSend: function(xhr, settings) {
            settings.data += "&_token=<?php echo csrf_token() ?>";
        }
    });

    $('.form-import').on('ajax:success', function (e, data) {
        $('div.success-import strong.counter').text(data.counter);
        $('div.success-import').slideDown();
        window.location.reload();
    });

    $('.form-find').on('ajax:success', function (e, data) {
        $('div.success-find strong.counter').text(data.counter);
        $('div.success-find').slideDown();
        window.location.reload();
    });

    $('.form-publish-all').on('ajax:success', function (e, data) {
        $('div.success-publish-all').slideDown();
    });

    $('#new-locales').select2({
        width : '100%'
    });
});