{{-- Shared DataTables init for admin metadata tables (themes / subthemes / tags). --}}
<script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
<script>
(function ($) {
    if (typeof $ === 'undefined' || !$.fn.DataTable) {
        return;
    }

    window.khInitMetadataTable = function (selector, options) {
        var $table = $(selector);
        if (!$table.length || $.fn.DataTable.isDataTable($table)) {
            return null;
        }

        var defaults = {
            autoWidth: false,
            ordering: true,
            searching: true,
            paging: true,
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
            order: [[1, 'asc']],
            columnDefs: [
                { orderable: false, targets: -1 },
                { orderable: false, searchable: false, targets: 0 }
            ],
            language: {
                search: 'Search:',
                lengthMenu: 'Show _MENU_',
                info: 'Showing _START_ to _END_ of _TOTAL_',
                zeroRecords: 'No matching records'
            }
        };

        var table = $table.DataTable($.extend(true, {}, defaults, options || {}));

        var $filter = $('#filterTitle');
        if ($filter.length && $filter.val()) {
            table.search(String($filter.val())).draw();
        }

        $('#filterButton').off('click.khMetaDt').on('click.khMetaDt', function (e) {
            e.preventDefault();
            table.search(String($filter.val() || '')).draw();
        });

        $('#reset').off('click.khMetaDt').on('click.khMetaDt', function (e) {
            e.preventDefault();
            $filter.val('');
            var $theme = $('#theme_id');
            if ($theme.length) {
                $theme.val('').trigger('change');
            }
            table.search('').columns().search('').draw();
        });

        return table;
    };

    /**
     * Select2 inside Bootstrap modals must set dropdownParent or the list is unusable.
     */
    window.khInitModalSelect2 = function ($select, $modal) {
        if (!$select || !$select.length || typeof $.fn.select2 !== 'function') {
            return;
        }
        if ($select.hasClass('select2-hidden-accessible')) {
            $select.select2('destroy');
        }
        $select.select2({
            width: '100%',
            dropdownParent: $modal && $modal.length ? $modal : $(document.body),
            placeholder: $select.data('placeholder') || 'Select an option',
            allowClear: true
        });
    };
})(jQuery);
</script>
