<!--  Extra Large modal example -->
<div class="modal" id="create-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myExtraLargeModalLabel">Add Indicator Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form action="{{ url('admin/kpi/save_data') }}" method="post" id='filetypes' class='filetypes'>
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="id" id="id" class="newform">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="name">Country</label>
                                <select class="form-control" name="country_id">
                                    <option selected disabled>Choose Country</option>
                                    @foreach($countries as $country)
                                        <option value="{{$country->id}}">{{$country->name}}</option>
                                    @endforeach;
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="name">Indicator</label>
                                <select class="form-control" name="kpi_id">
                                    <option selected disabled>Choose KPI</option>
                                    @foreach($kpis as $kpi)
                                    <option value="{{$kpi->id}}">{{$kpi->name}}</option>
                                    @endforeach;
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="name">Year</label>
                                <select class="form-control" name="year">
                                    <option selected disabled>Choose Year</option>
                                    @for($i=date('Y')-1 ;$i< date('Y')+1; $i++)
                                    <option value="{{$i}}">{{$i}}</option>
                                    @endfor;
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="name">Month</label>
                                <select class="form-control" name="month">
                                    <option selected disabled>Choose Month</option>
                                    @for($i=1 ; $i<=12; $i++)
                                    <option value="{{$i}}">{{$i}}</option>
                                    @endfor;
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="indicator_value">Indicator Value</label>
                                <input type="number" placeholder="Indicator Value" class="form-control" id="indicator_value" name="indicator_value" required/>
                            </div>
                        </div>

                    </div>

                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger" data-dismiss="modal" type="button">Cancel</button>
                    <button class="btn btn-primary" type="submit">Save Record</button>
                </div>

            </form>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>