<script>
    window.khDataTableDefaults = window.khDataTableDefaults || {
        language: {
            search: '',
            searchPlaceholder: 'Search...',
            lengthMenu: 'Rows _MENU_',
            info: 'Showing _START_–_END_ of _TOTAL_',
            infoEmpty: 'No records',
            infoFiltered: '(filtered from _MAX_ total)',
            zeroRecords: 'No matching records found',
            emptyTable: 'No data available',
            paginate: {
                first: 'First',
                last: 'Last',
                next: 'Next',
                previous: 'Prev'
            },
            processing: '<i class="fa fa-spinner fa-spin mr-1"></i> Loading...'
        },
        dom: '<"row kh-dt-toolbar align-items-center mb-2"<"col-sm-12 col-md-6"l>>rt<"kh-dt-footer"<"dataTables_info"i><"dataTables_paginate"p>>'
    };

    (function ($) {
        if (!$.fn.dataTable) {
            return;
        }

        $.extend(true, $.fn.dataTable.defaults, window.khDataTableDefaults);

        $(document).on('init.dt', function (_e, settings) {
            var api = new $.fn.dataTable.Api(settings);
            var $wrapper = $(api.table().container());

            $wrapper.addClass('kh-datatable-wrapper');

            var $footer = $wrapper.find('.kh-dt-footer');
            if ($footer.length) {
                var $info = $footer.find('.dataTables_info').detach();
                var $paginate = $footer.find('.dataTables_paginate').detach();
                $footer.empty().append($info).append($paginate);
            }
        });
    })(jQuery);
</script>
