@extends('layouts.plain')

@section('content')

<section class="middle gray">
				<div class="container">
					<div class="row align-items-center justify-content-center">
					
						
						<div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 mfliud">
							<form class="border p-3 rounded bg-white"  method="POST" action="{{ route('register') }}">
                               <h3 class="py-3 text-success">Register for an account</h3>	

								<div class="row">
									<div class="form-group col-md-6">
										<label>First Name *</label>
										<input type="text" class="form-control @error('firstname') is-invalid @enderror" name="firstname" placeholder="First Name" value="{{ old('firstname') }}" required autocomplete="firstname" autofocus>
                                        @error('firstname')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
									</div>
									<div class="form-group col-md-6">
										<label>Last Name</label>
										<input type="text" class="form-control @error('lastname') is-invalid @enderror" name="lastname" placeholder="Last Name" value="{{ old('lastname') }}" required autocomplete="lastname" autofocus>
                                        @error('lastname')
											<span class="invalid-feedback" role="alert">
												<strong>{{ $message }}</strong>
											</span>
                                        @enderror
									</div>

									<div class="form-group col-md-12">
										<label>Preferences *</label>
										<select class="form-control @error('preferences') is-invalid @enderror" name="preferences">
											<option></option>
										</select>
                                        @error('preferences')
											<span class="invalid-feedback" role="alert">
												<strong>{{ $message }}</strong>
											</span>
                                        @enderror
									</div>

								</div>
								
								<div class="form-group">
									<label>Email *</label>
									<input type="text" class="form-control @error('email') is-invalid @enderror" name="email" placeholder="Email Address*" value="{{ old('email') }}" required autocomplete="off" autofocus>
                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
								</div>
								
								<div class="row">
									<div class="form-group col-md-6">
										<label>Password *</label>
										<input type="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password*" value="{{ old('password') }}" required autocomplete="off" autofocus>
                                        @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
									</div>
									
									<div class="form-group col-md-6">
										<label>Confirm Password *</label>
										<input type="password" class="form-control" placeholder="Confirm Password*">
									</div>
								</div>
								
								<div class="form-group">
									<p>By registering your details, you agree with our Terms & Conditions, and Privacy and Cookie Policy.</p>
								</div>
								
								<div class="form-group">
									<div class="d-flex align-items-center justify-content-between">
										<div class="flex-1">
											<input id="ddd" class="checkbox-custom" name="ddd" type="checkbox">
											<label for="ddd" class="checkbox-custom-label">Sign me up for the Newsletter!</label>
										</div>		
									</div>
								</div>
								
								<div class="form-group">
									<button type="submit" class="btn btn-md full-width theme-bg text-light fs-md ft-medium">Create An Account</button>
								</div>
							</form>
						</div>
						
					</div>
				</div>
			</section>


@endsection

@section('scripts')

 @include('common.select2')

@endsection
