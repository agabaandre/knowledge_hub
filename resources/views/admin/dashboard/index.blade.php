@extends('admin.layouts.main')

@section('content')

<div class="row">
  <div class="col-sm-3">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">{{ count($publications)}} Publications</h5>
        <div class="progress mt-1 mb-2" style="height: 5px;">
          <div class="progress-bar progress-bar-striped" role="progress-bar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <p class="card-text">Total Number of publications</p>
        <a href="{{ url('admin/publications') }}" class="text-primary">View List</a>
      </div>
    </div>
  </div>
  <div class="col-sm-3">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">{{ count($authors)}} Resource Authors</h5>
        <div class="progress mt-1 mb-2" style="height: 5px;">
          <div class="progress-bar progress-bar-striped" role="progress-bar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <p class="card-text">Number of resource conttibuting authors</p>
        <a href="{{ url('admin/authors') }}" class="text-primary">View List</a>
      </div>
    </div>
  </div>
  <div class="col-sm-3">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title"> {{  count($experts) }} Experts</h5>
        <div class="progress mt-1 mb-2" style="height: 5px;">
          <div class="progress-bar progress-bar-striped" role="progress-bar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <p class="card-text">Workforce Experts</p>
        <a href="{{ url('admin/experts') }}" class="text-primary">View List</a>
      </div>
    </div>
  </div>
  <div class="col-sm-3">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">{{  count($forums) }} Forum Discussions</h5>
        <div class="progress mt-1 mb-2" style="height: 5px;">
          <div class="progress-bar progress-bar-striped" role="progress-bar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <p class="card-text">Active Forum Discussions</p>
        <a href="{{ url('admin/forums') }}" class="text-primary">View List</a>
      </div>
    </div>
  </div>
</div>


<!--row 2-->
<!--
<div class="row bg-white rounded">
  <div class="col-sm-3">
      <div class="card-body">
        <h5 class="card-title">Statistics 34</h5>
        <div class="progress mt-1 mb-2" style="height: 5px;">
          <div class="progress-bar progress-bar-striped" role="progress-bar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
        <a href="#" class="text-primary">View List</a>
      </div>
  </div>
  <div class="col-sm-3">
      <div class="card-body">
        <h5 class="card-title">Statistics 45</h5>
        <div class="progress mt-1 mb-2" style="height: 5px;">
          <div class="progress-bar progress-bar-striped" role="progress-bar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
        <a href="#" class="text-primary">View List</a>
      </div>
  </div>
  <div class="col-sm-3">
      <div class="card-body">
        <h5 class="card-title">Statistics 89</h5>
        <div class="progress mt-1 mb-2" style="height: 5px;">
          <div class="progress-bar progress-bar-striped" role="progress-bar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
        <a href="#" class="text-primary">View List</a>
      </div>
  </div>
  <div class="col-sm-3">
      <div class="card-body">
        <h5 class="card-title">Statistics 80</h5>
        <div class="progress mt-1 mb-2" style="height: 5px;">
          <div class="progress-bar progress-bar-striped" role="progress-bar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
        <a href="#" class="text-primary">View List</a>
      </div>
  </div>
</div>-->


<div class="row bg-white rounded mt-4">

<div class="col-lg-12">
  <h4 class="mt-3">Most Recent Resources</h4>

  <table class="table table-striped">
    <thead>
      <tr>
        <th>#</th>
        <th width="10%">Created</th>
        <th>Title</th>
        <th>Description</th>
        <th>Author</th>
      </tr>
    </thead>
    <tbody>
      @foreach($publications as $row)
      <tr>
          <td>#</td>
          <td>{!! time_ago($row->created_at) !!}</td>
          <td>{!! $row->title !!}</td>
          <td>{!! truncate($row->description,20) !!}</td>
          <td>{!! truncate($row->author->name,20) !!}</td>
        </tr>
        @endforeach
    </tbody>
  </table>
</div>


</div>

@endsection