<!--  Extra Large modal example -->
<div class="modal" id="create-modal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myExtraLargeModalLabel">Add Indicator Data (Excel-like Table)</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form action="{{ url('admin/kpi/save_data') }}" method="post" id='kpi-data-form' class='kpi-data-form'>
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> You can add multiple rows at once. Click "Add Row" to add more entries, or enter data directly in the table below.
                    </div>
                    
                    <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                        <table class="table table-bordered table-hover" id="indicator-data-table">
                            <thead class="thead-light" style="position: sticky; top: 0; z-index: 10; background: white;">
                                <tr>
                                    <th style="width: 20%;">Country <span class="text-danger">*</span></th>
                                    <th style="width: 25%;">Indicator <span class="text-danger">*</span></th>
                                    <th style="width: 10%;">Year <span class="text-danger">*</span></th>
                                    <th style="width: 10%;">Month <span class="text-danger">*</span></th>
                                    <th style="width: 15%;">Value <span class="text-danger">*</span></th>
                                    <th style="width: 10%;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="indicator-data-tbody">
                                <tr class="data-row">
                                    <td>
                                        <select class="form-control form-control-sm country-select" name="data[0][country_id]" required>
                                            <option value="">Choose Country</option>
                                            @foreach($countries as $country)
                                                <option value="{{$country->id}}">{{$country->name}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control form-control-sm kpi-select" name="data[0][kpi_id]" required>
                                            <option value="">Choose Indicator</option>
                                            @foreach($kpis as $kpi)
                                                <option value="{{$kpi->id}}">{{$kpi->name}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control form-control-sm year-select" name="data[0][year]" required>
                                            <option value="">Choose Year</option>
                                            @for($i=date('Y')-5 ;$i<= date('Y')+1; $i++)
                                                <option value="{{$i}}" {{ $i == date('Y') ? 'selected' : '' }}>{{$i}}</option>
                                            @endfor
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control form-control-sm month-select" name="data[0][month]" required>
                                            <option value="">Choose Month</option>
                                            @for($i=1 ; $i<=12; $i++)
                                                <option value="{{$i}}" {{ $i == date('m') ? 'selected' : '' }}>{{$i}}</option>
                                            @endfor
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" step="any" class="form-control form-control-sm value-input" name="data[0][indicator_value]" placeholder="0.00" required/>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-danger remove-row" style="display: none;">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        <button type="button" class="btn btn-sm btn-success" id="add-row-btn">
                            <i class="fa fa-plus"></i> Add Row
                        </button>
                        <span class="ml-2 text-muted" id="row-count">1 row(s)</span>
                    </div>

                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger" data-dismiss="modal" type="button">Cancel</button>
                    <button class="btn btn-primary" type="submit">
                        <i class="fa fa-save"></i> Save All Records
                    </button>
                </div>

            </form>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

<script>
$(document).ready(function() {
    let rowIndex = 1;
    
    // Store countries and KPIs options as JSON for dynamic row creation
    var countriesOptions = @json($countries->map(function($c) { return ['id' => $c->id, 'name' => $c->name]; }));
    var kpisOptions = @json($kpis->map(function($k) { return ['id' => $k->id, 'name' => $k->name]; }));
    var currentYear = {{ date('Y') }};
    var currentMonth = {{ date('m') }};
    
    // Add new row
    $('#add-row-btn').on('click', function() {
        var countriesHtml = '<option value="">Choose Country</option>';
        countriesOptions.forEach(function(country) {
            countriesHtml += '<option value="' + country.id + '">' + country.name + '</option>';
        });
        
        var kpisHtml = '<option value="">Choose Indicator</option>';
        kpisOptions.forEach(function(kpi) {
            kpisHtml += '<option value="' + kpi.id + '">' + kpi.name + '</option>';
        });
        
        var yearsHtml = '<option value="">Choose Year</option>';
        for (var y = currentYear - 5; y <= currentYear + 1; y++) {
            var selected = (y == currentYear) ? ' selected' : '';
            yearsHtml += '<option value="' + y + '"' + selected + '>' + y + '</option>';
        }
        
        var monthsHtml = '<option value="">Choose Month</option>';
        for (var m = 1; m <= 12; m++) {
            var selected = (m == currentMonth) ? ' selected' : '';
            monthsHtml += '<option value="' + m + '"' + selected + '>' + m + '</option>';
        }
        
        const newRow = '<tr class="data-row">' +
            '<td><select class="form-control form-control-sm country-select" name="data[' + rowIndex + '][country_id]" required>' + countriesHtml + '</select></td>' +
            '<td><select class="form-control form-control-sm kpi-select" name="data[' + rowIndex + '][kpi_id]" required>' + kpisHtml + '</select></td>' +
            '<td><select class="form-control form-control-sm year-select" name="data[' + rowIndex + '][year]" required>' + yearsHtml + '</select></td>' +
            '<td><select class="form-control form-control-sm month-select" name="data[' + rowIndex + '][month]" required>' + monthsHtml + '</select></td>' +
            '<td><input type="number" step="any" class="form-control form-control-sm value-input" name="data[' + rowIndex + '][indicator_value]" placeholder="0.00" required/></td>' +
            '<td><button type="button" class="btn btn-sm btn-danger remove-row"><i class="fa fa-trash"></i></button></td>' +
            '</tr>';
        
        $('#indicator-data-tbody').append(newRow);
        
        // Reinitialize Select2 if available
        if (typeof $.fn.select2 !== 'undefined') {
            $('#indicator-data-tbody tr:last .country-select, #indicator-data-tbody tr:last .kpi-select').select2({
                width: '100%',
                dropdownParent: $('#create-modal')
            });
        }
        
        rowIndex++;
        updateRowCount();
    });
    
    // Remove row
    $(document).on('click', '.remove-row', function() {
        $(this).closest('tr').remove();
        updateRowCount();
    });
    
    // Update row count
    function updateRowCount() {
        const count = $('#indicator-data-tbody tr').length;
        $('#row-count').text(count + ' row(s)');
        
        // Show/hide remove buttons
        if (count > 1) {
            $('.remove-row').show();
        } else {
            $('.remove-row').hide();
        }
    }
    
    // Form submission - validate at least one row has all fields
    $('#kpi-data-form').on('submit', function(e) {
        let hasValidRow = false;
        $('#indicator-data-tbody tr').each(function() {
            const country = $(this).find('.country-select').val();
            const kpi = $(this).find('.kpi-select').val();
            const year = $(this).find('.year-select').val();
            const month = $(this).find('.month-select').val();
            const value = $(this).find('.value-input').val();
            
            if (country && kpi && year && month && value) {
                hasValidRow = true;
            }
        });
        
        if (!hasValidRow) {
            e.preventDefault();
            alert('Please fill in at least one complete row with all required fields.');
            return false;
        }
    });
    
    // Initialize Select2 for better UX
    if (typeof $.fn.select2 !== 'undefined') {
        $('#create-modal').on('shown.bs.modal', function() {
            $('.country-select, .kpi-select').select2({
                width: '100%',
                dropdownParent: $('#create-modal')
            });
        });
    }
});
</script>