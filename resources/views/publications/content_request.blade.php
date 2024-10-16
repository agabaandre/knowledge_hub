@extends('layouts.plain')

@section('styles')
@endsection

@section('content')
    <div class="row">


        <div class="card col-lg-12">
            <div class="card-header text-left  pt-5">
                <h3 class="card-title float-left">Content Request Form</h3>
            </div>


            <div class="card-body text-left  py-5">


                <div class="container  px-5">

                    <form method="POST" action="{{ route('content-request') }}" id='publications' enctype="multipart/form-data"
                        class='publications' id="publication_form" data-parsley-validate="">
                        @csrf

                        <div class="row">

                            <div class="col-md-12 mt-3">
                                <div class="mb-3">
                                    <label class="form-label" for="title">What is the subject of the request? *</label>
                                    <input placeholder="Request Subject" class="form-control newform" id="title"
                                        name="title" value="{{ old('title') }}" required="">
                                </div>
                            </div>

                            <div class="col-md-6" style="{{ current_user() ? 'display:none;' : '' }}">
                                <div class="mb-3">
                                    <label class="form-label" for="publication">Which Country are you from? *</label>
                                    @include('partials.countries.dropdown', [
                                        'field' => 'country_id',
                                        'required' => 'required',
                                        'class' => 'select2 theme',
                                        'selected' =>
                                            old('country_id') ?? current_user() ? current_user()->country_id : '',
                                    ])
                                </div>
                            </div>
                            <div class="col-md-6" style="{{ current_user() ? 'display:none;' : '' }}">
                                <div class="mb-3">
                                    <label class="form-label" for="email">Email Address *</label>
                                    <input placeholder="Your Email Address" class="form-control newform" id="email"
                                        name="email"
                                        value="{{ old('email') ?? current_user() ? current_user()->email : '' }}"
                                        required="">
                                </div>
                            </div>


                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label" for="summernote">Share more details about your request
                                        *</label>
                                    <textarea placeholder="Descripion" class="form-control newform" id="summernote" name="description" required="">{!! old('description') !!}</textarea>
                                </div>
                            </div>
                            <div class="col-md-12 d-flex justify-content-end pr-3">

                                <input type="submit" class="btn btn-success mr-3" value="Submit Request">
                            </div>


                        </div>

                    </form>

                </div>
            </div>

        </div>


    </div>
@endsection

@section('scripts')
    @include('common.select2')
    @include('account.partials.create_js')
    @include('account.partials.wizard_js')
@endsection
