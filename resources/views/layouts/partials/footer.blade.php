	<!-- ============================ Footer Start ================================== -->
	<footer class="light-footer skin-light-footer style-2">
		<div class="footer-middle">
			<div class="container">
				<div class="row">

					<div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
						<div class="footer_widget">
						<h4 class="widget_title">Address</h4>
							<img src="{{ asset('assets/images/icon_Africa_cdc.png') }}" class="img-footer small mb-2" alt="" />

							<div class="address mt-2">
								Addis Ababa <br>Ethiopia
							</div>
							<div class="address mt-3">
								+251 11 551 7700<br>africacdc@africa-union.org
							</div>
							<div class="address mt-2">
					
							</div>
						</div>
					</div>

					<div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
						<div class="footer_widget">
							<h4 class="widget_title">Navigate</h4>
							<ul class="footer-menu">
							    <li><a href="{{ url('/') }}">Home</a></li>
								@guest
								<li><a href="{{ url('login') }}" >Login</a></li>
								@else
								<li><a href="{{ url('account/publish') }}">Publish a resource</a></li>
								@endguest
								<li><a href="{{ url('forums') }}">Discussion Forums</a></li>
								
							</ul>
						</div>
					</div>
					<div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
						<div class="footer_widget">
							<h4 class="widget_title">About This Portal</h4>
							<ul class="footer-menu">
								<li><a href="{{ url('faqs') }}">Frequently asked questions</a></li>
								<li><a href="https://africacdc.org" target="_blank">Africa CDC Website</a></li>
								<li><a href="{{ url('privacy') }}">Privacy Policy</a></li>
							</ul>
						</div>
					</div>
					<div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
						<div class="footer_widget">
							<h4 class="widget_title">Knowledge Hub Social Platforms </h4>
							<ul class="footer-menu">
								<li><a href="https://facebook.com"><i class="lni lni-facebook-filled"></i> Facebook</a></li>
								<li><a href="https://twitter.com" target="_blank"><i class="lni lni-twitter-filled"></i> Twitter</a></li>
								<li><a href="https://linkedin.com"><i class="lni lni-linkedin-filled"></i>LinkedIn</a></li>
								<li><a href="https://acacdemia.edu">Academia</a></li>
								<li><a href="https://www.researchgate.net/">Research Gate</a></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="footer-bottom br-top">
			<div class="container">
				<div class="row align-items-center">
					<div class="col-lg-12 col-md-12 text-center">
						<p class="mb-0">© 2022 Africa CDC.</p>
					</div>
				</div>
			</div>
		</div>
</div> 
	<!-- closes langauge translator -->
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
						<input type="email" name="email" class="form-control" placeholder="Username*" autocomplete="off">
					</div>

					<div class="form-group">
						<label>Password</label>
						<input type="password" name="password" class="form-control" placeholder="Password*" autocomplete="off">
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
						<button type="submit" class="btn btn-md full-width theme-bg text-light fs-md ft-medium">Login</button>
					</div>

                    </form>
				</div>
			</div>
		</div>
	</div>
	<!-- End Modal -->

	<a id="back2Top" class="top-scroll" title="Back to top" href="#"><i class="ti-arrow-up"></i></a>


	</div>

	@if(!get_cookie('is_returning'))
        <div class="cookie-consent">
            <span>This site uses cookies to enhance user experience. see<a href="{{ url('privacy_policy/read') }}" class="ml-1 text-decoration-none text-success">Privacy policy</a> </span>
            <div class="mt-2 d-flex align-items-center justify-content-center g-2">
                <button class="allow-button mr-1" allow="1">Allow cookies</button>
                <button class="allow-button" allow="0">cancel</button>
            </div>
        </div>
	@endif

	
	<script src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit" type="text/javascript"></script>

	<script src="{{ asset('frontend/js/popper.min.js')}}"></script>
	<script src="{{ asset('frontend/js/bootstrap.min.js')}}"></script>
	<script src="{{ asset('frontend/js/slick.js')}}"></script>
	<script src="{{ asset('frontend/js/slider-bg.js')}}"></script>
	<script src="{{ asset('frontend/js/smoothproducts.js')}}"></script>
	<script src="{{ asset('frontend/js/snackbar.min.js')}}"></script>
	<script src="{{ asset('frontend/js/jQuery.style.switcher.js')}}"></script>
	<script src="{{ asset('frontend/js/custom.js')}}"></script>
	<script src="{{ asset('assets/plugins/select2/js/select2.min.js')}}"></script>
	<!-- ============================================================== -->
	<!-- This page plugins -->
	<!-- ============================================================== -->

<script src="{{ asset('frontend/js/jquery-ui.js') }}"></script>
<script src="{{ asset('frontend/js/aos/dist/aos.js')}}"></script>

	<script type="text/javascript">

        AOS.init();

		$('.allow-button').click(function(){

			var allow = $(this).attr('allow');

			if(parseInt(allow) === 1){
				//cookie for 90days
				var date = new Date();
				date.setTime(date.getTime() + (90*24*60*60*1000));
				expires = "; expires=" + date.toUTCString();
				document.cookie = "is_returning" + "=" + "yes"  + expires + "; path=/";
			}

			$('.cookie-consent').hide();
		});


		$('.autocomplete').autocomplete({
			source: "{{ url('records/autocomplete') }}",
			minLength: 5,
			select: function(event, ui) {
				console.log(ui.item);
				$('.term').val(ui.item.label);
				$('.search-form').submit();
			}
		});

		$(document).ready(function() {

			setTimeout(function(){

				$('.alert').fadeOut('slow')
			},10000);

		});

		//Quizz
	</script>

	<script type="text/javascript">
	const fs = require('fs');
    const path = require('path');

		// Add event listener to language selector
		document.getElementById('language-selector').addEventListener('change', function() {
		const targetLanguage = this.value;

		// Check if translation file exists for the selected language
		const filePath = path.join(__dirname, `${targetLanguage}.html`);
		if (fs.existsSync(filePath)) {
			// If translation file exists, use it to update content
			const content = fs.readFileSync(filePath, 'utf-8');
			updateContent(content);
		} else {
			// If translation file does not exist, make API request to DeepL
			const content = document.getElementById('langauge-container').innerHTML;
			translateContent(content, targetLanguage, filePath);
		}
		});

		// Function to translate content using DeepL API
		function translateContent(content, targetLanguage, filePath) {
		// Make API request to DeepL
		const xhr = new XMLHttpRequest();
		xhr.open('POST', 'https://api-free.deepl.com/v2/translate');
		xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		xhr.onload = function() {
			if (xhr.status === 200) {
			// Write response to file
			const response = JSON.parse(xhr.responseText);
			const translatedContent = response.translations[0].text;
			fs.writeFile(filePath, translatedContent, function(err) {
				if (err) {
				console.log('Error writing file: ' + err);
				} else {
				console.log('File saved: ' + filePath);
				}
			});

			// Update content with translated text
			updateContent(translatedContent);
			} else {
			console.log('Error: ' + xhr.status);
			}
		};
		xhr.send('auth_key=YOUR_AUTH_KEY&text=' + encodeURIComponent(content) + '&target_lang=' + targetLanguage);
		}

		// Function to update content on the page
		function updateContent(content) {
		document.getElementById('langauge-container').innerHTML = content;
		}
	</script>

	</body>

	</html>