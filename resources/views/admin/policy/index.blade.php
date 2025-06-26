@extends('admin.layouts.main')

@section('styles')


@endsection

@section('content')
<div class="row">
	<div class="card col-lg-12">
		<div class="card-header text-left">
			<h3 class="card-title float-left">{{ $title ?? 'Privacy Policy' }}</h3>
			 <hr>
		</div>
		<!-- Card Header With Form Filters -->
		<div class="card-body">
			<form  class="container-fluid" id="form-privacy-policy">
				  <div class="row">
				   
						<div class="form-group col-md-12">
							<label for="title">Edit Policy</label>
							<textarea name="policy"  class="form-control summernote-lg">{!! $policy !!}</textarea>
						</div>

                        <div class="form-group col-lg-4 col-lg-offset-8">
                            <button type="submit" class="btn btn-success">Update Policy</button>
                        </div>
					
				</div>

			
            </form>
		</div>
		

	</div>


@endsection

@section('scripts')

@include('partials.general.summernote')
@include('common.sweet-alert')

<script>

$('#form-privacy-policy').on('submit', function(e) {
		e.preventDefault();
		var data = $(this).serialize();

		$.ajax({
			url: '{{  url("admin/privacy/save") }}',
			type: 'POST',
			data: data,
			success: function(response) {
            
				if (response.status == 'success') {
					swal('Success','Privacy Policy Updated Successfully','success');
				} else {
					swal('Failure','Something went wrong!','error');
				}
			}
		});
	});
</script>

@endsection