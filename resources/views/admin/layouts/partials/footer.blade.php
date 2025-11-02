<!-- Footer opened -->
<div class="main-footer ht-40">
	<div class="container-fluid pd-t-0-f ht-100p">
		<span>Copyright &copy; <?php echo date('Y'); ?><a href="https://africacdc.org/">Africa CDC</a> All rights reserved.</span>
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
<!-- <script src="{{  asset('assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
<script src="{{  asset('assets/plugins/perfect-scrollbar/p-scroll.js') }}"></script> -->

<script src="{{  asset('assets/plugins/parsleyjs/parsley.min.js') }}"></script>

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
<script src="{{  asset('assets/plugins/rating/jquery.rating-stars.js') }}"></script>
<script src="{{  asset('assets/plugins/rating/jquery.barrating.js') }}"></script>

<!-- Internal Echart Plugin -->
<!-- <script src="{{  asset('assets/plugins/echart/echart.js') }}"></script> -->

<!-- Tooltip js -->
<script src="{{  asset('assets/js/tooltip.js') }}"></script>

<!-- Internal Index js -->
<!-- <script src="{{  asset('assets/js/index.js') }}" id="change-js"></script>
<script src="{{  asset('assets/js/dashboard.sampledata.js') }}"></script>
<script src="{{  asset('assets/js/chart.flot.sampledata.js') }}"></script> -->

<!-- Right-sidebar js -->
<!-- <script src="{{  asset('assets/plugins/sidebar/sidebar.js') }}"></script> -->
<script src="{{  asset('assets/plugins/sidebar/sidebar-custom.js') }}"></script>

<!-- Custom js -->
<script src="{{  asset('assets/js/custom.js') }}"></script>

<!-- Lobibox Notifications -->
<script src="https://cdn.jsdelivr.net/npm/lobibox@1.2.7/dist/js/lobibox.min.js"></script>
<script>
    window.notifyx = function(type, msg){
        try{
            Lobibox.notify(type || 'info', {
                size: 'mini',
                sound: false,
                delay: 3500,
                title: false,
                pauseDelayOnHover: true,
                position: 'top right',
                msg: msg || ''
            });
        }catch(e){
            try{ if(window.Swal){ Swal.fire(type||'info', msg||'', type||'info'); } else { alert(msg); } }catch(err){}
        }
    }
</script>


<!-- Add Select2 Nodemodules -->
<link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />
<script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>

<!-- Add Sweetalert2 Nodemodule -->
<link rel="stylesheet" href="{{ asset('assets/plugins/sweet-alert/sweetalert.css') }}"  />
<script src="{{ asset('assets/plugins/sweet-alert/sweetalert.min.js') }}"></script>


