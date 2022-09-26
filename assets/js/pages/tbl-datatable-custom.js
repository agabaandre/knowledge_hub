'use strict';
$(document).ready(function() {
    // [ Zero-configuration ]
    $('#zero-configuration').DataTable();

    // [ HTML5-Export ]
    $('#key-act-button').DataTable({
        dom: 'Bfrtip',
        buttons: [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5'
        ]
    });

    // [ Columns-Reorder ]
    $('#col-reorder').DataTable({
        colReorder: true
    });

    // [ Fixed-Columns ]
    $('#fixed-columns-left').DataTable({
        scrollY: "300px",
        scrollX: true,
        scrollCollapse: true,
        paging: false,
        fixedColumns: true,
    });
    $('#fixed-columns-left-right').DataTable({
        scrollY: "300px",
        scrollX: true,
        scrollCollapse: true,
        paging: false,
        fixedColumns: true,
        fixedColumns: {
            leftColumns: 1,
            rightColumns: 1
        }
    });
    $('#fixed-header').DataTable({
        fixedHeader: true
    });

    // [ Scrolling-table ]
    $('#scrolling-table').DataTable({
        scrollY: 300,
        paging: false,
        keys: true
    });

    // [ Responsive-table ]
    $('#responsive-table').DataTable({
    });

    $('#responsive-table-model').DataTable({
        responsive: {
            details: {
                display: $.fn.dataTable.Responsive.display.modal({
                    header: function(row) {
                        var data = row.data();
                        return 'Details for ' + data[0] + ' ' + data[1];
                    }
                }),
                renderer: $.fn.dataTable.Responsive.renderer.tableAll({
                    tableClass: 'table'
                })
            }
        }
    });
});
