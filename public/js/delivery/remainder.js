jQuery('.datetimepicker').datetimepicker({
    'format' : 'Y-m-d H:i:s'
});

$(document).ready(function() {
    if ($('#table_remainders').length) {
        let table = $('#table_remainders').dataTable({
            //'info': false,
            //'pageLength': 50,
            //'sDom': 'lTfr<"clearfix">tip',
            /*'oTableTools': {
                'aButtons': [
                    {
                        'sExtends':    'collection',
                        'sButtonText': '<i class="fa fa-cloud-download"></i>&nbsp;&nbsp;&nbsp;<i class="fa fa-caret-down"></i>',
                        'aButtons':    [ 'csv', 'xls', 'pdf', 'copy', 'print' ]
                    }
                ]
            }*/

            //'bPaginate': false,
            'bLengthChange': false,
            'bFilter': true,
            'bInfo': false,
            'bAutoWidth': false,

            'fnDrawCallback': function(oSettings) {
                if (oSettings._iDisplayLength >= oSettings.fnRecordsDisplay()) {
                    $(oSettings.nTableWrapper).find('.dataTables_paginate').hide();
                }
            }
        });

        /*let tt = new $.fn.dataTable.TableTools( table );
        $( tt.fnContainer() ).insertBefore('div.dataTables_wrapper');

        new $.fn.dataTable.FixedHeader( table );*/
    }

});