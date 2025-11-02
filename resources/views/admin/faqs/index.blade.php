@extends('admin.layouts.main')

@section('styles')
 @include('common.table')
    <link href="{{ asset('assets/plugins/summernote/dist/summernote.min.css') }}" rel="stylesheet">
    <style>
        .card { border: 1px solid #e2e8f0; border-radius: 0; margin-bottom: 1.5rem; }
        .card-header { background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 1rem 1.5rem; }
        .card-body { padding: 1.5rem; }
        .form-group { margin-bottom: 1.5rem; }
    </style>
@endsection

@section('content')
    <div class="page-header">
        <h1 class="page-title">FAQs Management</h1>
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">FAQs</li>
            </ol>
		</div>
					</div>

    <div class="row">
        <!-- Filters Card -->
					<div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="card-title mb-0">Filter FAQs</h3>
                        <small class="text-muted">Search and filter FAQs</small>
                    </div>
                    <div>
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#create-modal">
                            <i class="fa fa-plus"></i> Add FAQ
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ url('admin/faqs') }}" class="mb-3">
                        <div class="row">
                            <div class="col-md-10">
                                <div class="form-group mb-0">
                                    <input type="text" name="term" id="filterTitle" class="form-control" placeholder="Search by question..." value="{{ @$search->term ?? ''}}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="d-flex">
                                    <button type="submit" id="filterButton" class="btn btn-primary btn-sm mr-2">Filter</button>
                                    <a href="{{ url('admin/faqs') }}" class="btn btn-secondary btn-sm">Clear</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
						</div>
					</div>
				</div>

				<div class="row">
        <!-- FAQs Table Card -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">FAQs</h3>
					</div>
				</div>
                <div class="card-body">
                    @if(session('message'))
                        <div class="alert alert-{{ session('status') == 'success' ? 'success' : 'danger' }}">
                            {{ session('message') }}
		</div>
                    @endif

                    @if(count($faqs) > 0)
                        <div class="table-responsive">
                            <table id="faqsTable" class="table table-striped table-bordered table-hover" style="border-radius: 0;">
				<thead>
					<tr>
                                        <th width="60px">#</th>
						<th>Question</th>
						<th>Answer</th>
                                        <th width="150px">Actions</th>
					</tr>
				</thead>
				<tbody>
                                    @foreach($faqs as $idx => $row)
                                        <tr>
                                            <td><span class="text-muted">{{ $faqs->firstItem() + $idx }}</span></td>
                                            <td><strong>{{ clean_unicode($row->question) }}</strong></td>
                                            <td>{!! Str::limit(clean_unicode(strip_tags($row->answer)), 100) !!}</td>
                                            <td>
                                                <a href="javascript:void(0);" onclick="openEditModal({{ $row->id }})" class="btn btn-sm btn-outline-primary mr-1" title="Edit">
                                                    <i class="fa fa-edit mr-1"></i> Edit
                                                </a>
                                                <a href="javascript:void(0);" onclick="openDeleteModal('{{ $row->id }}')" class="btn btn-sm btn-outline-danger" title="Delete">
                                                    <i class="fa fa-trash"></i>
                                                </a>
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>
		</div>

                        <!-- Pagination -->
                        <div class="mt-3">
                            {{ $faqs->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <p class="text-muted">No FAQs found</p>
                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#create-modal">
                                <i class="fa fa-plus"></i> Add FAQ
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
	</div>

    <!-- Include modals -->
	@include('admin.faqs.partials.delete-modal')
	@include('admin.faqs.partials.create-modal')

    @endsection

	@section('scripts')
    <script src="{{ asset('assets/plugins/summernote/dist/summernote.min.js') }}"></script>
	<script>  
		var toDeleteRow = '';

        function deleteRow() {
			let url = `{{ url('admin/faqs/delete')}}?id=${toDeleteRow}`;

				fetch(url)
				.then(res => res.text())
				.then(res => {
                    console.log(res);
					$('#delete-modal').modal('hide');
					window.location.reload();
                });
		}

        function openDeleteModal(row = 0) {
			toDeleteRow = row;
			$('#delete-modal').modal('show');
		}

        function openEditModal(rowId) {
            // Convert to integer to ensure proper type
            rowId = parseInt(rowId);
            
            if (!rowId || isNaN(rowId)) {
                alert('Invalid FAQ ID');
                return;
            }

            console.log('Loading FAQ with ID:', rowId);
            
            // Reset form first to clear any previous data
            $('#id').val('');
            $('#question').val('');
            $('#answer').val('');
            
            // Destroy summernote if it exists
            if ($('#answer').data('summernote')) {
                $('#answer').summernote('destroy');
            }

            // Fetch FAQ data via AJAX
            $.ajax({
                url: '{{ url("admin/faqs/get") }}',
                method: 'GET',
                data: { 
                    id: rowId,
                    _t: new Date().getTime() // Cache buster
                },
                dataType: 'json',
                cache: false, // Prevent caching
                success: function(response) {
                    console.log('FAQ Response:', response);
                    
                    if (response.success && response.faq) {
                        var faq = response.faq;
                        
                        // Verify we have the correct FAQ
                        if (parseInt(faq.id) !== rowId) {
                            console.error('ID mismatch! Requested:', rowId, 'Got:', faq.id);
                            alert('Error: Loaded FAQ ID does not match requested ID');
                            return;
                        }
                        
                        console.log('Setting FAQ data - ID:', faq.id, 'Question:', faq.question);
                        
                        // Set form values
                        $('#id').val(faq.id);
                        $('#question').val(faq.question);
                        $('#faqModalLabel').text('Edit FAQ');
                        
                        // Destroy summernote if it exists
                        if ($('#answer').data('summernote')) {
                            $('#answer').summernote('destroy');
                        }
                        
                        // Store FAQ answer for later use (avoid closure issues)
                        var faqAnswer = faq.answer || '';
                        
                        // Set the raw value first
                        $('#answer').val(faqAnswer);
                        
                        // Show modal
			$('#create-modal').modal('show');
                        
                        // Wait for modal to be shown, then initialize summernote
                        setTimeout(function() {
                            // Initialize summernote
                            $('#answer').summernote({
                                height: 300,
                                toolbar: [
                                    ['style', ['style']],
                                    ['font', ['bold', 'underline', 'clear']],
                                    ['fontname', ['fontname']],
                                    ['color', ['color']],
                                    ['para', ['ul', 'ol', 'paragraph']],
                                    ['table', ['table']],
                                    ['insert', ['link', 'picture', 'video']],
                                    ['view', ['fullscreen', 'codeview', 'help']],
                                ],
                            });
                            
                            // Set content after summernote is fully initialized
                            setTimeout(function() {
                                if ($('#answer').data('summernote')) {
                                    console.log('Setting summernote content for FAQ ID:', faq.id);
                                    $('#answer').summernote('code', faqAnswer);
                                } else {
                                    console.error('Summernote not initialized!');
                                }
                            }, 300);
                        }, 500);
                    } else {
                        console.error('Invalid response:', response);
                        alert('FAQ not found');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', {
                        status: status,
                        error: error,
                        responseText: xhr.responseText,
                        statusCode: xhr.status
                    });
                    alert('An error occurred while loading FAQ data. Please try again.');
                }
            });
        }

        // Form submission handler - ensure summernote content is included
        $('#faqForm').on('submit', function(e) {
            // Get summernote content if initialized
            if ($('#answer').data('summernote')) {
                var content = $('#answer').summernote('code');
                // Update the textarea with summernote content
                $('#answer').val(content);
            }
        });

        // Initialize summernote on page load and when modal opens
        $(document).ready(function() {
            var isEditMode = false;

            // Initialize summernote when modal is shown (for create mode)
            $('#create-modal').on('shown.bs.modal', function() {
                // Only initialize if not already initialized and not in edit mode
                var hasId = $('#id').val() && $('#id').val() !== '';
                if ($('#answer').length && !$('#answer').data('summernote') && !hasId) {
                    $('#answer').summernote({
                        height: 300,
                        toolbar: [
                            ['style', ['style']],
                            ['font', ['bold', 'underline', 'clear']],
                            ['fontname', ['fontname']],
                            ['color', ['color']],
                            ['para', ['ul', 'ol', 'paragraph']],
                            ['table', ['table']],
                            ['insert', ['link', 'picture', 'video']],
                            ['view', ['fullscreen', 'codeview', 'help']],
                        ],
                    });
                }
            });

            // Reset form when modal is closed
            $('#create-modal').on('hidden.bs.modal', function() {
                // Destroy summernote instance
                if ($('#answer').data('summernote')) {
                    $('#answer').summernote('destroy');
                }
                
                $('#id').val('');
                $('#question').val('');
                $('#answer').val('');
                $('#faqModalLabel').text('Create FAQ');
                isEditMode = false;
            });

            // When clicking "Add FAQ" button, reset the modal
            $('[data-target="#create-modal"]').on('click', function() {
                // Make sure summernote is destroyed first
                if ($('#answer').data('summernote')) {
                    $('#answer').summernote('destroy');
                }
                $('#id').val('');
                $('#question').val('');
                $('#answer').val('');
                $('#faqModalLabel').text('Create FAQ');
                isEditMode = false;
            });
        });
	</script>
	@endsection
