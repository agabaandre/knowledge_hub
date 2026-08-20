@extends(admin_layout())

@section('styles')
 @include('common.table')
 <link href="{{ asset('assets/plugins/datatable/css/jquery.dataTables.min.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="row">
	<div class="card col-lg-12">
		<div class="card-header text-left">
			<h3 class="card-title float-left">{{ $title ?? 'Administrative Units' }}</h3>
			 <hr>
		</div>
		<!-- Card Header With Form Filters -->
		<div class="card-header">
			<form  class="container-fluid" method="get" action="{{ url('admin/adminunits') }}" id="adminUnitsFilterForm">
				  <div class="row">
				   
				    <div class="col-md-12 text-right">
					 <a href="#create-modal" data-toggle="modal" class="btn btn-outline-success float-right"><i class="fa fa-plus"></i> Add Admin Unit</a>
					</div>

					<div class="col-md-12">
						<div class="form-group">
							<label for="filterTitle">Search</label>
							<input type="text" name="term" id="filterTitle" class="form-control"
								placeholder="Filter by name, code, or description"
                                value="{{ @$search->term ?? ''}}"
							>
						</div>
					</div>

					
				</div>

				<div class="row">
					<div class="col-md-12 text-right">
						<button type="submit" id="filterButton" class="btn btn-primary btn-sm">Filter Data</button>
						<button type="button" id="reset" class="btn btn-secondary btn-sm">Reset</button>
                        <button type="button" id="exportButton" class="btn btn-success btn-sm">Export Data</button>
						
					</div>

					@include('admin.adminunits.partials.create-modal',['row'=>null])
	
					
				</div>
            </form>
		</div>
		<div class="card-body text-left">
			@include('layouts.partials.alerts')
			<table id="adminUnitsTable" class="table table-striped table-bordered" data-kh-datatable="client">
				<thead>
					<tr>
					    <th style="width:70px;">Icon</th>
						<th>Unit Name</th>
						<th>Description</th>
						<th>Parent</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
					@foreach($adminunits as $row)
						<tr>
						    <td data-order="{{ e((string) ($row->icon ?: 'fa-building')) }}">
                                @if($row->logo)
                                    <img src="{{ storage_link("uploads/adminunits/".$row->logo) }}" width="50px" class="img img-thumbnail" alt=""/>
                                @else
                                    <span class="d-inline-flex align-items-center justify-content-center border rounded bg-light" style="width:50px;height:50px;">
                                        <i class="fa {{ $row->icon ?: 'fa-building' }} fa-lg text-secondary" aria-hidden="true"></i>
                                    </span>
                                @endif
                            </td>
							<td>{{ $row->name }}</td>
							<td>{{ $row->description }}</td>
							<td>{{ ($row->parent)?$row->parent->name:"N/A" }}</td>
							<td>
								<a class="btn btn-sm btn-danger ml-1" href="javascript:void(0);" onclick="openDeleteModal('{{ $row->id }}')"> Delete</a>
								<a class="btn btn-sm btn-primary ml-1" href="#edit{{$row->id}}" data-toggle="modal"> Edit</a>
							</td>
						</tr>
						@include('admin.adminunits.partials.edit-modal',['row'=>$row])
					@endforeach
				</tbody>
			</table>
		</div>

	</div>

	
	@include('admin.adminunits.partials.delete-modal')

@endsection

@section('scripts')
    @include('admin.partials.metadata_datatable')
    @include('common.attachment_js')
    <script>
    $(function () {
        window.khInitMetadataTable('#adminUnitsTable', {
            order: [[1, 'asc']],
            columnDefs: [
                { orderable: false, searchable: false, targets: [0, -1] }
            ],
            pageLength: 25
        });

        function initFaIconSelect($root) {
            var $select = $root.find('.select2-fa-icons');
            if (!$select.length || typeof $.fn.select2 !== 'function') {
                return;
            }
            if ($select.hasClass('select2-hidden-accessible')) {
                $select.select2('destroy');
            }
            $select.select2({
                placeholder: 'Select icon class',
                width: '100%',
                dir: 'ltr',
                dropdownParent: $root
            });
        }

        $('#create-modal').on('show.bs.modal', function () {
            var $modal = $(this);
            initFaIconSelect($modal);
            var $icon = $modal.find('select[name="icon"]');
            if ($icon.length && !$icon.val()) {
                $icon.val(@json($defaultUnitIcon ?? 'fa-building')).trigger('change');
            }
            var defaultCountry = @json($defaultOwnerCountryId ? (string) $defaultOwnerCountryId : null);
            var $country = $modal.find('select[name="country_id"]');
            if (defaultCountry && $country.length && !$country.val()) {
                $country.val(defaultCountry).trigger('change');
            }
        });

        $(document).on('show.bs.modal', '[id^="edit"]', function () {
            initFaIconSelect($(this));
        });
    });

     document.addEventListener('change', function (e) {
         if (!e.target.matches('.js-admin-unit-country')) return;
         var opt = e.target.options[e.target.selectedIndex];
         var iso2 = opt.getAttribute('data-iso2') || '';
         var iso3 = opt.getAttribute('data-iso3') || '';
         var form = e.target.closest('form');
         if (!form) return;
         var iso2Input = form.querySelector('.js-admin-unit-iso2');
         var iso3Input = form.querySelector('.js-admin-unit-iso3');
         if (iso2Input && iso2) iso2Input.value = iso2;
         if (iso3Input && iso3) iso3Input.value = iso3;
     });
     </script>
@endsection
