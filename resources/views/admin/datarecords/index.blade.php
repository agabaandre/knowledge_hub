@extends('admin.layouts.main')

@section('styles')
    @include('common.table')
@endsection

@section('content')
    <div class="row">
        <div class="card col-lg-12">
            <div class="card-header text-left">
                <h3 class="card-title float-left">{{ $title ?? 'Categories' }}</h3>
                <hr>
            </div>
            <!-- Card Header With Form Filters -->
            <div class="card-header">
                <form class="container-fluid">
                    <div class="row">

                        <div class="col-md-12 text-right">
                            <a href="{{ url('/admin/datarecords/create') }}" class="btn btn-outline-success float-right"><i class="fa fa-plus"></i> Create Data Record</a>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="title">Search</label>
                                <input type="text" name="term" id="filterTitle" class="form-control"
                                       placeholder="Filter by name"
                                       value="{{ @$search->term ?? '' }}"
                                >
                            </div>
                        </div>


                    </div>

                    <div class="row">
                        <div class="col-md-12 text-right">
                            <!-- Export Button -->
                            <button type="submit" id="filterButton" class="btn btn-primary btn-sm">Filter Data</button>
                            <button type="button" id="reset" class="btn btn-secondary btn-sm">Reset</button>
                            <button type="button" id="exportButton" class="btn btn-success btn-sm">Export Data</button>

                        </div>
                    </div>
                </form>
            </div>
            <div class="card-body text-left">
                <!-- Datatable -->
                <table id="publicationTable" class="table table-striped table-bordered">
                    <thead>
                    <tr>
						<th>#</th>
                        <th>Title</th>
						<th>Description</th>
						<th>Country</th>
						<th>Category / SubCategory</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>

                    @php
                        $i = 1;
                    @endphp

					@foreach($records as $record)
						<tr>
							<td width="5%"><i class="fa {{ $record->file_type->icon }} fa-2x text-muted"></i></td>
							<td>
                              <a href="{{ $record->publication }}" target="_blank">
								{!!truncate($record->title, 30) !!}
                              </a>
							</td>
							<td>
								{!! truncate($record->description, 50) !!}
							</td>
							<td>
								{{ $record->country->name ?? '' }}
							</td>
							<td>
							
								<!-- Edit Modal Action -->
								<a href="{{ url('admin/datarecords/edit') }}?id={{$record->id}}" type="button" class="btn btn-primary btn-sm" 
								>Edit</a>
								<!-- Delete Modal Action -->
								<a class="btn btn-sm btn-danger ml-1" href="javascript:void(0);" onclick="openDeleteModal('{{ $record->id }}')" class="text-danger"> Delete</a>
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>

                <div class="py-2"> {{$records->links() }}</div>

            </div>

        </div>

        <!-- @include('admin.datarecords.partials.create-modal', [
            'countries' => $countries,
            'categories' => $categories
        ]) -->
        <!-- Include edit-modal.php -->
        <!-- @include('admin.datarecords.partials.edit-modal') -->
        <!-- Include delete-modal.php -->
        <!-- @include('admin.datarecords.partials.delete-modal') -->


    </div>
@endsection
