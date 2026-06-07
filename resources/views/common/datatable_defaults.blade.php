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

        function applyKhTableMobileLabels(tableNode) {
            var $table = $(tableNode);
            if (!$table.hasClass('kh-table-mobile-cards')) {
                return;
            }

            var labels = [];
            $table.find('thead th').each(function () {
                var custom = $(this).attr('data-mobile-label');
                labels.push(custom || $(this).text().trim());
            });

            $table.find('tbody tr').each(function () {
                $(this).find('td').each(function (i) {
                    if (labels[i]) {
                        $(this).attr('data-label', labels[i]);
                    }
                });
            });
        }

        $(document).on('init.dt draw.dt', function (_e, settings) {
            var api = new $.fn.dataTable.Api(settings);
            var $wrapper = $(api.table().container());
            var $table = $(api.table().node());

            $wrapper.addClass('kh-datatable-wrapper');

            if ($table.hasClass('kh-table-wrap-cells')) {
                $table.removeClass('nowrap');
            }

            applyKhTableMobileLabels($table);

            var $footer = $wrapper.find('.kh-dt-footer');
            if ($footer.length) {
                var $info = $footer.find('.dataTables_info').detach();
                var $paginate = $footer.find('.dataTables_paginate').detach();
                $footer.empty().append($info).append($paginate);
            }
        });
    })(jQuery);
</script>
