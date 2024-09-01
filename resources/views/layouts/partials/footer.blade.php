<!-- ============================ Footer Start ================================== -->

<footer class="{{ settings()->footer_style }} skin-dark-footer justify-content-center">
	<div class="footer-middle py-0">
		<div class="container">
			<div class="row">

				<div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 mb-4">
					<div class="footer_widget">
						<h4 class="widget_title">Address</h4>
						@if(settings()->footer_style == 'dark-footer')
							<img src="{{ settings()->logo }}" class="logo img-footer mb-1" alt="Logo"
								style="width:220px; filter: brightness(0) invert(1);">
						@else
							<img src="{{ settings()->logo }}" class="logo img-footer mb-1" alt="Logo" style="width:220px;">
						@endif

						<div class="address mt-0">
							<a href="https://www.google.com/maps/search/?api=1&query={!! urlencode(settings()->address) !!}"
								target="_blank" class="d-block">
								{!! settings()->address !!}
							</a>
						</div>
						<div class="address mt-0">
							<a href="tel:{!! settings()->phone !!}" class="d-block">
								{!! settings()->phone !!}
							</a>
							<a href="mailto:{!! settings()->email !!}" class="d-block">
								{!! settings()->email !!}
							</a>
						</div>
					</div>
				</div>

				<div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 mb-4">
					<div class="footer_widget">
						<h4 class="widget_title">Navigate</h4>
						<ul class="footer-menu list-unstyled">
							<li><a href="{{ url('/') }}" class="text-decoration-none">Home</a></li>
							@guest
								<li><a href="{{ url('login') }}" class="text-decoration-none">Login</a></li>
							@else
								<li><a href="{{ url('account/publish') }}" class="text-decoration-none">Publish a
										resource</a></li>
							@endguest
							<li><a href="{{ url('forums') }}" class="text-decoration-none">Discussion Forums</a></li>
							<li><a href="{{ url('courses') }}" class="text-decoration-none">Courses</a></li>
							
						</ul>
					</div>
				</div>

				<div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 mb-4">
					<div class="footer_widget">
						<h4 class="widget_title">About This Portal</h4>
						<ul class="footer-menu list-unstyled">
							<li><a href="{{ url('faqs') }}" class="text-decoration-none">Frequently asked questions</a>
							</li>
							<li><a href="https://africacdc.org" target="_blank" class="text-decoration-none">Africa CDC
									Website</a></li>
							<li><a href="{{ url('privacy') }}" class="text-decoration-none">Privacy Policy</a></li>
							<li><a href="{{ url('user_manual') }}" class="text-decoration-none">User Guide</a></li>
						</ul>
					</div>
				</div>

				<div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 mb-4">
					<div class="footer_widget">
						<h4 class="widget_title">Find us On</h4>
						<ul class="footer-menu list-inline">
							<li class="list-inline-item">
								<a href="{{ settings()->facebook }}" target="_blank" class="text-decoration-none"
									data-toggle="tooltip" data-placement="top" title="Facebook">
									<i class="fa-brands fa-facebook"></i>
								</a>
							</li>
							<li class="list-inline-item">
								<a href="{{ settings()->twitter }}" target="_blank" class="text-decoration-none"
									data-toggle="tooltip" data-placement="top" title="Twitter">
									<i class="fa-brands fa-x-twitter"></i>
								</a>
							</li>
							<li class="list-inline-item">
								<a href="{{ settings()->linkedin }}" target="_blank" class="text-decoration-none"
									data-toggle="tooltip" data-placement="top" title="LinkedIn">
									<i class="fa-brands fa-linkedin-in"></i>
								</a>
							</li>
							<li class="list-inline-item">
								<a href="{{ settings()->academia }}" target="_blank" class="text-decoration-none"
									data-toggle="tooltip" data-placement="top" title="Academia">
									<i class="fa-solid fa-a"></i>
								</a>
							</li>
							<li class="list-inline-item">
								<a href="{{ settings()->researchgate }}" target="_blank" class="text-decoration-none"
									data-toggle="tooltip" data-placement="top" title="ResearchGate">
									<i class="fa-brands fa-researchgate"></i>
								</a>
							</li>
						</ul>
					</div>
				</div>
				

			</div>
		</div>
	</div>

	<div class="footer-bottom py-2">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-lg-12 col-md-12 text-center">
					<p class="mb-0">© {{ date('Y') }} Africa CDC. All rights reserved.</p>
				</div>
			</div>
		</div>
	</div>
