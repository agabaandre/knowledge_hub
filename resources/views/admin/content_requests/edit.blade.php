@extends(admin_layout())

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-lg-10 col-xl-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                    <h3 class="card-title mb-0">
                        <i class="fa fa-edit mr-2"></i>Edit Content Request
                    </h3>
                    <a href="{{ route('admin.content-requests.index') }}" class="btn btn-secondary btn-sm mt-2 mt-md-0">
                        <i class="fa fa-arrow-left mr-1"></i>Back to list
                    </a>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0 pl-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if ($contentRequest->isProcessed())
                        <div class="alert alert-info mb-4" style="border-radius: 0.25rem;">
                            <i class="fa fa-check-circle mr-2"></i>
                            This request was processed
                            @if ($contentRequest->processed_at)
                                on <strong>{{ $contentRequest->processed_at->format('M d, Y H:i') }}</strong>
                            @endif
                            @if ($contentRequest->processedBy)
                                by <strong>{{ $contentRequest->processedBy->name }}</strong>
                            @endif
                            . Editing below updates the core request fields only.
                        </div>
                        @if ($contentRequest->content_links || $contentRequest->admin_comments)
                            <div class="border rounded p-3 mb-4 bg-light">
                                @if ($contentRequest->content_links)
                                    <p class="mb-2"><strong>Content links (on file):</strong></p>
                                    <div class="small mb-3" style="white-space: pre-wrap;">{{ $contentRequest->content_links }}</div>
                                @endif
                                @if ($contentRequest->admin_comments)
                                    <p class="mb-2"><strong>Admin comments (on file):</strong></p>
                                    <div class="small" style="white-space: pre-wrap;">{{ $contentRequest->admin_comments }}</div>
                                @endif
                            </div>
                        @endif
                    @endif

                    <form id="content-request-edit-form" action="{{ route('admin.content-requests.update', $contentRequest->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="subject">Subject <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('subject') is-invalid @enderror"
                                   id="subject"
                                   name="subject"
                                   value="{{ old('subject', $contentRequest->subject) }}"
                                   maxlength="200"
                                   required>
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="content_request_description">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control summernote-lg @error('description') is-invalid @enderror"
                                      id="content_request_description"
                                      name="description"
                                      rows="10"
                                      required>{!! old('description', $contentRequest->description) !!}</textarea>
                            @error('description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Rich text (HTML). Use the toolbar to format; source HTML is preserved when saved.</small>
                        </div>

                        <div class="form-group">
                            <label for="country_id">Country <span class="text-danger">*</span></label>
                            <select class="form-control @error('country_id') is-invalid @enderror"
                                    id="country_id"
                                    name="country_id"
                                    required>
                                <option value="">Select country</option>
                                @foreach (\App\Models\Country::orderBy('name')->get() as $country)
                                    <option value="{{ $country->id }}"
                                        {{ (string) old('country_id', $contentRequest->country_id) === (string) $country->id ? 'selected' : '' }}>
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('country_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email">Requester email</label>
                            <input type="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   id="email"
                                   name="email"
                                   value="{{ old('email', $contentRequest->email) }}"
                                   placeholder="Optional">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Used when the request is processed and a notification is sent.</small>
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save mr-1"></i>Save changes
                            </button>
                            <a href="{{ route('admin.content-requests.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@include('partials.general.summernote')
<script>
$(document).ready(function() {
    $('#content-request-edit-form').on('submit', function() {
        var $ta = $('#content_request_description');
        if ($ta.length && $ta.data('summernote')) {
            $ta.val($ta.summernote('code'));
        }
    });
});
</script>
@endsection
