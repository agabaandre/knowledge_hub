<!-- Footer opened -->
<div class="main-footer ht-40">
	<div class="container-fluid pd-t-0-f ht-100p">
		<span>Copyright &copy; <?php echo date('Y'); ?><a href="https://www.africacdc.org/">Africa CDC</a> All rights reserved.</span>
	</div>
</div>
<!-- Footer closed -->

<!-- Back-to-top -->
<a href="#top" id="back-to-top"><i class="las la-angle-double-up"></i></a>


<script src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit" type="text/javascript"></script>

<!-- Popper js -->
<script src="{{  asset('assets/plugins/popper/popper.min.js') }}"></script>

<!-- Bootstrap Bundle js -->
<script src="{{  asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

<!-- Ionicons js -->
<script src="{{  asset('assets/plugins/ionicons/ionicons.js') }}"></script>

<!-- Moment js -->
<script src="{{  asset('assets/plugins/moment/moment.js') }}"></script>

<!-- Sparkline js -->
<script src="{{  asset('assets/plugins/jquery-sparkline/jquery.sparkline.min.js') }}"></script>

<!-- Piety js -->
<script src="{{  asset('assets/plugins/peity/jquery.peity.min.js') }}"></script>

<!-- P-scroll js -->
<script src="{{  asset('assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
<script src="{{  asset('assets/plugins/perfect-scrollbar/p-scroll.js') }}"></script>

<!-- Horizontalmenu js-->
<script src="{{  asset('assets/plugins/horizontal-menu/horizontal-menu.js') }}"></script>

<!--- Colorchange js -->
<script src="{{  asset('assets/js/color-change.js') }}"></script>

<!-- Internal Flot js-->
<!-- <script src="{{  asset('assets/plugins/jquery.flot/jquery.flot.js') }}"></script>
<script src="{{  asset('assets/plugins/jquery.flot/jquery.flot.pie.js') }}"></script>
<script src="{{  asset('assets/plugins/jquery.flot/jquery.flot.resize.js') }}"></script>
<script src="{{  asset('assets/plugins/jquery.flot/jquery.flot.categories.js') }}"></script> -->

<!-- Internal Chart js-->
<script src="{{  asset('assets/plugins/chart.js/Chart.bundle.min.js') }}"></script>

<!-- Rating js-->
<!-- <script src="{{  asset('assets/plugins/rating/jquery.rating-stars.js') }}"></script>
<script src="{{  asset('assets/plugins/rating/jquery.barrating.js') }}"></script> -->

<!-- Internal Echart Plugin -->
<!-- <script src="{{  asset('assets/plugins/echart/echart.js') }}"></script> -->

<!-- Tooltip js -->
<script src="{{  asset('assets/js/tooltip.js') }}"></script>

<!-- Internal Index js -->
<!-- <script src="{{  asset('assets/js/index.js') }}" id="change-js"></script>
<script src="{{  asset('assets/js/dashboard.sampledata.js') }}"></script>
<script src="{{  asset('assets/js/chart.flot.sampledata.js') }}"></script> -->

<!-- Right-sidebar js -->
<script src="{{  asset('assets/plugins/sidebar/sidebar.js') }}"></script>
<script src="{{  asset('assets/plugins/sidebar/sidebar-custom.js') }}"></script>

<!-- Custom js -->
<script src="{{  asset('assets/js/custom.js') }}"></script>


<!-- Add Select2 Nodemodules -->
<link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />
<script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>

<!-- Add Sweetalert2 Nodemodule -->
<link rel="stylesheet" href="{{ asset('assets/plugins/sweet-alert/sweetalert.css') }}"  />
<script src="{{ asset('assets/plugins/sweet-alert/sweetalert.min.js') }}"></script>