</footer>
<!-- ============================ Footer End ================================== -->

<!-- Log In Modal -->
<div class="modal fade" id="login" tabindex="-1" role="dialog" aria-labelledby="loginmodal" aria-hidden="true">
	<div class="modal-dialog modal-xl login-pop-form" role="document">
		<div class="modal-content" id="loginmodal">
			<div class="modal-headers">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span class="ti-close"></span>
				</button>
			</div>

			<div class="modal-body p-5">
				<div class="text-center mb-4">
					<h2 class="m-0 ft-regular">Login</h2>
				</div>

				<form action="{{ route('login') }}" method="post">
					@csrf

					<input type="hidden" name="route" value="front" />
					<div class="form-group">
						<label>User Name</label>
						<input type="email" name="email" class="form-control" placeholder="Username*"
							autocomplete="off">
					</div>

					<div class="form-group">
						<label>Password</label>
						<input type="password" name="password" class="form-control" placeholder="Password*"
							autocomplete="off">
					</div>

					<div class="form-group">
						<div class="d-flex align-items-center justify-content-between">
							<div class="flex-1">
								<input id="dd" class="checkbox-custom" name="remember" type="checkbox">
								<label for="dd" class="checkbox-custom-label">Remember Me</label>
							</div>
							<div class="eltio_k2">
								<a href="#" class="theme-cl">Lost Your Password?</a>
							</div>
						</div>
					</div>

					<div class="form-group">
						<button type="submit"
							class="btn btn-md full-width theme-bg text-light fs-md ft-medium">Login</button>
					</div>

				</form>
			</div>
		</div>
	</div>
</div>
<!-- End Modal -->

<a id="back2Top" class="top-scroll" title="Back to top" href="#"><i class="ti-arrow-up"></i></a>

@if(!get_cookie('is_returning'))
	<div class="cookie-consent">
		<span>This site uses cookies to enhance user experience. see<a href="{{ url('privacy_policy/read') }}"
				class="ml-1 text-decoration-none text-success">Privacy policy</a></span>
		<div class="mt-2 d-flex align-items-center justify-content-center g-2">
			<button class="allow-button mr-1" allow="1">Allow cookies</button>
			<button class="allow-button" allow="0">Cancel</button>
		</div>
	</div>
@endif

<script src="{{ asset('frontend/js/popper.min.js')}}"></script>
<script src="{{ asset('frontend/js/bootstrap.min.js')}}"></script>
<script src="{{ asset('frontend/js/slick.js')}}"></script>
<script src="{{ asset('frontend/js/slider-bg.js')}}"></script>
<script src="{{ asset('frontend/js/smoothproducts.js')}}"></script>
<script src="{{ asset('frontend/js/snackbar.min.js')}}"></script>
<script src="{{ asset('frontend/js/jQuery.style.switcher.js')}}"></script>
<script src="{{ asset('frontend/js/custom.js')}}"></script>
<script src="{{ asset('assets/plugins/select2/js/select2.min.js')}}"></script>
<script src="{{ asset('frontend/js/jquery-ui.js') }}"></script>
<script src="{{ asset('frontend/js/aos/dist/aos.js')}}"></script>

@include('layouts.partials.language')

<script type="text/javascript">

	AOS.init();

	$('[data-toggle="tooltip"]').tooltip();

	$('.allow-button').click(function () {
		var allow = $(this).attr('allow');

		if (parseInt(allow) === 1) {
			var date = new Date();
			date.setTime(date.getTime() + (90 * 24 * 60 * 60 * 1000));
			expires = "; expires=" + date.toUTCString();
			document.cookie = "is_returning" + "=" + "yes" + expires + "; path=/";
		}

		$('.cookie-consent').hide();
	});

	$('.autocomplete').autocomplete({
		source: "{{ url('records/autocomplete') }}",
		minLength: 5,
		select: function (event, ui) {
			console.log(ui.item);
			$('.term').val(ui.item.label);
			$('.search-form').submit();
		}
	});

	$(document).ready(function () {
		setTimeout(function () {
			$('.alert').fadeOut('slow')
		}, 10000);
	});
</script>