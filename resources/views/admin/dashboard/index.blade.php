@extends('admin.layouts.main')

@section('content')

<div class="row">
  <div class="col-sm-3">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Statistics 34</h5>
        <div class="progress mt-1 mb-2" style="height: 5px;">
          <div class="progress-bar progress-bar-striped" role="progress-bar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
        <a href="#" class="text-primary">View List</a>
      </div>
    </div>
  </div>
  <div class="col-sm-3">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Statistics 45</h5>
        <div class="progress mt-1 mb-2" style="height: 5px;">
          <div class="progress-bar progress-bar-striped" role="progress-bar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
        <a href="#" class="text-primary">View List</a>
      </div>
    </div>
  </div>
  <div class="col-sm-3">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Statistics 89</h5>
        <div class="progress mt-1 mb-2" style="height: 5px;">
          <div class="progress-bar progress-bar-striped" role="progress-bar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
        <a href="#" class="text-primary">View List</a>
      </div>
    </div>
  </div>
  <div class="col-sm-3">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Statistics 80</h5>
        <div class="progress mt-1 mb-2" style="height: 5px;">
          <div class="progress-bar progress-bar-striped" role="progress-bar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
        <a href="#" class="text-primary">View List</a>
      </div>
    </div>
  </div>
</div>


<!--row 2-->

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
</div>


<div class="row bg-white rounded mt-4">

<div class="col-lg-12">
  <h4 class="mt-3">Most Browsed Resources</h4>

  <table class="table table-striped">
    <thead>
      <tr>
        <th>#</th>
        <th>Value 0</th>
        <th>Value 1</th>
        <th>Value 2</th>
        <th>Value 3</th>
        <th>Value 4</th>
      </tr>
    </thead>
    <tbody>
      @foreach([1,2,3,4,4,4,4] as $row)
      <tr>
          <td>#</td>
          <td>Static header</td>
          <td>Value 1</td>
          <td>Value 2</td>
          <td>Value 3</td>
          <td>Value 4</td>
        </tr>
        @endforeach
    </tbody>
  </table>
</div>


</div>

@endsection