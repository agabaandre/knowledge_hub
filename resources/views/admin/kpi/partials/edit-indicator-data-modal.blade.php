<!-- Edit Indicator Data Modal -->
<div class="modal" id="edit-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Indicator Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form action="{{ url('admin/kpi/update_data') }}" method="post" id='edit-kpi-data-form'>
                @csrf
                <input type="hidden" name="old_kpi_id" id="edit_old_kpi_id">
                <input type="hidden" name="old_country_id" id="edit_old_country_id">
                <input type="hidden" name="old_period" id="edit_old_period">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_country_id">Country <span class="text-danger">*</span></label>
                        <select class="form-control" id="edit_country_id" name="country_id" required>
                            <option value="">Choose Country</option>
                            @foreach($countries as $country)
                                <option value="{{$country->id}}">{{$country->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="edit_kpi_id">Indicator <span class="text-danger">*</span></label>
                        <select class="form-control" id="edit_kpi_id" name="kpi_id" required>
                            <option value="">Choose Indicator</option>
                            @foreach($kpis as $kpi)
                                <option value="{{$kpi->id}}">{{$kpi->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_year">Year <span class="text-danger">*</span></label>
                                <select class="form-control" id="edit_year" name="year" required>
                                    <option value="">Choose Year</option>
                                    @for($i=date('Y')-5 ;$i<= date('Y')+1; $i++)
                                        <option value="{{$i}}">{{$i}}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_month">Month <span class="text-danger">*</span></label>
                                <select class="form-control" id="edit_month" name="month" required>
                                    <option value="">Choose Month</option>
                                    @for($i=1 ; $i<=12; $i++)
                                        <option value="{{$i}}">{{$i}}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="edit_indicator_value">Value <span class="text-danger">*</span></label>
                        <input type="number" step="any" class="form-control" id="edit_indicator_value" name="indicator_value" placeholder="0.00" required/>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger" data-dismiss="modal" type="button">Cancel</button>
                    <button class="btn btn-primary" type="submit">
                        <i class="fa fa-save"></i> Update Record
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

