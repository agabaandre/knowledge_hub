@extends(admin_layout())

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
                                <label for="title">{{ ui_t('general.search', 'Search') }}</label>
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
                            <button type="submit" id="filterButton" class="btn btn-primary btn-sm">{{ ui_t('general.filter_data', 'Filter Data') }}</button>
                            <button type="button" id="reset" class="btn btn-secondary btn-sm">{{ ui_t('general.reset', 'Reset') }}</button>
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

                    @foreach($records as $row)
                        <tr>
							<td>{{ $row->id }}</td>
                            <td>{{ $row->title }}</td>
							<td>{!!  truncate(html_to_text($row->description), 80) !!}</td>
							<td>{{ $row->country->name }}</td>
							<td>{{ $row->category->category_name }}/{{ optional($row->sub_category)->sub_category_name }}</td>

                            <td>
                                <a href="{{ url('/admin/datarecords/edit?id=' . $row->id)}}" class="btn btn-sm btn-primary ml-1">{{ ui_t('general.edit', 'Edit') }}</a>
                                <a href="javascript:void(0);" class="btn btn-sm btn-danger ml-1"
                                   onclick="openDeleteModal('{{ $row->id }}')">{{ ui_t('general.delete', 'Delete') }}</a>
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
