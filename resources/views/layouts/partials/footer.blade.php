<!-- ============================ Footer Start ================================== -->
<style>
.footer-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 0.75rem;
}

.tag-pill {
    display: inline-block;
    padding: 0.25rem 0.6rem;
    font-size: 0.75rem;
    font-weight: 500;
    color: #ffffff !important;
    text-decoration: none;
    border-radius: 12px;
    transition: all 0.2s ease;
    white-space: nowrap;
    background-color: rgba(17, 154, 72, 0.8) !important;
    text-shadow: 0 1px 2px rgba(0,0,0,0.3);
    border: 1px solid rgba(255,255,255,0.2);
}

.tag-pill:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 6px rgba(0,0,0,0.3);
    color: #ffffff !important;
    text-decoration: none;
    background-color: rgba(17, 154, 72, 0.95) !important;
}
</style>

<footer class="{{ settings()->footer_style }} skin-dark-footer justify-content-center">
	<div class="footer-middle py-0">
		<div class="container">
			<div class="row">

				<div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 mb-4 notranslate">
					<div class="footer_widget">
						<h4 class="widget_title">{{ __('ui_body.footer_address') }}</h4>
						@php
							$footerLogoPx = (int)(settings()->logo_scale ?? 80);
							$footerLogoPx = in_array($footerLogoPx, [40,50,60,70,80,100,120]) ? $footerLogoPx : 80;
						@endphp
						@if(settings()->logo ?? null)
							<img src="{{ settings()->logo }}" class="logo img-footer mb-1 {{ (settings()->footer_logo_inverse ?? false) ? 'logo-inverse' : '' }}" alt="Logo" style="max-height:{{ $footerLogoPx }}px; width:auto;">
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

				<div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 mb-4 notranslate">
					<div class="footer_widget">
						<h4 class="widget_title">{{ __('ui_body.footer_navigate') }}</h4>
						<ul class="footer-menu list-unstyled">
							<li><a href="{{ url('/') }}" class="text-decoration-none">{{ __('ui_body.footer_home') }}</a></li>
							@guest
								<li><a href="{{ url('login') }}" class="text-decoration-none">{{ __('ui_body.footer_login') }}</a></li>
							@else
								<li><a href="{{ url('account/publish') }}" class="text-decoration-none">{{ __('ui_body.publish_a_resource') }}</a></li>
							@endguest
							<li><a href="{{ url('forums') }}" class="text-decoration-none">{{ __('ui_body.footer_discussion_forums') }}</a></li>
							<li><a href="{{ url('courses') }}" class="text-decoration-none">{{ __('ui_body.footer_courses') }}</a></li>
							
						</ul>
					</div>
				</div>

				<div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 mb-4 notranslate">
					<div class="footer_widget">
						<h4 class="widget_title">{{ __('ui_body.footer_about_portal') }}</h4>
						<ul class="footer-menu list-unstyled">
							<li><a href="{{ url('faqs') }}" class="text-decoration-none">{{ __('ui_body.footer_faq') }}
							</a></li>
							<li><a href="https://africacdc.org" target="_blank" class="text-decoration-none">{{ __('ui_body.footer_africa_cdc_website') }}</a></li>
							<li><a href="{{ url('privacy') }}" class="text-decoration-none">{{ __('ui_body.footer_privacy_policy') }}</a></li>
							<li><a href="https://github.com/Africa-cdc-Khub/knowledge_hub/wiki#user-guide" class="text-decoration-none">{{ __('ui_body.footer_user_guide') }}</a></li>
						</ul>
					</div>
				</div>

				<div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 mb-4 notranslate">
					<div class="footer_widget">
						<h4 class="widget_title">{{ __('ui_body.footer_find_us') }}</h4>
						<ul class="footer-menu list-inline mb-3">
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
							<li class="list-inline-item">
								<a href="https://play.google.com/store/apps/details?id=com.africacdc.khubmobile&hl=en" target="_blank"
									class="text-decoration-none" data-toggle="tooltip" data-placement="top" title="Get the Mobile App">
									<i class="fa-brands fa-google-play"></i>
								</a>
							</li>
						</ul>
						<p class="text-white small mb-3">{{ __('ui_body.footer_download_app') }}</p>
						
						{{-- Tags Section --}}
						@if((settings()->show_tags ?? false) && isset($tags) && count($tags) > 0)
						<div class="mt-3">
							<h5 class="widget_title mb-2" style="font-size:0.95rem;">{{ __('ui_body.footer_popular_tags') }}</h5>
							<div class="footer-tags">
								@php 
									$colors = [settings()->primary_color,settings()->primary_text_color,settings()->icon_font_color];
								@endphp
								@foreach($tags->take(5) as $tag)
								<a href="{{ tag_records_url($tag) }}" 
								   class="tag-pill" 
								   title="{{$tag->tag_text}}">
									{{ truncate($tag->tag_text,15) }}
								</a>
								@endforeach
							</div>
						</div>
						@endif
					</div>
				</div>
				

			</div>
		</div>
	</div>

	<div class="footer-bottom py-2">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-lg-12 col-md-12 text-center">
					<p class="mb-0 notranslate">{{ __('ui_body.footer_copyright', ['year' => date('Y')]) }}</p>
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
				<div class="text-center mb-4 notranslate">
					<h2 class="m-0 ft-regular">{{ __('ui_body.login_modal_title') }}</h2>
				</div>

				<form action="{{ route('login') }}" method="post">
					@csrf

					<input type="hidden" name="route" value="front" />
					<div class="form-group notranslate">
						<label>{{ __('ui_body.username') }}</label>
						<input type="email" name="email" class="form-control" placeholder="Username*"
							autocomplete="off">
					</div>

					<div class="form-group notranslate">
						<label>{{ __('ui_body.password') }}</label>
						<input type="password" name="password" class="form-control" placeholder="Password*"
							autocomplete="off">
					</div>

					<div class="form-group notranslate">
						<div class="d-flex align-items-center justify-content-between">
							<div class="flex-1">
								<input id="dd" class="checkbox-custom" name="remember" type="checkbox">
								<label for="dd" class="checkbox-custom-label">{{ __('ui_body.remember_me') }}</label>
							</div>
							<div class="eltio_k2">
								<a href="#" class="theme-cl">{{ __('ui_body.lost_password') }}</a>
							</div>
						</div>
					</div>

					<div class="form-group notranslate">
						<button type="submit"
							class="btn btn-md full-width theme-bg text-light fs-md ft-medium">{{ __('ui_body.login_submit') }}</button>
					</div>

				</form>
			</div>
		</div>
	</div>
</div>
<!-- End Modal -->

<a id="back2Top" class="top-scroll" title="Back to top" href="#"><i class="ti-arrow-up"></i></a>

@if(!get_cookie('is_returning'))
	<div class="cookie-consent notranslate">
		<span>{{ __('ui_body.cookie_notice') }}<a href="{{ url('privacy_policy/read') }}"
				class="ml-1 text-decoration-none text-success">{{ __('ui_body.cookie_privacy_policy') }}</a></span>
		<div class="mt-2 d-flex align-items-center justify-content-center g-2">
			<button class="allow-button mr-1" allow="1">{{ __('ui_body.cookie_allow') }}</button>
			<button class="allow-button" allow="0">{{ __('ui_body.cookie_cancel') }}</button>
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