<!-- Create Quize  -->
@include('layouts.partials.language')
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
        // Initialise authors table only if present
        if ($('table#authors-table').length) {
            var authorsTable = $('table#authors-table').DataTable({
                "autoWidth": true,
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                exportOptions: {
                    columns: [0, 1, 2, 3, 4],
                }
            });
            $('#exportAuthors').on('click', function() {
                authorsTable.button(1).trigger();
            });
        }
    });

    $(document).ready(function() {
        // Initialise publications table only if present
        if ($('#publicationTable').length) {
            var table = $('#publicationTable').DataTable({
                "autoWidth": true,
                "dom": 'bootstrap',
                "buttons": [
                    'copy', 'csv', 'excel', 'pdf',
                ]
            });

            $('#filterButton').on('click', function() {
                var filterTitle = $('#filterTitle').val();
                var filterDesc = $('#filterDesc').val();
                var filterSource = $('#filterSource').val();
                table.columns(1).search(filterTitle || '').draw();
                table.columns(2).search(filterDesc || '').draw();
                table.columns(3).search(filterSource || '').draw();
            });

            $('#exportButton').on('click', function() {
                if ($('#filterTitle').val()) {
                    var filter = $('#filterTitle').val();
                    table.search(filter).draw();
                }
                table.button(1).trigger();
            });
        }
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

    // Notification Bell Auto-Refresh
    (function() {
        var refreshInterval = 60000; // Refresh every 60 seconds
        var isDropdownOpen = false;

        // Check if notification bell exists
        if (!$('#notification-bell').length) {
            return;
        }

        // Force close dropdown on page load
        function ensureDropdownClosed() {
            var $dropdown = $('#unified-notification-dropdown');
            var $menu = $('#notification-dropdown-menu');
            
            if ($dropdown.length && $menu.length) {
                // Remove show classes on page load
                $dropdown.removeClass('show');
                $menu.removeClass('show');
                
                // Hide the dropdown menu
                $menu.css({
                    'display': 'none',
                    'visibility': 'hidden',
                    'opacity': '0'
                });
                
                // Set aria-expanded to false
                $('#notification-bell').attr('aria-expanded', 'false');
            }
        }

        // Call on page load
        $(document).ready(function() {
            ensureDropdownClosed();
        });

        // Also call immediately in case DOM is already ready
        ensureDropdownClosed();

        // Fix dropdown positioning to prevent overflow
        function fixDropdownPosition() {
            var $dropdown = $('#unified-notification-dropdown');
            var $menu = $('#notification-dropdown-menu');
            
            if ($dropdown.length && $menu.length) {
                // Remove any existing event handlers to prevent duplicates
                $dropdown.off('shown.bs.dropdown');
                
                $dropdown.on('shown.bs.dropdown', function() {
                    // Ensure menu is visible when shown
                    $menu.css({
                        'display': 'flex',
                        'visibility': 'visible',
                        'opacity': '1'
                    });
                    
                    setTimeout(function() {
                        var windowWidth = $(window).width();
                        var windowScrollLeft = $(window).scrollLeft();
                        var dropdownOffset = $dropdown.offset();
                        var dropdownWidth = $dropdown.outerWidth();
                        var menuWidth = $menu.outerWidth();
                        var menuRight = dropdownOffset.left + dropdownWidth;
                        var spaceOnRight = windowWidth - menuRight + windowScrollLeft;
                        
                        // If not enough space on right, position it to fit
                        if (spaceOnRight < menuWidth) {
                            var newRight = Math.max(10, windowWidth - (dropdownOffset.left + menuWidth + 10));
                            $menu.css({
                                'right': newRight + 'px',
                                'left': 'auto',
                                'transform': 'none'
                            });
                        } else {
                            // Reset to default right alignment
                            $menu.css({
                                'right': '0',
                                'left': 'auto'
                            });
                        }
                        
                        // Ensure dropdown is visible
                        var menuLeft = $menu.offset().left;
                        if (menuLeft < 0) {
                            $menu.css('right', (Math.abs(menuLeft) + 10) + 'px');
                        }
                        if (menuLeft + menuWidth > windowWidth) {
                            var overflow = (menuLeft + menuWidth) - windowWidth;
                            var currentRight = parseInt($menu.css('right')) || 0;
                            $menu.css('right', (currentRight + overflow + 10) + 'px');
                        }
                    }, 10);
                });

                // Handle hide event to ensure proper cleanup
                $dropdown.off('hidden.bs.dropdown');
                $dropdown.on('hidden.bs.dropdown', function() {
                    $menu.css({
                        'display': 'none',
                        'visibility': 'hidden',
                        'opacity': '0'
                    });
                });
            }
        }
        
        fixDropdownPosition();

        // Function to update notification counts and badge
        function updateNotificationCounts() {
            // Don't refresh if dropdown is open
            if (isDropdownOpen) {
                return;
            }

            $.ajax({
                url: '{{ route("admin.notifications.counts") }}',
                method: 'GET',
                success: function(response) {
                    if (response.success && response.counts) {
                        var counts = response.counts;
                        var total = counts.total;

                        // Update badge
                        var badge = $('#notification-count-badge');
                        if (total > 0) {
                            if (badge.length) {
                                badge.text(total);
                            } else {
                                $('#notification-bell').append(
                                    '<span class="badge badge-danger badge-pill" id="notification-count-badge" style="position:absolute;top:-4px;right:-6px;min-width:20px;">' + total + '</span>'
                                );
                            }
                        } else {
                            badge.remove();
                        }

                        // Update breakdown
                        var breakdown = $('#notification-breakdown');
                        if (breakdown.length) {
                            if (total > 0) {
                                breakdown.html(
                                    '<div class="d-flex justify-content-between mb-1">' +
                                    '<span>Forums:</span>' +
                                    '<strong>' + counts.forums + '</strong>' +
                                    '</div>' +
                                    '<div class="d-flex justify-content-between mb-1">' +
                                    '<span>Publications:</span>' +
                                    '<strong>' + counts.publications + '</strong>' +
                                    '</div>' +
                                    '<div class="d-flex justify-content-between mb-1">' +
                                    '<span>Forum Comments:</span>' +
                                    '<strong>' + counts.forum_comments + '</strong>' +
                                    '</div>' +
                                    '<div class="d-flex justify-content-between mb-1">' +
                                    '<span>Publication Comments:</span>' +
                                    '<strong>' + counts.publication_comments + '</strong>' +
                                    '</div>' +
                                    '<div class="d-flex justify-content-between">' +
                                    '<span>COP Approvals:</span>' +
                                    '<strong>' + (counts.cop_approvals || 0) + '</strong>' +
                                    '</div>'
                                );
                            } else {
                                breakdown.html('<p class="text-muted mb-0">No pending approvals</p>');
                            }
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Error refreshing notification counts:', error);
                }
            });
        }

        // Track dropdown state
        $('#unified-notification-dropdown').on('show.bs.dropdown', function() {
            isDropdownOpen = true;
        });

        $('#unified-notification-dropdown').on('hide.bs.dropdown', function() {
            isDropdownOpen = false;
            // Refresh after dropdown closes
            setTimeout(updateNotificationCounts, 1000);
        });

        // Initial update
        updateNotificationCounts();

        // Set up interval for auto-refresh
        setInterval(updateNotificationCounts, refreshInterval);
    })();

    // Date Picker Initialization for Admin
    $(document).ready(function() {
        // Initialize jQuery UI datepicker for elements with class 'datepicker', 'date', or 'date2'
        // This will only run if jQuery UI datepicker is already loaded
        if (typeof $.fn.datepicker !== 'undefined') {
            $('.datepicker, input.date, input.date2').each(function() {
                // Check if datepicker is already initialized
                if (!$(this).hasClass('hasDatepicker')) {
                    $(this).datepicker({
                        dateFormat: 'yy-mm-dd',
                        changeMonth: true,
                        changeYear: true,
                        yearRange: '-100:+10',
                        showOtherMonths: true,
                        selectOtherMonths: true,
                        showButtonPanel: true,
                        constrainInput: true
                    });
                }
            });
        }

        // Note: HTML5 date inputs (type="date") already have native date pickers
        // They work well on modern browsers, so no additional initialization needed
    });
</script>