<!-- Create Quize  -->
<script>
	//CSRF setup
	$.ajaxSetup({ headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
        });


	/*
	$('#edit-quize-modal').on('show.bs.modal', function(event) {

		console.log('Modal Opened');

		var button = $(event.relatedTarget);
		var id = button.data('id');
		var question = button.data('question');
		var answers = button.data('answers');
		var modal = $(this);
		modal.find('.modal-body #id').val(id);
		modal.find('.modal-body #question').val(question);

		var answersArray = answers.split(',');
		for (var i = 0; i < answersArray.length; i++) {

			var answer = answersArray[i].split(':');

			var id = answer[0];
			var text = answer[1];
			var is_correct = answer[2];

			// Append the answer to the respective form input
			// $("#answer" + (i + 1)).val(text);
			modal.find('.modal-body #answer' + (i + 1)).val(text);

			if (is_correct == 1) {
				// $("#correct_answer").val(i + 1);
				// Set the select otpion to the correct answer
				modal.find('.modal-body #correct_answer').val(i + 1);
			}
		}
	});

	$('#edit-tag-modal').on('show.bs.modal', function(event) {
		console.log('Modal Opened');
		var button = $(event.relatedTarget);
		var tagId = button.data('id');
		var tagText = button.data('tag');
		var modal = $(this);
		modal.find('.modal-body #tag_id').val(tagId);
		modal.find('.modal-body #tag_name').val(tagText);
	});

	$('#edit-quote-modal').on('show.bs.modal', function(event) {
		console.log('Modal Opened');
		var button = $(event.relatedTarget);
		var id = button.data('id');
		var quote = button.data('quote');

		console.log(quote);

		var modal = $(this);
		modal.find('.modal-body #id').val(id);
		// modal.find('.modal-body #quote_description').val(quote);

		$('#quote_description').summernote('code', quote);
	});

	$('#edit-author-modal').on('show.bs.modal', function(event) {
		console.log('Update Author Modal Opened');
		var button = $(event.relatedTarget);
		var authorId = button.data('id');
		var authorName = button.data('name');
		var authorEmail = button.data('email');
		var authorPhone = button.data('phone');
		var authorAddress = button.data('address');
		var modal = $(this);
		modal.find('.modal-body #id').val(authorId);
		modal.find('.modal-body #name').val(authorName);
		modal.find('.modal-body #email').val(authorEmail);
		modal.find('.modal-body #telephone').val(authorPhone);
		modal.find('.modal-body #address').val(authorAddress);
		// Is organization
		var isOrganization = button.data('isorganization');
		// isOrganization Select
		var isOrganizationSelect = modal.find('.modal-body #is_organization');
		if (isOrganization == 1) {
			isOrganizationSelect.val('1');
		} else {
			isOrganizationSelect.val('0');
		}
	});

	$(document).ready(function() {
		var authorsTable = $('table#authors-table').DataTable({
			"autoWidth": true,
			buttons: [
				'copy', 'csv', 'excel', 'pdf', 'print'
			],
			exportOptions: {
				columns: [0, 1, 2, 3, 4],

			}
		});


		var exportButton = $('#exportAuthors');

		exportButton.on('click', function() {
			authorsTable.button(1).trigger();
		});

	});

	$(document).ready(function() {
		var table = $('#publicationTable').DataTable({
			"autoWidth": true,
			"dom": 'bootstrap',
			"buttons": [
				'copy', 'csv', 'excel', 'pdf',
			]
		});


		var filterButton = $('#filterButton');

		var exportButton = $('#exportButton');

		filterButton.on('click', function() {
			var filterTitle = $('#filterTitle').val();
			var filterDesc = $('#filterDesc').val();
			var filterSource = $('#filterSource').val();

			// Apply both filters
			table.columns(1).search(filterTitle).draw();
			table.columns(2).search(filterDesc).draw();
			table.columns(3).search(filterSource).draw();
		});

		exportButton.on('click', function() {

			// If filter has value, apply filter to table and export
			if ($('#filterTitle').val()) {
				var filter = $('#filterTitle').val();
				table.search(filter).draw();
				table.button(1).trigger();
			} else {
				table.button(1).trigger();
			}
		});
	});

	// On Edit Forum Modal Shown
	$('#edit-forum-modal').on('show.bs.modal', function(event) {
		var button = $(event.relatedTarget);
		var id = button.data('id');
		var title = button.data('title');
		var desc = button.data('description');
		var modal = $(this);
		modal.find('.modal-title').text('Update Forum');
		modal.find('#id').val(id);
		modal.find('#title').val(title);
		modal.find('#description').summernote('code', desc);
	});

	$('#quote_description').summernote({
		height: 300,
	});

	// On Edit Modal Shown Event
	$('#edit-publication-modal').on('show.bs.modal', function(event) {
		var button = $(event.relatedTarget);
		var id = button.data('id');
		var title = button.data('title');
		var desc = button.data('description');


		var modal = $(this);

		modal.find('.modal-title').text('Update Publication');
		modal.find('#id').val(id);
		modal.find('#title').val(title);
		// modal.find('#publication_description').summernote({
		// 	height: 300,
		// 	toolbar: [
		// 		['style', ['style']],
		// 		['font', ['bold', 'underline', 'clear']],
		// 		['fontname', ['fontname']],
		// 		['color', ['color']],
		// 		['para', ['ul', 'ol', 'paragraph']],
		// 		['table', ['table']],
		// 		['insert', ['link', 'picture', 'video']],
		// 		['view', ['fullscreen', 'codeview', 'help']],
		// 	],
		// });

		// Set summernote content for description
		$('#publication_description').summernote('code', desc);



		$('#edit-publication-form').on('submit', function(e) {
			e.preventDefault();
			var form = $(this);
			var url = form.attr('action');

			$.ajax({
				type: 'POST',
				url: url,
				data: form.serialize(),
				success: function(response) {

					var result = JSON.parse(response);

					if (result.status == 'success') {
						$('#editModal').modal('hide');

						Swal.fire({
							icon: 'success',
							title: 'Success',
							text: 'Publication updated successfully!',
						}).then((result) => {
							if (result.value) {
								location.reload();
							}
						});
					} else {
						$('#editModal').modal('hide');
						Swal.fire({
							icon: 'error',
							title: 'Oops...',
							text: 'Publication update failed!',
						});
					}
				}
			});
		});
	});

	// Sweet Alert Delete Confirmation
	$('#deleteModal').on('show.bs.modal', function(event) {
		var button = $(event.relatedTarget);
		var id = button.data('id');
		var title = button.data('title');
		var modal = $(this);

		modal.find('.modal-title').text('Delete Publication: ' + title);
		modal.find('#delete_id').val(id);

		$('#deleteForm').on('submit', function(e) {
			e.preventDefault();
			var form = $(this);
			var url = form.attr('action');

			$.ajax({
				type: 'POST',
				url: url,
				data: form.serialize(),
				success: function(response) {
					console.log(response);
					Swal.fire({
						title: 'Success!',
						text: 'Publication Deleted Successfully!',
						type: 'success',
						confirmButtonText: 'Ok'
					}).then((result) => {
						if (result.value) {
							window.location.href = "{{ url('publications') }}";
						}
					});
				}
			});
		});
	});
	*/
</script>


