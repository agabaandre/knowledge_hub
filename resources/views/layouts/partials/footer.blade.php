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
			@include('layouts.partials.footer_i18n_row')
		</div>
	</div>

	@include('layouts.partials.footer_i18n_bottom')
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
				@include('layouts.partials.login_i18n')
			</div>
		</div>
	</div>
</div>
<!-- End Modal -->

<a id="back2Top" class="top-scroll" title="Back to top" href="#"><i class="ti-arrow-up"></i></a>

@if(!get_cookie('is_returning'))
	@include('layouts.partials.cookie_i18n')
@endif

<script src="{{ asset('frontend/js/popper.min.js')}}"></script>
<script src="{{ asset('frontend/js/bootstrap.min.js')}}"></script>
<script src="{{ asset('frontend/js/slick.js')}}"></script>
<script src="{{ asset('frontend/js/slider-bg.js')}}"></script>
<script src="{{ asset('frontend/js/smoothproducts.js')}}"></script>
<script src="{{ asset('frontend/js/snackbar.min.js')}}"></script>
<script src="{{ asset('frontend/js/jQuery.style.switcher.js')}}"></script>
<script src="{{ asset('frontend/js/custom.js')}}"></script>
<script src="{{ asset('js/khub-content-preloader.js') }}?v={{ @filemtime(public_path('js/khub-content-preloader.js')) }}"></script>
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