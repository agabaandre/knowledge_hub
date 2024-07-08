@extends('admin.layouts.main')

@section('styles')
    @include('common.table')
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title">{{ $description ?? 'Thematic Area' }}</h1>
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Dropdown Lists</a></li>
            <li class="breadcrumb-item active" aria-current="page">Security Themes</li>
        </ol>
    </div>
</div>
    <div class="row">
        <div class="card col-lg-12">
           
            <!-- Card Header With Form Filters -->
            <div class="card-header">
                <form class="container-fluid">
                    <div class="row">

                        <div class="col-md-12 text-right">
                            <a href="#create-modal" data-toggle="modal" class="btn btn-outline-success float-right"><i
                                    class="fa fa-plus"></i> Add Thematic Area</a>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="description">Search</label>
                                <input type="text" name="term" id="filterTitle" class="form-control"
                                    placeholder="Filter by name" value="{{ @$search->term ?? '' }}">
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
                            <th>Thematic Area</th>
                            <th>Icon</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>

                        @php
$i = 1;
                        @endphp

                        @foreach ($themes as $row)
                            <tr>
                                <td>{{ $i++ }}</td>
                                <td>{{ $row->description }}</td>
                                <td>{{ $row->icon }}</td>
                                <td>
                                    <a href="#edit-theme-modal" class="btn btn-sm btn-success mr-1" data-toggle="modal"
                                        data-id="{{ $row->id }}" data-description="{!! $row->description !!}" data-icon="{!! $row->icon !!}">Edit
                                        Theme</a>

                                    <a class="btn btn-sm btn-danger ml-1" href="javascript:void(0);"
                                        onclick="openDeleteModal('{{ $row->id }}')" class="text-danger"> Delete</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="py-2"> {{ $themes->links() }}</div>

            </div>

        </div>

        @include('admin.themes.partials.create-modal')
        <!-- Include edit-modal.php -->
        @include('admin.themes.partials.edit-modal')
        <!-- Include delete-modal.php -->
        @include('admin.themes.partials.delete-modal')
    @endsection
