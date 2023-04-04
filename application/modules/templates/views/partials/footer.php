	<!-- ============================ Footer Start ================================== -->
	<footer class="light-footer skin-light-footer style-2">
		<div class="footer-middle">
			<div class="container">
				<div class="row">

					<div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
						<div class="footer_widget">
							<img src="<?php echo base_url() ?>assets/images/icon_Africa_cdc.png" class="img-footer small mb-2" alt="" />

							<div class="address mt-2">
								Addis Ababa <br>Ethiopia
							</div>
							<div class="address mt-3">
								+251 11 551 7700<br>africacdc@africa-union.org
							</div>
							<div class="address mt-2">
								<ul class="list-inline">
									<li class="list-inline-item"><a href="#" class="theme-cl"><i class="lni lni-facebook-filled"></i></a></li>
									<li class="list-inline-item"><a href="#" class="theme-cl"><i class="lni lni-twitter-filled"></i></a></li>
								</ul>
							</div>
						</div>
					</div>

					<div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
						<div class="footer_widget">
							<h4 class="widget_title">Navigate</h4>
							<ul class="footer-menu">
							    <li><a href="<?php echo base_url(); ?>">Home</a></li>
								<?php if(is_guest()): ?>
								<li><a href="#login" data-bs-toggle="modal">Login</a></li>
								
								<?php else: ?>
								<li><a href="<?php echo base_url("account/publish"); ?>">Publish a resource</a></li>
								<?php endif; ?>
								<li><a href="<?php echo base_url("forums"); ?>">Discussion Forums</a></li>
								
							</ul>
						</div>
					</div>
					<div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
						<div class="footer_widget">
							<h4 class="widget_title">About This Portal</h4>
							<ul class="footer-menu">
								<li><a href="<?php echo base_url("faqs"); ?>">Frequently asked questions</a></li>
								<li><a href="https://africacdc.org" target="_blank">Africa CDC Website</a></li>
								<li><a href="<?php echo base_url('privacy_policy/read'); ?>">Privacy Policy</a></li>
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

					<?php echo form_open(base_url('account/login')); ?>

					<input type="hidden" name="route" value="front" />
					<div class="form-group">
						<label>User Name</label>
						<input type="text" name="username" class="form-control" placeholder="Username*">
					</div>

					<div class="form-group">
						<label>Password</label>
						<input type="password" name="password" class="form-control" placeholder="Password*">
					</div>

					<div class="form-group">
						<div class="d-flex align-items-center justify-content-between">
							<div class="flex-1">
								<input id="dd" class="checkbox-custom" name="dd" type="checkbox">
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

					<?php echo form_close(); ?>
				</div>
			</div>
		</div>
	</div>
	<!-- End Modal -->

	<a id="back2Top" class="top-scroll" title="Back to top" href="#"><i class="ti-arrow-up"></i></a>


	</div>

	<?php if(!get_cookie('is_returning')): ?>
	<div class="cookie-consent">

          <span>This site uses cookies to enhance user experience. see<a href="<?php echo base_url('privacy_policy/read'); ?>" class="ml-1 text-decoration-none text-success">Privacy policy</a> </span>
          <div class="mt-2 d-flex align-items-center justify-content-center g-2">
            <button class="allow-button mr-1" allow="1">Allow cookies</button>
            <button class="allow-button" allow="0">cancel</button>
          </div>
          
    </div>

	<?php endif; ?>

	<!-- ============================================================== -->
	<!-- End Wrapper -->
	<!-- ============================================================== -->

	<!-- ============================================================== -->
	<!-- All Jquery -->
	<!-- ============================================================== -->
	
	<script src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit" type="text/javascript"></script>

	<script src="<?php echo base_url(); ?>resources/js/popper.min.js"></script>
	<script src="<?php echo base_url(); ?>resources/js/bootstrap.min.js"></script>
	<script src="<?php echo base_url(); ?>resources/js/slick.js"></script>
	<script src="<?php echo base_url(); ?>resources/js/slider-bg.js"></script>
	<script src="<?php echo base_url(); ?>resources/js/smoothproducts.js"></script>
	<script src="<?php echo base_url(); ?>resources/js/snackbar.min.js"></script>
	<script src="<?php echo base_url(); ?>resources/js/jQuery.style.switcher.js"></script>
	<script src="<?php echo base_url(); ?>resources/js/custom.js"></script>
	<script src="<?php echo base_url(); ?>resources/js/quiz.js"></script>
	<!-- ============================================================== -->
	<!-- This page plugins -->
	<!-- ============================================================== -->

<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>

	    <!-- include summernote css/js -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<script src="<?php echo base_url() ?>node_modules/sweetalert2/dist/sweetalert2.all.min.js"></script>
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

	<script type="text/javascript">

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
			source: "<?php echo base_url(); ?>records/autocomplete",
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
			},3000);

			$('#summernote').summernote({
				placeholder: 'Discussion body here',
				tabsize: 2,
				height: 200
			});
		});

		//Quizz
	</script>

<script type="text/javascript">
		function googleTranslateElementInit() {
			new google.translate.TranslateElement({
				pageLanguage: 'en',
				layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
				autoDisplay: false
			}, 'google_translate_element');
		}

		function translateLanguage(lang) {
		function googleTranslateElementInit() {
  new google.translate.TranslateElement({pageLanguage: 'en', layout: google.translate.TranslateElement.InlineLayout.SIMPLE}, 'google_translate_element');
}

function translateLanguage(lang) {
			var $frame = $('.goog-te-menu-frame:first');
			if (!$frame.size()) {
				alert("Error: Could not find Google translate frame.");
				return false;
			}
			$frame.contents().find('.goog-te-menu2-item span.text:contains(' + lang + ')').get(0).click();
			}

			$(function() {
			// initialize the Google Translate API
			googleTranslateElementInit();

			// add an event listener to the language selector
			$('.language-select').on('change', function() {
				var selectedLang = $(this).val();
				translateLanguage(selectedLang);
			});
			});

		}

		$(function() {
			
			// Pick the div data as required
			var head = "" + $('.pop-head').html();
			var data = "" + $('.pop-body').html();

			$('[data-bs-toggle="popover"]').popover({
				html: true,
				title: head,
				content: data,
				placement:'bottom',
				trigger:'click'
			});
		});
	</script>

	</body>

	</html>