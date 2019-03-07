jQuery(document).ready(function($){

    $.ajaxSetup({
        beforeSend: function(xhr, settings) {
            settings.data += "&_token=<?php echo csrf_token() ?>";
        }
    });

    $('.editable').editable({
        validate:function(value){
            if(value.trim().length == 0) return 'Please insert translate';
        }
    }).on('hidden', function(e, reason){
        var locale = $(this).data('locale');
        if(reason === 'save'){
            $(this).removeClass('status-0').addClass('status-1');
        }
        if(reason === 'save' || reason === 'nochange') {
            var $next = $(this).closest('tr').next().find('.editable.locale-'+locale);
            setTimeout(function() {
                $next.editable('show');
            }, 300);
        }
    });

    $("a.delete-key").click(function(event){
        event.preventDefault();
        var row = $(this).closest('tr');
        var url = $(this).attr('href');
        var id = row.attr('id');
        $.post( url, {id: id}, function(){
            row.remove();
        } );
    });

    $('.form-publish').on('ajax:success', function (e, data) {
        $('div.success-publish').slideDown();
    });
});